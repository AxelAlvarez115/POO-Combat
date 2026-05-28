<?php

declare(strict_types=1);

class HeroesManager
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // ── Création ──────────────────────────────────────────────────────────────

    public function add(Hero $hero): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO heroes (name, class, health_point, victories)
             VALUES (:name, :class, :health_point, :victories)'
        );
        $stmt->execute([
            ':name'         => $hero->getName(),
            ':class'        => $hero->getClass(),
            ':health_point' => $hero->getHealthPoint(),
            ':victories'    => $hero->getVictories(),
        ]);

        $hero->setId((int) $this->db->lastInsertId());
    }

    // ── Lecture ───────────────────────────────────────────────────────────────

    public function findAllAlive(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM heroes WHERE health_point > 0 ORDER BY victories DESC, id DESC'
        );

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findAllDead(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM heroes WHERE health_point = 0 ORDER BY victories DESC, id DESC'
        );

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function find(int $id): ?Hero
    {
        $stmt = $this->db->prepare('SELECT * FROM heroes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function exists(string $name): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM heroes WHERE name = :name');
        $stmt->execute([':name' => $name]);

        return (int) $stmt->fetchColumn() > 0;
    }

    // ── Mise à jour ───────────────────────────────────────────────────────────

    public function update(Hero $hero): void
    {
        if ($hero->getId() === null) {
            throw new InvalidArgumentException('Hero id is null, cannot update.');
        }

        $stmt = $this->db->prepare(
            'UPDATE heroes SET health_point = :hp, victories = :victories WHERE id = :id'
        );
        $stmt->execute([
            ':hp'        => $hero->getHealthPoint(),
            ':victories' => $hero->getVictories(),
            ':id'        => $hero->getId(),
        ]);
    }

    // ── Suppression ───────────────────────────────────────────────────────────

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM heroes WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    // ── Hydratation ───────────────────────────────────────────────────────────

    /** Crée un objet Hero à partir d'une ligne de base de données. */
    private function hydrate(array $row): Hero
    {
        $hero = new Hero(
            (string) $row['name'],
            (string) $row['class'],
            (int) $row['health_point'],
            (int) ($row['victories'] ?? 0)
        );
        $hero->setId((int) $row['id']);

        return $hero;
    }

    /** Hydrate un tableau de lignes en objets Hero. */
    private function hydrateAll(array $rows): array
    {
        return array_map([$this, 'hydrate'], $rows);
    }
}
