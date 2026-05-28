<?php

declare(strict_types=1);

class Monster
{
    private const VALID_CLASSES = [
        'infantry', 'roamer', 'specialist', 'elite', 'monstrosity', 'captain', 'ritualist',
    ];

    /**
     * Faiblesses des classes de héros face aux classes de monstres.
     * Quand le monstre appartient à la classe faiblesse du héros → dégâts × 2.
     */
    private const HERO_WEAKNESSES = [
        'psyker'  => 'infantry',     // la masse de l'infanterie noie le psyker isolé
        'zealot'  => 'ritualist',    // les rituels chaotiques contrent la foi du Zealot
        'veteran' => 'elite',        // l'armure lourde des élites absorbe les tirs
        'ogryn'   => 'roamer',       // les rôdeurs rapides esquivent le lent Ogryn
        'arbites' => 'monstrosity',  // les monstruosités défient la loi impériale
    ];

    private ?int $id = null;
    private string $name;
    private string $class;
    private int $healthPoint;

    public function __construct(string $name, string $class, int $healthPoint = 100)
    {
        $this->name        = $name;
        $this->class       = in_array($class, self::VALID_CLASSES) ? $class : 'infantry';
        $this->healthPoint = $healthPoint;
    }

    // ── Getters / Setters ────────────────────────────────────────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): void
    {
        $this->class = in_array($class, self::VALID_CLASSES) ? $class : 'infantry';
    }

    public function getHealthPoint(): int
    {
        return $this->healthPoint;
    }

    public function setHealthPoint(int $hp): void
    {
        $this->healthPoint = max(0, $hp);
    }

    public function isAlive(): bool
    {
        return $this->healthPoint > 0;
    }

    // ── Attaque ──────────────────────────────────────────────────────────────

    /**
     * Attaque un héros.
     *
     * @param float $effectMultiplier  Multiplicateur issu des effets de statut (ex. 0.5 si gelé).
     */
    public function hit(Hero $hero, float $effectMultiplier = 1.0): string
    {
        // Dégâts de base selon la classe du monstre
        switch ($this->class) {
            case 'infantry':
                $damage = random_int(3, 5);
                break;
            case 'roamer':
                $damage = random_int(5, 8);
                break;
            case 'specialist':
                $damage = random_int(15, 20);
                break;
            case 'elite':
                $damage = random_int(17, 23);
                break;
            case 'monstrosity':
                $damage = random_int(25, 30);
                break;
            case 'captain':
                $damage = random_int(25, 30);
                break;
            case 'ritualist':
                $damage = random_int(5, 6);
                break;
            default:
                $damage = random_int(5, 20);
                break;
        }

        // Interaction de classe : le monstre inflige 2× dégâts si le héros lui est faible
        $weakness = self::HERO_WEAKNESSES[$hero->getClass()] ?? null;
        $weaknessNote = '';
        if ($weakness !== null && $this->class === $weakness) {
            $damage      *= 2;
            $weaknessNote = ' 💢 [FAIBLESSE DE CLASSE — DÉGÂTS ×2]';
        }

        // Application du multiplicateur d'effet de statut (gel, etc.)
        $damage = (int) round($damage * $effectMultiplier);

        $hero->setHealthPoint($hero->getHealthPoint() - $damage);

        return sprintf(
            '%s inflige %d dégâts à %s.%s',
            $this->name,
            $damage,
            $hero->getName(),
            $weaknessNote
        );
    }
}
