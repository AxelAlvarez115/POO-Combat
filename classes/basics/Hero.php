<?php

declare(strict_types=1);

class Hero
{
    private const VALID_CLASSES = ['psyker', 'zealot', 'veteran', 'ogryn', 'arbites'];

    /** Coût en énergie de chaque attaque spéciale */
    private const SPECIAL_COSTS = [
        'psyker'  => 40,
        'zealot'  => 45,
        'veteran' => 35,
        'ogryn'   => 30,
        'arbites' => 50,
    ];

    private const ENERGY_RECOVERY = 20;
    private const MAX_ENERGY      = 100;

    private ?int $id = null;
    private string $name;
    private string $class;
    private int $healthPoint;
    private int $energy;
    private int $victories;

    public function __construct(string $name = '', string $class = '', int $healthPoint = 100, int $victories = 0)
    {
        $this->name        = $name;
        $this->class       = in_array($class, self::VALID_CLASSES) ? $class : 'veteran';
        $this->healthPoint = $healthPoint;
        $this->energy      = self::MAX_ENERGY;
        $this->victories   = $victories;
    }

    // ── Identité ─────────────────────────────────────────────────────────────

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
        $this->class = in_array($class, self::VALID_CLASSES) ? $class : 'veteran';
    }

    // ── Santé ─────────────────────────────────────────────────────────────────

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

    // ── Énergie ──────────────────────────────────────────────────────────────

    public function getEnergy(): int
    {
        return $this->energy;
    }

    public function setEnergy(int $energy): void
    {
        $this->energy = max(0, min(self::MAX_ENERGY, $energy));
    }

    /** Récupération automatique partielle d'énergie à chaque tour. */
    public function recoverEnergy(): void
    {
        $this->energy = min(self::MAX_ENERGY, $this->energy + self::ENERGY_RECOVERY);
    }

    public function getSpecialCost(): int
    {
        return self::SPECIAL_COSTS[$this->class] ?? 30;
    }

    public function canUseSpecial(): bool
    {
        return $this->energy >= $this->getSpecialCost();
    }

    // ── Victoires ─────────────────────────────────────────────────────────────

    public function getVictories(): int
    {
        return $this->victories;
    }

    public function setVictories(int $victories): void
    {
        $this->victories = max(0, $victories);
    }

    public function incrementVictories(): void
    {
        $this->victories++;
    }

    // ── Attaque normale ──────────────────────────────────────────────────────

    /**
     * Attaque un monstre avec l'attaque de base.
     *
     * @param float $multiplier Multiplicateur de dégâts (ex. 1.3 si le monstre est affaibli).
     */
    public function hit(Monster $monster, float $multiplier = 1.0): string
    {
        $critMessage = '';

        switch ($this->class) {
            case 'psyker':
                $damage = random_int(500, 1000);
                if (in_array($monster->getClass(), ['monstrosity', 'captain', 'ritualist'])) {
                    $damage     *= 8;
                    $critMessage = ' ⚡ [FRAPPE PSIONIQUE]';
                }
                break;

            case 'zealot':
                $damage = random_int(100, 700);
                if ($monster->getClass() === 'monstrosity') {
                    $damage     *= 10;
                    $critMessage = ' 🔥 [FRÉNÉSIE MARTIALE]';
                }
                break;

            case 'veteran':
                $damage = random_int(600, 700);
                if (in_array($monster->getClass(), ['elite', 'specialist'])) {
                    $damage     *= 5;
                    $critMessage = ' 🎯 [TIR DE PRÉCISION]';
                }
                break;

            case 'ogryn':
                $damage = random_int(300, 400);
                break;

            case 'arbites':
                $damage = random_int(700, 800);
                if ($monster->getClass() === 'captain') {
                    $damage     *= 3;
                    $critMessage = ' ⚖️ [EXÉCUTION JUDICIAIRE]';
                }
                break;

            default:
                $damage = random_int(5, 25);
                break;
        }

        $damage = (int) round($damage * $multiplier);
        $monster->setHealthPoint($monster->getHealthPoint() - $damage);

        return sprintf(
            '%s inflige %d dégâts à %s.%s',
            $this->name,
            $damage,
            $monster->getName(),
            $critMessage
        );
    }

    // ── Attaque spéciale ─────────────────────────────────────────────────────

    /**
     * Déclenche l'attaque spéciale du héros (consomme de l'énergie).
     *
     * @return array{message: string, effect: string, duration: int, value: int}
     */
    public function specialHit(Monster $monster): array
    {
        $cost         = $this->getSpecialCost();
        $this->energy = max(0, $this->energy - $cost);

        $effect   = 'none';
        $duration = 0;
        $value    = 0;

        switch ($this->class) {
            case 'psyker':
                $damage = random_int(2000, 5000);
                if (in_array($monster->getClass(), ['monstrosity', 'captain', 'ritualist'])) {
                    $damage *= 3;
                }
                $effect   = 'freeze';
                $duration = 2;
                $message  = sprintf(
                    '⚡ [SURGE PSIONIQUE] %s lâche une décharge warp → %s dégâts sur %s !',
                    $this->name,
                    number_format($damage),
                    $monster->getName()
                );
                break;

            case 'zealot':
                $damage = random_int(3000, 8000);
                if ($monster->getClass() === 'monstrosity') {
                    $damage *= 2;
                }
                $effect   = 'weakness';
                $duration = 3;
                $message  = sprintf(
                    '🔥 [LITANIES DE HAINE] %s s\'embrase de fureur → %s dégâts sur %s !',
                    $this->name,
                    number_format($damage),
                    $monster->getName()
                );
                break;

            case 'veteran':
                $damage = random_int(600, 700) + random_int(600, 700) + random_int(600, 700);
                if (in_array($monster->getClass(), ['elite', 'specialist'])) {
                    $damage = (int) round($damage * 1.5);
                }
                $message = sprintf(
                    '🎯 [TIR DE SUPPRESSION] %s lâche une salve de 3 coups → %s dégâts sur %s !',
                    $this->name,
                    number_format($damage),
                    $monster->getName()
                );
                break;

            case 'ogryn':
                $damage   = random_int(1000, 1500);
                $effect   = 'stun';
                $duration = 1;
                $message  = sprintf(
                    '💥 [CHARGE BULLDOZER] %s percute %s → %s dégâts !',
                    $this->name,
                    $monster->getName(),
                    number_format($damage)
                );
                break;

            case 'arbites':
                $damage = random_int(2000, 4000);
                if ($monster->getClass() === 'captain') {
                    $damage *= 2;
                }
                $effect   = 'poison';
                $duration = 3;
                $value    = 500;
                $message  = sprintf(
                    '⚖️ [SENTENCE EXÉCUTOIRE] %s prononce le jugement → %s dégâts sur %s !',
                    $this->name,
                    number_format($damage),
                    $monster->getName()
                );
                break;

            default:
                $damage  = random_int(500, 1000);
                $message = sprintf('%s attaque spécialement pour %s dégâts !', $this->name, number_format($damage));
                break;
        }

        $monster->setHealthPoint($monster->getHealthPoint() - $damage);

        return [
            'message'  => $message,
            'effect'   => $effect,
            'duration' => $duration,
            'value'    => $value,
        ];
    }
}
