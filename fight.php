<?php

declare(strict_types=1);

require_once 'config/autoload.php';
require_once 'config/db.php';

$heroId        = isset($_POST['hero_id']) ? (int) $_POST['hero_id'] : null;
$heroesManager = new HeroesManager($db);
$fightManager  = new FightsManager();
$hero          = $heroId ? $heroesManager->find($heroId) : null;

// --- Gestion du monstre en session ---
// On crée un nouveau monstre uniquement à la première visite (sans start_fight).
// Quand le joueur lance le combat, on récupère le même monstre depuis la session.
if (isset($_POST['start_fight']) && isset($_SESSION['current_fight_monster'])) {
    $data    = $_SESSION['current_fight_monster'];
    $monster = new Monster($data['name'], $data['class'], $data['hp']);
} else {
    $monster = $fightManager->createMonster();
    $_SESSION['current_fight_monster'] = [
        'name'  => $monster->getName(),
        'class' => $monster->getClass(),
        'hp'    => $monster->getHealthPoint(),
    ];
}

// --- Résolution du combat (exécutée avant le HTML) ---
$fightResult = null;
$heroHpBefore = null;

if (isset($_POST['start_fight']) && $hero && $hero->isAlive()) {
    $heroHpBefore = $hero->getHealthPoint();
    $fightResult  = $fightManager->fight($hero, $monster);
    $heroesManager->update($hero);
    unset($_SESSION['current_fight_monster']);
}

// ── Helpers ───────────────────────────────────────────────────────────────────

/** Colorie une ligne du log selon son emoji de tête. */
function styleLogLine(string $line): string
{
    if (str_starts_with($line, '── Tour')) {
        return 'text-gray-600 text-xs text-center tracking-widest py-0.5';
    }
    if (str_contains($line, '⚡') || str_contains($line, '🔥') || str_contains($line, '🎯')
        || str_contains($line, '💥') || str_contains($line, '⚖️')) {
        return 'text-yellow-300 font-semibold';
    }
    if (str_contains($line, '🏆')) {
        return 'text-green-400 font-bold';
    }
    if (str_contains($line, '💀')) {
        return 'text-red-400 font-bold';
    }
    if (str_contains($line, '🌿')) {
        return 'text-emerald-400 font-semibold';
    }
    if (str_contains($line, '❄️') || str_contains($line, '😵')
        || str_contains($line, '💫') || str_contains($line, '☠️')) {
        return 'text-purple-300';
    }
    if (str_contains($line, '💢')) {
        return 'text-orange-400';
    }
    return 'text-gray-300 text-sm';
}

/** Nom de l'attaque spéciale selon la classe. */
function specialName(string $class): string
{
    return [
        'psyker'  => 'Surge Psionique',
        'zealot'  => 'Litanies de Haine',
        'veteran' => 'Tir de Suppression',
        'ogryn'   => 'Charge Bulldozer',
        'arbites' => 'Sentence Exécutoire',
    ][$class] ?? 'Attaque Spéciale';
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="assets/img/icons/app-icon.png" rel="icon" type="image/png">
    <title>Darktide — Combat</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Rajdhani:wght@400;500;600;700&display=swap');
        body         { font-family: 'Rajdhani', sans-serif; }
        .font-cinzel { font-family: 'Cinzel', serif; }
        .glow-red    { box-shadow: 0 0 20px rgba(220,38,38,0.45); }
        .glow-cyan   { box-shadow: 0 0 20px rgba(34,211,238,0.25); }
        .card-monster { background: linear-gradient(135deg, #1a0505 0%, #2d1515 100%); }
        .card-hero    { background: linear-gradient(135deg, #050d1a 0%, #0f2040 100%); }
        .log-container::-webkit-scrollbar       { width: 5px; }
        .log-container::-webkit-scrollbar-track { background: #111827; }
        .log-container::-webkit-scrollbar-thumb { background: #374151; border-radius: 3px; }
    </style>
</head>
<body class="bg-[url('assets/img/base/thumb-1920-1225343.jpg')] bg-cover bg-center bg-fixed min-h-screen">

    <?php include 'partials/header.php'; ?>

    <main class="flex flex-col items-center px-4 pb-12 pt-2 text-white">
    <div class="w-full max-w-3xl">

    <?php if (!$hero): ?>
        <!-- Aucun héros sélectionné -->
        <div class="bg-gray-900/95 border border-red-800 rounded-xl p-8 text-center mt-6">
            <p class="text-xl text-red-400 font-cinzel font-semibold mb-4">Aucun héros sélectionné.</p>
            <a href="index.php" class="inline-block px-6 py-2 border border-cyan-600 text-cyan-400 rounded-lg hover:bg-cyan-900/40 transition">
                ← Retour à la sélection
            </a>
        </div>

    <?php elseif (!$hero->isAlive()): ?>
        <!-- Héros mort -->
        <div class="bg-gray-900/95 border border-red-800 rounded-xl p-8 text-center mt-6 glow-red">
            <h3 class="text-2xl font-cinzel font-bold text-red-400 mb-3">— Combat Impossible —</h3>
            <p class="text-gray-300 mb-6">Ce héros est mort et ne peut plus combattre.</p>
            <a href="index.php" class="inline-block px-6 py-2 border border-cyan-600 text-cyan-400 rounded-lg hover:bg-cyan-900/40 transition">
                ← Retour à la sélection
            </a>
        </div>

    <?php elseif ($fightResult === null): ?>
        <!-- ═══════════════════════════════════════════════════════
             PHASE DE PRÉSENTATION
             ═══════════════════════════════════════════════════════ -->
        <h2 class="text-3xl font-cinzel font-black text-red-500 text-center my-6 tracking-widest">
            ⚔ SIMULATION DE COMBAT ⚔
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">

            <!-- Monstre -->
            <div class="card-monster border border-red-900 rounded-xl p-5 flex flex-col items-center glow-red">
                <span class="text-xs uppercase tracking-widest text-red-400 mb-2 font-semibold">Ennemi</span>
                <img
                    class="w-44 h-44 object-cover rounded-lg border border-red-900 mb-3 shadow-lg"
                    src="assets/img/monsters/<?= htmlspecialchars(str_replace(' ', '_', $monster->getName())) ?>.png"
                    onerror="this.onerror=null; this.src='assets/img/icons/app-icon.png';"
                    alt="<?= htmlspecialchars($monster->getName()) ?>"
                >
                <p class="text-lg font-bold text-center"><?= htmlspecialchars($monster->getName()) ?></p>
                <span class="text-xs uppercase tracking-widest text-red-300 capitalize mb-4">
                    <?= htmlspecialchars($monster->getClass()) ?>
                </span>
                <div class="w-full">
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>HP</span>
                        <span class="text-red-400 font-bold"><?= number_format($monster->getHealthPoint()) ?></span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width:100%"></div>
                    </div>
                </div>
            </div>

            <!-- Héros -->
            <div class="card-hero border border-cyan-900 rounded-xl p-5 flex flex-col items-center glow-cyan">
                <span class="text-xs uppercase tracking-widest text-cyan-400 mb-2 font-semibold">Votre Héros</span>
                <img
                    class="w-44 h-44 object-cover rounded-lg border border-cyan-900 mb-3 shadow-lg"
                    src="assets/img/heroes/<?= htmlspecialchars($hero->getClass()) ?>.webp"
                    alt="<?= htmlspecialchars($hero->getName()) ?>"
                >
                <p class="text-lg font-bold text-center"><?= htmlspecialchars($hero->getName()) ?></p>
                <span class="text-xs uppercase tracking-widest text-cyan-300 capitalize mb-4">
                    <?= htmlspecialchars($hero->getClass()) ?>
                </span>

                <!-- HP -->
                <div class="w-full mb-3">
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>HP</span>
                        <span class="text-green-400 font-bold"><?= $hero->getHealthPoint() ?> / 100</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width:<?= $hero->getHealthPoint() ?>%"></div>
                    </div>
                </div>

                <!-- Énergie -->
                <div class="w-full mb-3">
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>Énergie</span>
                        <span class="text-yellow-300 font-bold"><?= $hero->getEnergy() ?> / 100</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-2">
                        <div class="bg-yellow-400 h-2 rounded-full" style="width:<?= $hero->getEnergy() ?>%"></div>
                    </div>
                </div>

                <!-- Infos spéciale -->
                <div class="w-full bg-gray-800/60 rounded-lg p-2 text-xs text-center text-gray-400">
                    ⚡ <span class="text-yellow-300"><?= specialName($hero->getClass()) ?></span>
                    — coût <?= $hero->getSpecialCost() ?> énergie · +20 récupérés/tour
                </div>

                <?php if ($hero->getVictories() > 0): ?>
                    <div class="mt-3 text-xs text-yellow-400 font-semibold">
                        🏆 <?= $hero->getVictories() ?> victoire<?= $hero->getVictories() > 1 ? 's' : '' ?> au compteur
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bouton lancer le combat -->
        <div class="flex justify-center">
            <form method="post" action="">
                <input type="hidden" name="hero_id" value="<?= $heroId ?>">
                <button
                    type="submit"
                    name="start_fight"
                    value="1"
                    class="font-cinzel font-bold text-lg px-14 py-3 bg-red-900/50 border-2 border-red-600
                           text-red-300 rounded-xl hover:bg-red-700/60 hover:text-white transition-all
                           tracking-widest glow-red"
                >
                    ⚔️ LANCER LE COMBAT
                </button>
            </form>
        </div>

    <?php else: ?>
        <!-- ═══════════════════════════════════════════════════════
             RÉSULTATS DU COMBAT
             ═══════════════════════════════════════════════════════ -->
        <?php
        $log     = $fightResult['log'];
        $stats   = $fightResult['stats'];
        $heroWon = $hero->isAlive();
        ?>

        <h2 class="text-3xl font-cinzel font-black text-red-500 text-center my-6 tracking-widest">
            ⚔ RÉSULTAT DU COMBAT ⚔
        </h2>

        <!-- Bannière victoire / défaite -->
        <?php if ($heroWon): ?>
            <div class="border border-green-700 bg-green-950/80 rounded-xl p-5 text-center mb-5">
                <p class="text-3xl font-cinzel font-black text-green-400 mb-1">🏆 VICTOIRE</p>
                <p class="text-gray-300">
                    <?= htmlspecialchars($hero->getName()) ?> survit —
                    HP : <span class="text-red-400 font-bold"><?= $heroHpBefore ?></span>
                    → <span class="text-green-400 font-bold"><?= $hero->getHealthPoint() ?></span>
                    &nbsp;|&nbsp; Victoires : <span class="text-yellow-400 font-bold"><?= $hero->getVictories() ?></span>
                </p>
            </div>
        <?php else: ?>
            <div class="border border-red-800 bg-red-950/80 rounded-xl p-5 text-center mb-5 glow-red">
                <p class="text-3xl font-cinzel font-black text-red-400 mb-1">💀 DÉFAITE</p>
                <p class="text-gray-300">
                    <?= htmlspecialchars($hero->getName()) ?> est tombé après
                    <span class="text-white font-bold"><?= $stats['turns'] ?></span> tour<?= $stats['turns'] > 1 ? 's' : '' ?>.
                </p>
            </div>
        <?php endif; ?>

        <!-- Statistiques du combat -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
            <div class="bg-gray-900/90 border border-gray-700 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-cyan-400"><?= $stats['turns'] ?></p>
                <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Tours</p>
            </div>
            <div class="bg-gray-900/90 border border-gray-700 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-yellow-400"><?= $stats['specials'] ?></p>
                <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Spéciales</p>
            </div>
            <div class="bg-gray-900/90 border border-gray-700 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-green-400"><?= number_format($stats['dealt']) ?></p>
                <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Dmg infligés</p>
            </div>
            <div class="bg-gray-900/90 border border-gray-700 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-red-400"><?= number_format($stats['taken']) ?></p>
                <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Dmg reçus</p>
            </div>
        </div>

        <!-- Journal de combat -->
        <div class="bg-gray-950/95 border border-gray-700/60 rounded-xl p-4 mb-5">
            <h3 class="text-xs uppercase tracking-widest text-gray-500 mb-3 font-semibold">Journal de combat</h3>
            <div class="log-container overflow-y-auto max-h-96 space-y-0.5">
                <?php foreach ($log as $line): ?>
                    <p class="<?= styleLogLine($line) ?>">
                        <?= str_starts_with($line, '── Tour') ? $line : '• ' . htmlspecialchars($line) ?>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Boutons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <?php if ($heroWon): ?>
                <form method="post" action="fight.php">
                    <input type="hidden" name="hero_id" value="<?= $heroId ?>">
                    <button
                        type="submit"
                        class="w-full sm:w-auto font-cinzel font-bold px-8 py-3 border-2 border-red-700
                               text-red-300 bg-red-950/50 rounded-xl hover:bg-red-800/50 hover:text-white
                               transition-all tracking-wider"
                    >
                        ⚔️ Nouveau Combat
                    </button>
                </form>
            <?php endif; ?>
            <a href="index.php"
               class="text-center font-cinzel font-bold px-8 py-3 border border-cyan-700 text-cyan-400
                      rounded-xl hover:bg-cyan-900/40 transition tracking-wider">
                ← Retour à la Base
            </a>
        </div>

    <?php endif; ?>

    </div>
    </main>
</body>
</html>
