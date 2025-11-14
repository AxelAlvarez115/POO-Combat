<?php 
// require_once 'config/autoload.php';
require_once 'config/db.php'; 
require_once 'classes/managers/HeroesManager.php';
require_once 'classes/basics/Hero.php';
require_once 'classes/basics/Monster.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <main>
        <?php if(isset($_POST['hero_id'])): ?>
            <?php 
            $heroesManager = new HeroesManager($db); 
            $hero = $heroesManager->find((int)$_POST['hero_id']);

            $monster = new Monster('Gobelin', 100);
            while ($hero->isAlive() && $monster->isAlive()) {
                echo '<p>' . $hero->hit($monster) . '</p>';
                if ($monster->isAlive()) {
                    echo '<p>' . $monster->hit($hero) . '</p>';
                }
            }
            $heroesManager->update($hero);
            ?>
        <?php endif; ?>
    </main>
</body>
</html>