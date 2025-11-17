<?php 
require_once 'config/db.php'; 
require_once 'classes/managers/HeroesManager.php';
require_once 'classes/basics/Hero.php';
require_once 'classes/basics/Monster.php';
require_once 'classes/managers/FightsManager.php';

$heroId = isset($_POST['hero_id']) ? (int)$_POST['hero_id'] : null;
$monsterId = isset($_POST['monster_id']) ? (int)$_POST['monster_id'] : null;
$heroesManager = new HeroesManager($db);
$fightManager  = new FightsManager();
$hero = $heroId ? $heroesManager->find($heroId) : null;
$monster = $fightManager->createMonster();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="assets/img/icons/app-icon.png" rel="icon" type="image/png">
    <title>Darktide - Fight</title>
</head>
<body class="bg-[url('assets/img/base/thumb-1920-1225343.jpg')] bg-cover bg-center bg-fixed">
    <?php include 'partials/header.php'; ?>
    <main class="text-white min-h-screen flex flex-col items-center p-8 pt-0">
        <div class="flex flex-col bg-gray-900 p-6 mt-8 w-full max-w-3xl 
                    scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-gray-900 overflow-y-auto rounded-lg shadow-xl">
            <?php if ($hero): ?>
                <h2 class="text-3xl text-red-600 font-extrabold text-center mb-6 drop-shadow">
                    — Fight Simulation —
                </h2>
                <div class="flex flex-col items-center mb-6">
                    <?php 
                        $monsterImg = htmlspecialchars(preg_replace('/\s+/', '_', $monster->getName()));
                    ?>
                    <img 
                        class="border-2 border-gray-700 bg-red-950 w-64 h-64 object-cover rounded-lg shadow-lg mb-3" 
                        src="assets/img/monsters/<?= $monsterImg ?>.png" 
                        alt="<?= htmlspecialchars($monster->getName()); ?>"
                    >
                    <p class="text-xl font-bold"><?= htmlspecialchars($monster->getName()); ?></p>
                    <span class="text-red-400 italic"><?= htmlspecialchars($monster->getClass()); ?></span>
                    <p>HP : <span class="text-green-400 font-semibold"><?= $monster->getHealthPoint(); ?></span></p>
                </div>
                <div class="flex gap-4 bg-gray-800 w-full p-4 rounded-md shadow mb-6">
                    <img class="border-2 border-gray-700 bg-gray-900 w-50 object-cover rounded-lg shadow-lg mb-3" src="assets/img/heroes/<?= htmlspecialchars($hero->getClass()); ?>.webp" alt="">
                    <p class="text-lg">
                        <strong>Hero :</strong> 
                        <?= htmlspecialchars($hero->getName()); ?>
                        <br>
                        Class : <span class="text-blue-400"><?= htmlspecialchars($hero->getClass()); ?></span>
                        <br>
                        HP : <span class="text-green-400"><?= $hero->getHealthPoint(); ?></span>
                    </p>
                </div>
                <form method="post" action="" class="flex items-center justify-center gap-4">
                    <input type="hidden" name="hero_id" value="<?= $heroId ?>">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="form-checkbox" name="autoresolve" checked>
                        <span class="ml-2">Auto-resolve Fight</span>
                    </label>
                    <button 
                        type="submit" 
                        class="cursor-pointer hover:bg-cyan-900 text-cyan-400 font-bold py-2 px-4 border border-cyan-500 rounded">
                        Start Fight
                    </button>
                </form>
                <?php 
                    if (!$hero->isAlive()) {
                        echo "
                        <div class='text-white mt-6 p-6 bg-red-800 border border-red-600 rounded-lg text-center'>
                            <h3 class='text-xl font-bold mb-2'>— Fight Cannot Proceed —</h3>
                            <p class='mb-2'>The selected hero is dead and cannot fight.</p>
                            <button onclick=\"window.location.href='index.php'\"
                                class='cursor-pointer hover:bg-cyan-900 text-cyan-400 font-bold py-2 px-4 border border-cyan-500 rounded'>
                                Return to Hero Selection
                            </button>
                        </div>";
                        exit;
                    }
                    if (isset($_POST['autoresolve'])) {
                        echo "<div class='mt-6 bg-gray-800 p-4 rounded shadow'>";
                        $log = $fightManager->fight($hero, $monster);
                        $heroesManager->update($hero);
                        foreach ($log as $line) {
                            echo "<p class='text-sm mb-1'>• " . htmlspecialchars($line) . "</p>";
                        }
                        echo "</div>";
                    }
                ?>
            <?php else: ?>
                <p class="text-center text-xl text-red-400 font-semibold">
                    Aucun héros sélectionné.
                </p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
