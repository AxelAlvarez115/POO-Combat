<?php 
require_once 'config/db.php'; 
require_once 'classes/managers/HeroesManager.php';
require_once 'classes/basics/Hero.php';
require_once 'classes/basics/Monster.php';
require_once 'classes/managers/FightsManager.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fight</title>
</head>
<body>
    <main>
        <?php if(isset($_POST['hero_id'])): ?>
            <?php 
                $heroesManager = new HeroesManager($db); 
                $hero = $heroesManager->find((int)$_POST['hero_id']);

                $fightManager = new FightsManager();
                $monster = $fightManager->createMonster();

                $log = $fightManager->fight($hero, $monster);

                $heroesManager->update($hero);

                foreach ($log as $line) {
                    echo "<p>" . htmlspecialchars($line) . "</p>";
                }
            ?>
        <?php else: ?>
            <p>Aucun héros sélectionné.</p>
        <?php endif; ?>
    </main>
</body>
</html>
