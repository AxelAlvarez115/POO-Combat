<?php
declare(strict_types=1);

class FightsManager {

    public function createMonster() {

        $names = ['Gorax', 'Béhémoth', 'Ravageur', 'Ombre', 'Krog'];
        $name = $names[array_rand($names)];

        return new Monster($name, 100);
    }

    public function fight(Hero $hero, Monster $monster) {
        $log = [];

        while ($hero->isAlive() && $monster->isAlive()) {

        $log[] = $monster->hit($hero);

        if (! $hero->isAlive()) {
        $log[] = sprintf("%s est mort. Fin du combat.", $hero->getName());
        break;
        }

        $log[] = $hero->hit($monster);

        if (! $monster->isAlive()) {
        $log[] = sprintf("%s a vaincu %s !", $hero->getName(), $monster->getName());
        break;
        }
        }

        return $log;
    }
}