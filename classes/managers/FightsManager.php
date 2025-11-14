<?php
declare(strict_types=1);

class FightsManager {

    public function createMonster() {
    $monsters = [
        [ 'name' => 'Poxwalker',            'hp' => 375,  'class' => 'infantry' ],
        [ 'name' => 'Groaner',              'hp' => 300,  'class' => 'infantry' ],
        [ 'name' => 'Mobebian 21st',        'hp' => 625,  'class' => 'infantry' ],

        [ 'name' => 'Scab Bruiser',         'hp' => 815,  'class' => 'roamer' ],
        [ 'name' => 'Scab Shooter',         'hp' => 500,  'class' => 'roamer' ],
        [ 'name' => 'Scab Stalker',         'hp' => 625,  'class' => 'roamer' ],
        [ 'name' => 'Dreg Bruiser',         'hp' => 628,  'class' => 'roamer' ],
        [ 'name' => 'Dreg Stalker',         'hp' => 500,  'class' => 'roamer' ],

        [ 'name' => 'Dreg Tox Bomber',      'hp' => 1000,  'class' => 'specialist' ],
        [ 'name' => 'Dreg Tox Flamer',      'hp' => 1400,  'class' => 'specialist' ],
        [ 'name' => 'Mutant',               'hp' => 4000,  'class' => 'specialist' ],
        [ 'name' => 'Pox Hound',            'hp' => 1400,  'class' => 'specialist' ],
        [ 'name' => 'Poxburster',           'hp' => 1400,  'class' => 'specialist' ],
        [ 'name' => 'Scab Bomber',          'hp' => 750,  'class' => 'specialist' ],
        [ 'name' => 'Scab Flamer',          'hp' => 1400,  'class' => 'specialist' ],
        [ 'name' => 'Scab Sniper',          'hp' => 500,  'class' => 'specialist' ],
        [ 'name' => 'Scab Trapper',         'hp' => 900,  'class' => 'specialist' ],

        [ 'name' => 'Bulwark',              'hp' => 4800,  'class' => 'elite' ],
        [ 'name' => 'Crusher',              'hp' => 6500,  'class' => 'elite' ],
        [ 'name' => 'Dreg Gunner',          'hp' => 1400,  'class' => 'elite' ],
        [ 'name' => 'Dreg Rager',           'hp' => 2000,  'class' => 'elite' ],
        [ 'name' => 'Dreg Shotgunner',      'hp' => 1000,  'class' => 'elite' ],
        [ 'name' => 'Reaper',               'hp' => 4000,  'class' => 'elite' ],
        [ 'name' => 'Scab Gunner',          'hp' => 1700,  'class' => 'elite' ],
        [ 'name' => 'Scab Mauler',          'hp' => 3700,  'class' => 'elite' ],
        [ 'name' => 'Scab Plasma Gunner',   'hp' => 1800,  'class' => 'elite' ],
        [ 'name' => 'Scab Radio Operator',  'hp' => 2000,  'class' => 'elite' ],
        [ 'name' => 'Scab Rager',           'hp' => 2500,  'class' => 'elite' ],
        [ 'name' => 'Scab Shotgunner',      'hp' => 1500,  'class' => 'elite' ],

        [ 'name' => 'Chaos spawn',          'hp' => 47250, 'class' => 'monstrosity' ],
        [ 'name' => 'Plague Ogryn',         'hp' => 60000, 'class' => 'monstrosity' ],
        [ 'name' => 'Beast of Nurgle',      'hp' => 52500, 'class' => 'monstrosity' ],
        [ 'name' => 'Daemonhost',           'hp' => 40000, 'class' => 'monstrosity' ],

        [ 'name' => 'Scab Captain',         'hp' => 50000, 'class' => 'captain' ],
        [ 'name' => 'Rinda Karnak',         'hp' => 72000, 'class' => 'captain' ],
        [ 'name' => 'Rodin Karnak',         'hp' => 72000, 'class' => 'captain' ],
        [ 'name' => 'Admonition Champion',  'hp' => 50000, 'class' => 'captain' ],

        [ 'name' => 'Dreg Ritualist',       'hp' => 2500, 'class' => 'ritualist' ],
    ];
        $data = $monsters[array_rand($monsters)];
        $name = $data['name'];
        $class = $data['class'];
        $health_point = $data['hp'];

        return new Monster($name, $class, $health_point);
    }

    public function fight(Hero $hero, Monster $monster) {
        $log = [];

        while ($hero->isAlive() && $monster->isAlive()) {

        $log[] = $monster->hit($hero, $monster);

        if (! $hero->isAlive()) {
        $log[] = sprintf("%s est mort. Fin du combat.", $hero->getName());
        break;
        }

        $log[] = $hero->hit($monster, $hero);

        if (! $monster->isAlive()) {
        $log[] = sprintf("%s a vaincu %s !", $hero->getName(), $monster->getName());
        break;
        }
        }

        return $log;
    }
}