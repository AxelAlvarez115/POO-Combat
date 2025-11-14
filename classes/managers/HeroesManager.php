<?php
declare(strict_types=1);
require_once 'classes/basics/Hero.php';
require_once 'config/db.php';


class HeroesManager {
    private PDO $db;


    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function add(Hero $hero) {
        $stmt = $this->db->prepare('INSERT INTO heroes (name, health_point) VALUES (:name, :health_point)');
        $stmt->execute([
        ':name' => $hero->getName(),
        ':health_point' => $hero->getHealthPoint(),
        ]);

        $id = (int)$this->db->lastInsertId();
        $hero->setId($id);
    }

    public function findAllAlive() {
        $stmt = $this->db->query('SELECT * FROM heroes WHERE health_point > 0 ORDER BY id DESC');
        $rows = $stmt->fetchAll();

        $list = [];
        foreach ($rows as $row) {
        $hero = new Hero($row['name'], (int)$row['health_point']);
        $hero->setId((int)$row['id']);
        $list[] = $hero;
        }

        return $list;
    }

    public function find(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM heroes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
        return null;
        }

        $hero = new Hero($row['name'], (int)$row['health_point']);
        $hero->setId((int)$row['id']);
        return $hero;
    }

    public function update(Hero $hero) {
        if ($hero->getId() === null) {
        throw new InvalidArgumentException('Hero id is null, cannot update');
        }

        $stmt = $this->db->prepare('UPDATE heroes SET health_point = :hp WHERE id = :id');
        $stmt->execute([
        ':hp' => $hero->getHealthPoint(),
        ':id' => $hero->getId(),
        ]);
    }

    public function exists(string $name) {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM heroes WHERE name = :name');
        $stmt->execute([':name' => $name]);
        return (int)$stmt->fetchColumn() > 0;
    }
}