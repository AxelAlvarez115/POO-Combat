<?php 
require_once 'config/db.php';
require_once 'classes/managers/HeroesManager.php';
require_once 'classes/basics/Hero.php';

$heroesManager = new HeroesManager($db);

if (!empty($_POST['name']) && !empty($_POST['class'])) {
    $name = trim($_POST['name']);
    $class = trim($_POST['class']);

    if ($name !== '' && $class !== '') {
        $hero = new Hero($name, $class);
        $heroesManager->add($hero);

        header("Location: " . $_SERVER['PHP_SELF'] . "?created=1");
        exit;
    }
}
if (isset($_POST['hero_id'], $_POST['delete'])) {
    $heroId = (int) $_POST['hero_id'];
    $heroesManager->delete($heroId);

    header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1");
    exit;
}
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
<body class="bg-[url('assets/img/base/thumb-1920-1225343.jpg')] bg-cover bg-center bg-fixed">
<?php include 'partials/header.php'; ?>
<main class="text-white min-h-screen flex flex-col items-center p-8">
    <?php if (isset($_GET['created'])): ?>
        <div class="mb-4 bg-green-800 border border-green-600 p-4 rounded shadow">
            <p class="font-semibold">Hero created successfully!</p>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="mb-4 bg-red-800 border border-red-600 p-4 rounded shadow">
            <p class="font-semibold">Hero deleted.</p>
        </div>
    <?php endif; ?>
    <div class="flex flex-col w-full max-w-2xl bg-gray-900 p-6 rounded-lg shadow-xl">
        <h1 class="text-3xl text-red-600 text-center font-extrabold mb-6 drop-shadow">
            — Create Your Hero —
        </h1>
        <form method="post" action="" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="name" class="block mb-1">Hero Name</label>
                <input 
                    type="text" 
                    name="name"
                    required
                    class="w-full rounded border border-gray-700 bg-gray-800 text-white px-3 py-2 focus:ring focus:ring-red-600"
                >
            </div>
            <div class="flex-1">
                <label for="class" class="block mb-1">Hero Class</label>
                <select 
                    name="class" 
                    class="w-full cursor-pointer rounded border border-gray-700 bg-gray-800 text-white px-3 py-2 focus:ring focus:ring-red-600"
                >
                    <option value="psyker">Psyker</option>
                    <option value="zealot">Zealot</option>
                    <option value="veteran">Veteran</option>
                    <option value="ogryn">Ogryn</option>
                    <option value="arbites">Arbites</option>
                </select>
            </div>
            <div class="flex items-end">
                <button 
                    type="submit"
                    class="cursor-pointer hover:bg-cyan-900 text-cyan-400 font-bold py-2 px-4 border border-cyan-500 rounded"
                >
                    Create Hero
                </button>
            </div>

        </form>
    </div>
    <div class="bg-gray-900 p-6 mt-8 w-full max-w-3xl rounded-lg shadow-xl 
                scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-gray-900 overflow-y-auto max-h-[600px]">
        <h2 class="text-2xl font-bold mb-4">List of Heroes</h2>
        <?php 
        $heroesAlive = $heroesManager->findAllAlive();
        if (empty($heroesAlive)) {
            echo '<p class="text-gray-400">No heroes alive. Create one!</p>';
        }
        foreach ($heroesAlive as $hero): ?>
            <div class="flex items-center mb-4 p-3 border-b border-gray-700 gap-4">
                <img 
                    class="w-16 h-16 rounded object-cover shadow" 
                    src="assets/img/icons/<?= strtolower(htmlspecialchars($hero->getClass())) ?>.png" 
                    alt="<?= htmlspecialchars($hero->getName()) ?>"
                >
                <div class="flex-1">
                    <p class="text-lg font-bold"><?= htmlspecialchars($hero->getName()) ?></p>
                    <p class="text-red-400"><?= htmlspecialchars($hero->getClass()) ?></p>
                    <p>HP: 
                        <span class="text-green-400"><?= $hero->getHealthPoint() ?></span>
                    </p>
                </div>
                <form method="post" action="fight.php">
                    <input type="hidden" name="hero_id" value="<?= $hero->getId() ?>">
                    <button 
                        type="submit"
                        class="cursor-pointer hover:bg-cyan-900 text-cyan-400 font-bold py-2 px-4 border border-cyan-500 rounded">
                        Select
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
        <?php 
        $heroesDead = $heroesManager->findAllDead();
        if (!empty($heroesDead)): ?>
            <h2 class="text-2xl font-bold mt-8 mb-4">Dead Heroes</h2>
            <?php foreach ($heroesDead as $hero): ?>
                <div class="flex items-center mb-4 p-3 border-b border-gray-700 gap-4 opacity-75">
                    <img 
                        class="w-16 h-16 rounded object-cover shadow" 
                        src="assets/img/icons/<?= strtolower(htmlspecialchars($hero->getClass())) ?>.png" 
                        alt="<?= htmlspecialchars($hero->getName()) ?>"
                    >
                    <div class="flex-1">
                        <p class="text-lg font-bold"><?= htmlspecialchars($hero->getName()) ?></p>
                        <p class="text-red-400"><?= htmlspecialchars($hero->getClass()) ?></p>
                        <p>HP: <span class="text-red-500"><?= $hero->getHealthPoint() ?></span></p>
                    </div>
                    <form method="post" action="">
                        <input type="hidden" name="hero_id" value="<?= $hero->getId() ?>">
                        <input type="hidden" name="delete" value="1">
                        <button 
                            type="submit"
                            class="cursor-pointer hover:bg-red-900 text-red-400 font-bold py-2 px-4 border border-red-500 rounded">
                            Delete
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
<script>

</script>
</body>
</html>
