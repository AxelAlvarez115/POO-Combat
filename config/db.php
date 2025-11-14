<?php
    try {
        $db = new PDO('mysql:host=localhost;dbname=poo-combat', "root", "");
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
?>