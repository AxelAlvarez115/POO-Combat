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
    <title>Document</title>
</head>
<body>
    <main>
        <?php if (isset($_GET['created'])): ?>
            <p style="color: green;">Hero created successfully!</p>
        <?php endif; ?>
        <?php 
        if (isset($_POST['name'])) {
            $heroesManager = new HeroesManager($db); 
            $hero = new Hero($_POST['name']);
            $heroesManager->add($hero);

            header("Location: " . $_SERVER['PHP_SELF'] . "?created=1");
            exit;
        }
        ?>
        <form method="post" action="">
            <input type="text" name="name" placeholder="Hero name" required>
            <button type="submit">Create Hero</button>
        </form>
        <div>
            <h2>List of Heroes</h2>
            <?php 
                $heroesManager = new HeroesManager($db);
                $heroes = $heroesManager->findAllAlive();
                foreach ($heroes as $hero) {
                    echo '<p>' . htmlspecialchars($hero->getName()) . '</p>';
                    echo '<form method="post" action="fight.php">';
                    echo '<input type="hidden" name="hero_id" value="' . $hero->getId() . '">';
                    echo '<button type="submit">Select</button>';
                    echo '</form>';
                }
            ?>
        </div>
    </main>
</body>
</html>