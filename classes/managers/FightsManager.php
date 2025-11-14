<?php
declare(strict_types=1);

class FightsManager {

    public function createMonster() {
        $monsters = [
            [ 'name' => 'Khorne', 'hp' => 1500000 ],
            [ 'name' => 'Tzeentch', 'hp' => 1500000 ],
            [ 'name' => 'Nurgle', 'hp' => 1500000 ],
            [ 'name' => 'Slaanesh', 'hp' => 1500000 ],
            [ 'name' => 'Gobelin', 'hp' => 100 ],
            [ 'name' => 'Skaven', 'hp' => 120 ],
            [ 'name' => 'Orc', 'hp' => 180 ],
            [ 'name' => 'Squig', 'hp' => 150 ],
            [ 'name' => 'Troll', 'hp' => 350 ],
            [ 'name' => 'Ogre', 'hp' => 420 ],
            [ 'name' => 'Dragon Ogre', 'hp' => 600 ],
            [ 'name' => 'Manticore', 'hp' => 550 ],
            [ 'name' => 'Hydre', 'hp' => 500 ],
            [ 'name' => 'Wyvern', 'hp' => 480 ],
            [ 'name' => 'Giant', 'hp' => 750 ],
            [ 'name' => 'Zombie', 'hp' => 90 ],
            [ 'name' => 'Skeletton', 'hp' => 100 ],
            [ 'name' => 'Banshee', 'hp' => 200 ],
            [ 'name' => 'Wraith', 'hp' => 260 ],
            [ 'name' => 'Chaos Spawn', 'hp' => 400 ],
            [ 'name' => 'Beast of Nurgle', 'hp' => 520 ],
            [ 'name' => 'Daemon', 'hp' => 220 ],
            [ 'name' => 'Bloodletter', 'hp' => 260 ],
            [ 'name' => 'Plaguebearer', 'hp' => 300 ],
            [ 'name' => 'Dragon', 'hp' => 900 ],
            [ 'name' => 'Bloodthirster', 'hp' => 1200 ],
            [ 'name' => 'Great Unclean One', 'hp' => 1500 ],
            [ 'name' => 'Carnifex', 'hp' => 1300 ],
            [ 'name' => 'Hive Tyrant', 'hp' => 1100 ],
        ];
        $data = $monsters[array_rand($monsters)];
        $name = $data['name'];
        $health_point = $data['hp'];

        return new Monster($name, $health_point);
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