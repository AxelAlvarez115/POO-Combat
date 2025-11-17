<?php
declare(strict_types=1);

class Monster {
    private ?int $id = null;
    private string $name;
    private string $class;
    private int $health_point;

    public function __construct(string $name, string $class, int $health_point = 100) {
        $this->name = $name;
        if ($class != 'infantry' && $class != 'daemon' && $class != 'armored' && $class != 'veteran' && $class != 'ogryn' && $class != 'arbites') {
            $this->class = 'infantry';
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
        if ($class != 'infantry' && $class != 'roamer' && $class != 'specialist' && $class != 'elite' && $class != 'monstrosity' && $class != 'captain' && $class != 'ritualist') {
            $this->class = 'infantry';
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

    public function hit(Hero $hero, Monster $monster) {
        if ($monster->getClass() === 'infantry') {
            $damage = random_int(10, 30);
        } elseif ($monster->getClass() === 'roamer') {
            $damage = random_int(20, 40);
        } elseif ($monster->getClass() === 'specialist') {
            $damage = random_int(30, 60);
        } elseif ($monster->getClass() === 'elite') {
            $damage = random_int(50, 100);
        } elseif ($monster->getClass() === 'monstrosity') {
            $damage = random_int(100, 200);
        } elseif ($monster->getClass() === 'captain') {
            $damage = random_int(150, 300);
        } elseif ($monster->getClass() === 'ritualist') {
            $damage = random_int(5, 20);
        } else {
            $damage = random_int(5, 20);
        }

        $hero->setHealthPoint($hero->getHealthPoint() - $damage);
        return sprintf("%s inflige %d dégâts à %s.", $this->getName(), $damage, $hero->getName());
    }
}