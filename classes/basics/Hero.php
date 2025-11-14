<?php
declare(strict_types=1);

class Hero {
    private ?int $id = null;
    private string $name;
    private string $class;
    private int $health_point;

    public function __construct(string $name = '', string $class = '', int $health_point = 100) {
    $this->name = $name;
    if ($class != 'psyker' && $class != 'zealot' && $class != 'veteran' && $class != 'ogryn' && $class != 'arbites') {
        $this->class = 'veteran';
    } else {
        $this->class = $class;
    }
    $this->health_point = $health_point;
    }

    public function getId() {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function getClass() {
        return $this->class;
    }

    public function setClass(string $class) {
        if ($class != 'psyker' && $class != 'zealot' && $class != 'veteran' && $class != 'ogryn' && $class != 'arbites') {
            $this->class = 'veteran';
        } else {
            $this->class = $class;
        }
    }

    public function getHealthPoint() {
        return $this->health_point;
    }

    public function setHealthPoint(int $hp) {
    $this->health_point = max(0, $hp);
    }

    public function isAlive() {
        return $this->health_point > 0;
    }

    public function hit(Monster $monster, Hero $hero) {
        if ($hero->getClass() === 'psyker') {
            $damage = random_int(500, 1000);
            if (in_array($monster->getClass(), ['monstrosity', 'captain', 'ritualist'])) {
                echo "Critical hit!";
                $damage *= 8;
            }

        } elseif ($hero->getClass() === 'zealot') {
            $damage = random_int(100, 700);
            if ($monster->getClass() === 'monstrosity') {
                $damage *= 10;
            }

        } elseif ($hero->getClass() === 'veteran') {
            $damage = random_int(600, 700);
            if (in_array($monster->getClass(), ['elite', 'specialist'])) {
                $damage *= 5;
            }

        } elseif ($hero->getClass() === 'ogryn') {
            $damage = random_int(300, 400);

        } elseif ($hero->getClass() === 'arbites') {
            $damage = random_int(700, 800);
            if ($monster->getClass() === 'captain') {
                $damage *= 3;
            }

        } else {
            $damage = random_int(5, 25);
        }

        $monster->setHealthPoint($monster->getHealthPoint() - $damage);
        return sprintf("%s inflige %d dégâts à %s.", $this->getName(), $damage, $monster->getName());
    }

}