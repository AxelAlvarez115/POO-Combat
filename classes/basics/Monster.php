<?php
declare(strict_types=1);

class Monster {
    private ?int $id = null;
    private string $name;
    private int $health_point;

    public function __construct(string $name = '', int $health_point = 100) {
        $this->name = $name;
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

    public function getHealthPoint() {
        return $this->health_point;
    }

    public function setHealthPoint(int $hp) {
        $this->health_point = max(0, $hp);
    }

    public function isAlive() {
        return $this->health_point > 0;
    }

    public function hit(Hero $hero) {

    $damage = random_int(5, 20);
    $hero->setHealthPoint($hero->getHealthPoint() - $damage);
    return sprintf("%s inflige %d dégâts à %s.", $this->getName(), $damage, $hero->getName());
    }
}