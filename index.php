<?php

declare(strict_types=1);

require_once 'config/autoload.php';
require_once 'config/db.php';

$heroesManager = new HeroesManager($db);
$error         = null;

// --- Suppression d'un héros mort ---
if (isset($_POST['hero_id'], $_POST['delete'])) {
    $heroesManager->delete((int) $_POST['hero_id']);
    header('Location: ' . $_SERVER['PHP_SELF'] . '?deleted=1');
    exit;
}

// --- Création d'un héros ---
if (!empty($_POST['name']) && !empty($_POST['class'])) {
    $name  = trim($_POST['name']);
    $class = trim($_POST['class']);

    if ($heroesManager->exists($name)) {
        $error = 'Un héros nommé "' . htmlspecialchars($name) . '" existe déjà. Choisissez un autre nom.';
    } else {
        $hero = new Hero($name, $class);
        $heroesManager->add($hero);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?created=1');
        exit;
    }
}

// ── Données ────────────────────────────────────────────────────────────────
$heroesAlive = $heroesManager->findAllAlive();
$heroesDead  = $heroesManager->findAllDead();

$classLabels = [
    'psyker'  => 'Psyker',
    'zealot'  => 'Zealot',
    'veteran' => 'Veteran',
    'ogryn'   => 'Ogryn',
    'arbites' => 'Arbites',
];

$classAccents = [
    'psyker'  => ['ring' => 'border-purple-700', 'text' => 'text-purple-400', 'bar' => 'bg-purple-500'],
    'zealot'  => ['ring' => 'border-orange-700', 'text' => 'text-orange-400', 'bar' => 'bg-orange-500'],
    'veteran' => ['ring' => 'border-green-700',  'text' => 'text-green-400',  'bar' => 'bg-green-500'],
    'ogryn'   => ['ring' => 'border-yellow-700', 'text' => 'text-yellow-400', 'bar' => 'bg-yellow-500'],
    'arbites' => ['ring' => 'border-blue-700',   'text' => 'text-blue-400',   'bar' => 'bg-blue-500'],
];

$specialNames = [
    'psyker'  => 'Surge Psionique',
    'zealot'  => 'Litanies de Haine',
    'veteran' => 'Tir de Suppression',
    'ogryn'   => 'Charge Bulldozer',
    'arbites' => 'Sentence Exécutoire',
];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="assets/img/icons/app-icon.png" rel="icon" type="image/png">
    <title>Darktide</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Rajdhani:wght@400;500;600;700&display=swap');
        body         { font-family: 'Rajdhani', sans-serif; }
        .font-cinzel { font-family: 'Cinzel', serif; }
        .hero-card   {
            background: linear-gradient(145deg, #060d1a 0%, #0c1a30 100%);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hero-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(34,211,238,0.18);
        }
        .dead-card { background: linear-gradient(145deg, #0d0606 0%, #1a0c0c 100%); }
        .form-card  { background: linear-gradient(145deg, #080810 0%, #0f1624 100%); }
        input, select { font-family: 'Rajdhani', sans-serif; font-size: 1rem; }
    </style>
</head>
<body class="bg-[url('assets/img/base/thumb-1920-1225343.jpg')] bg-cover bg-center bg-fixed min-h-screen">

<?php include 'partials/header.php'; ?>

<main class="flex flex-col items-center px-4 pb-12 pt-6 text-white">

    <!-- ── Notifications ──────────────────────────────────────────────── -->
    <?php if (isset($_GET['created'])): ?>
        <div class="mb-5 w-full max-w-2xl flex items-center gap-3 bg-green-950/90 border border-green-700 px-5 py-3 rounded-xl">
            <span class="text-xl">✅</span>
            <p class="font-semibold text-green-300">Soldat enrôlé dans les rangs de l'Inquisition !</p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="mb-5 w-full max-w-2xl flex items-center gap-3 bg-red-950/90 border border-red-800 px-5 py-3 rounded-xl">
            <span class="text-xl">🗑️</span>
            <p class="font-semibold text-red-300">Héros retiré du registre impérial.</p>
        </div>
    <?php endif; ?>

    <?php if ($error !== null): ?>
        <div class="mb-5 w-full max-w-2xl flex items-center gap-3 bg-yellow-950/90 border border-yellow-700 px-5 py-3 rounded-xl">
            <span class="text-xl">⚠️</span>
            <p class="font-semibold text-yellow-300"><?= $error ?></p>
        </div>
    <?php endif; ?>

    <!-- ── Formulaire de création ──────────────────────────────────────── -->
    <section class="form-card border border-gray-700/50 w-full max-w-2xl rounded-xl p-7 mb-10 shadow-2xl">
        <h1 class="font-cinzel text-2xl font-bold text-red-500 text-center mb-6 tracking-widest uppercase">
            — Enrôlement —
        </h1>
        <form method="post" action="" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="name" class="block text-xs uppercase tracking-widest text-gray-400 mb-1.5">
                    Nom du soldat
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    required
                    placeholder="Ex : Atoma Prime"
                    class="w-full rounded-lg border border-gray-600 bg-gray-900/80 text-white px-4 py-2.5
                           focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 placeholder-gray-600"
                >
            </div>
            <div class="flex-1">
                <label for="class" class="block text-xs uppercase tracking-widest text-gray-400 mb-1.5">
                    Classe
                </label>
                <select
                    name="class"
                    id="class"
                    class="w-full rounded-lg border border-gray-600 bg-gray-900/80 text-white px-4 py-2.5
                           focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 cursor-pointer"
                >
                    <option value="psyker">Psyker — Surge Psionique</option>
                    <option value="zealot">Zealot — Litanies de Haine</option>
                    <option value="veteran">Veteran — Tir de Suppression</option>
                    <option value="ogryn">Ogryn — Charge Bulldozer</option>
                    <option value="arbites">Arbites — Sentence Exécutoire</option>
                </select>
            </div>
            <div class="flex items-end">
                <button
                    type="submit"
                    class="w-full sm:w-auto font-cinzel font-bold px-6 py-2.5 bg-red-900/50 border border-red-600
                           text-red-300 rounded-lg hover:bg-red-700/60 hover:text-white transition-all tracking-wider
                           whitespace-nowrap"
                >
                    Enrôler
                </button>
            </div>
        </form>
    </section>

    <!-- ── Héros vivants ───────────────────────────────────────────────── -->
    <section class="w-full max-w-4xl">
        <h2 class="font-cinzel text-lg font-bold text-gray-300 mb-5 tracking-widest flex items-center gap-3 uppercase">
            <span class="block h-px flex-1 bg-gradient-to-r from-transparent to-red-800"></span>
            Soldats Disponibles
            <span class="block h-px flex-1 bg-gradient-to-l from-transparent to-red-800"></span>
        </h2>

        <?php if (empty($heroesAlive)): ?>
            <div class="border border-dashed border-gray-700 rounded-xl p-10 text-center text-gray-500 mb-10">
                <p class="text-lg mb-1">Aucun soldat en vie.</p>
                <p class="text-sm">Enrôlez votre premier héros ci-dessus.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
                <?php foreach ($heroesAlive as $hero):
                    $cls     = $hero->getClass();
                    $accent  = $classAccents[$cls] ?? $classAccents['veteran'];
                    $hpPct   = $hero->getHealthPoint();
                ?>
                    <div class="hero-card border <?= $accent['ring'] ?>/40 rounded-xl p-4 flex flex-col gap-3">

                        <!-- En-tête de la carte -->
                        <div class="flex items-center gap-3">
                            <img
                                class="w-14 h-14 rounded-lg object-cover border <?= $accent['ring'] ?>/60 shadow"
                                src="assets/img/icons/<?= strtolower(htmlspecialchars($cls)) ?>.png"
                                alt="<?= htmlspecialchars($cls) ?>"
                            >
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-white truncate text-base">
                                    <?= htmlspecialchars($hero->getName()) ?>
                                </p>
                                <span class="text-xs uppercase tracking-widest font-semibold <?= $accent['text'] ?>">
                                    <?= htmlspecialchars($classLabels[$cls] ?? $cls) ?>
                                </span>
                            </div>
                            <?php if ($hero->getVictories() > 0): ?>
                                <span class="shrink-0 text-xs bg-yellow-900/50 border border-yellow-700/60
                                             text-yellow-400 px-2 py-0.5 rounded-full font-semibold">
                                    🏆 <?= $hero->getVictories() ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Barre HP -->
                        <div>
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>HP</span>
                                <span class="<?= $accent['text'] ?> font-bold">
                                    <?= $hero->getHealthPoint() ?> / 100
                                </span>
                            </div>
                            <div class="w-full bg-gray-800 rounded-full h-1.5">
                                <div class="<?= $accent['bar'] ?> h-1.5 rounded-full transition-all"
                                     style="width:<?= $hpPct ?>%"></div>
                            </div>
                        </div>

                        <!-- Attaque spéciale -->
                        <p class="text-xs text-gray-500">
                            ⚡ <?= htmlspecialchars($specialNames[$cls] ?? '—') ?>
                        </p>

                        <!-- Bouton -->
                        <form method="post" action="fight.php" class="mt-auto">
                            <input type="hidden" name="hero_id" value="<?= $hero->getId() ?>">
                            <button
                                type="submit"
                                class="w-full font-cinzel text-sm font-bold py-2 px-4 border <?= $accent['ring'] ?>
                                       <?= $accent['text'] ?> rounded-lg hover:bg-cyan-900/30
                                       transition-all duration-200 tracking-wider"
                            >
                                Au Combat →
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- ── Héros tombés ──────────────────────────────────────────── -->
        <?php if (!empty($heroesDead)): ?>
            <h2 class="font-cinzel text-lg font-bold text-gray-600 mb-5 tracking-widest flex items-center gap-3 uppercase">
                <span class="block h-px flex-1 bg-gradient-to-r from-transparent to-red-950"></span>
                Soldats Tombés
                <span class="block h-px flex-1 bg-gradient-to-l from-transparent to-red-950"></span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($heroesDead as $hero):
                    $cls = $hero->getClass();
                ?>
                    <div class="dead-card border border-red-950/40 rounded-xl p-4 flex flex-col gap-3 opacity-55">
                        <div class="flex items-center gap-3">
                            <img
                                class="w-14 h-14 rounded-lg object-cover border border-gray-800 shadow grayscale"
                                src="assets/img/icons/<?= strtolower(htmlspecialchars($cls)) ?>.png"
                                alt="<?= htmlspecialchars($cls) ?>"
                            >
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-500 line-through truncate">
                                    <?= htmlspecialchars($hero->getName()) ?>
                                </p>
                                <span class="text-xs uppercase tracking-widest text-gray-600">
                                    <?= htmlspecialchars($classLabels[$cls] ?? $cls) ?>
                                </span>
                            </div>
                            <?php if ($hero->getVictories() > 0): ?>
                                <span class="shrink-0 text-xs text-gray-600 border border-gray-700
                                             px-2 py-0.5 rounded-full">
                                    🏆 <?= $hero->getVictories() ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>HP</span>
                                <span class="text-red-900 font-bold">0 / 100</span>
                            </div>
                            <div class="w-full bg-gray-900 rounded-full h-1.5">
                                <div class="bg-red-950 h-1.5 rounded-full" style="width:0%"></div>
                            </div>
                        </div>

                        <form method="post" action="" class="mt-auto">
                            <input type="hidden" name="hero_id" value="<?= $hero->getId() ?>">
                            <input type="hidden" name="delete" value="1">
                            <button
                                type="submit"
                                class="w-full text-sm font-bold py-2 px-4 border border-red-900/60
                                       text-red-700 rounded-lg hover:bg-red-950/60 transition-all"
                            >
                                Retirer du registre
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</main>
</body>
</html>
