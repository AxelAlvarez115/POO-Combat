<?php 
// require_once 'config/autoload.php';
require_once 'config/db.php';
require_once 'classes/managers/HeroesManager.php';
require_once 'classes/basics/Hero.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="assets/img/icons/app-icon.png" rel="icon" type="image/png">
    <title>Darktide</title>
</head>
<body class="bg-[url('assets/img/base/thumb-1920-1225343.jpg')]">
    <?php include 'partials/header.php'; ?>
    <main class=" text-white min-h-screen flex flex-col items-center p-8">
        <!-- <?php if (isset($_GET['created'])): ?>
            <p style="color: green;">Hero created successfully!</p>
        <?php endif; ?> -->
        <?php 
        if (isset($_POST['name']) && isset($_POST['class'])) {
            $heroesManager = new HeroesManager($db); 
            $hero = new Hero($_POST['name'], $_POST['class']);
            $heroesManager->add($hero);

            header("Location: " . $_SERVER['PHP_SELF'] . "?created=1");
            exit;
        }
        if(isset($_POST['hero_id']) && isset($_POST['delete'])) {
            $hero_id = (int)$_POST['hero_id'];
            $heroesManager = new HeroesManager($db);
            $heroesManager->delete($hero_id);

            header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1");
            exit;
        }
        ?>
        <div class="flex flex-col">
            <h1 class="text-3xl text-red-900 text-center font-bold mb-4">- Create Your Hero -</h1>
                <form class="flex gap-2 bg-gray-900 p-4" method="post" action="">
                <div>
                    <label class="block mb-1" for="name">Hero Name</label>
                    <input class="rounded border border-gray-700 bg-gray-800 text-white px-2 py-1" type="text" name="name" placeholder="" required>
                </div>
                <div>
                    <label class="block mb-1" for="class">Hero Class</label>
                    <select name="class" class="cursor-pointer rounded border border-gray-700 bg-gray-800 text-white px-2 py-1" id="">
                        <option class="cursor-pointer" value="psyker">Psyker</option>
                        <option class="cursor-pointer" value="zealot">Zealot</option>
                        <option class="cursor-pointer" value="veteran">Veteran</option>
                        <option class="cursor-pointer" value="ogryn">Ogryn</option>
                        <option class="cursor-pointer" value="arbites">Arbites</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="cursor-pointer hover:bg-cyan-900 text-cyan-600 font-bold py-1 px-2 border border-cyan-600" type="submit">Create Hero</button>
                </div>
            </form>
        </div>
        <div class="bg-gray-900 p-4 mt-8 w-full scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-gray-900 max-h-140 overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">List of Heroes</h2>
            <?php 
                $heroesManager = new HeroesManager($db);
                $heroesAlive = $heroesManager->findAllAlive();
                if (count($heroesAlive) === 0) {
                    echo '<p>No heroes alive. Create one!</p>';
                }
                foreach ($heroesAlive as $hero) {
                    echo '<div class="flex mb-2 p-2 border-b border-gray-700 gap-2">';
                    echo '<img class="w-16 h-16 object-cover rounded" src="assets/img/icons/' . strtolower(htmlspecialchars($hero->getClass())) . '.png" alt="' . htmlspecialchars($hero->getName()) . '">';
                    echo '<p class="text-lg font-bold">' . htmlspecialchars($hero->getName()) . '</p>';
                    echo '<p class="text-red-500">' . htmlspecialchars($hero->getClass()) . '</p>';
                    echo '<p class="flex-1">HP: ' . htmlspecialchars((string)$hero->getHealthPoint()) . '</p>';
                    echo '<form method="post" action="fight.php">';
                    echo '<input type="hidden" name="hero_id" value="' . $hero->getId() . '">';
                    echo '<button class="cursor-pointer hover:bg-cyan-900 text-cyan-600 font-bold py-1 px-2 border border-cyan-600" type="submit">Select</button>';
                    echo '</form>';
                    echo '</div>';
                }
                $heroesDead = $heroesManager->findAllDead();
                if (count($heroesDead) > 0) {
                    echo '<h2 class="text-xl font-bold mt-8 mb-4">Dead Heroes</h2>';
                    foreach ($heroesDead as $hero) {
                    echo '<div class="flex mb-2 p-2 border-b border-gray-700 gap-2">';
                    echo '<img class="w-16 h-16 object-cover rounded" src="assets/img/icons/' . strtolower(htmlspecialchars($hero->getClass())) . '.png" alt="' . htmlspecialchars($hero->getName()) . '">';
                    echo '<p class="text-lg font-bold">' . htmlspecialchars($hero->getName()) . '</p>';
                    echo '<p class="text-red-500">' . htmlspecialchars($hero->getClass()) . '</p>';
                    echo '<p class="flex-1">HP: ' . htmlspecialchars((string)$hero->getHealthPoint()) . '</p>';
                    echo '<form method="post" action="">';
                    echo '<input type="hidden" name="hero_id" value="' . $hero->getId() . '">';
                    echo '<input type="hidden" name="delete" value="1">';
                    echo '<button class="cursor-pointer hover:bg-cyan-900 text-red-600 font-bold py-1 px-2 border border-red-600" type="submit">Delete</button>';
                    echo '</form>';
                    echo '</div>';
                    }
                }
            ?>
        </div>
    </main>
</body>
</html>