<?php

declare(strict_types=1);

class FightsManager
{
    private const MONSTERS = [
        // Infantry
        ['name' => 'Poxwalker',            'class' => 'infantry',    'hp' => 375],
        ['name' => 'Groaner',              'class' => 'infantry',    'hp' => 300],
        ['name' => 'Moebian 21st',         'class' => 'infantry',    'hp' => 625],

        // Roamers
        ['name' => 'Scab Bruiser',         'class' => 'roamer',      'hp' => 815],
        ['name' => 'Scab Shooter',         'class' => 'roamer',      'hp' => 500],
        ['name' => 'Scab Stalker',         'class' => 'roamer',      'hp' => 625],
        ['name' => 'Dreg Bruiser',         'class' => 'roamer',      'hp' => 628],
        ['name' => 'Dreg Stalker',         'class' => 'roamer',      'hp' => 500],

        // Specialists
        ['name' => 'Dreg Tox Bomber',      'class' => 'specialist',  'hp' => 1000],
        ['name' => 'Dreg Tox Flamer',      'class' => 'specialist',  'hp' => 1400],
        ['name' => 'Mutant',               'class' => 'specialist',  'hp' => 4000],
        ['name' => 'Pox Hound',            'class' => 'specialist',  'hp' => 1400],
        ['name' => 'Poxburster',           'class' => 'specialist',  'hp' => 1400],
        ['name' => 'Scab Bomber',          'class' => 'specialist',  'hp' => 750],
        ['name' => 'Scab Flamer',          'class' => 'specialist',  'hp' => 1400],
        ['name' => 'Scab Sniper',          'class' => 'specialist',  'hp' => 500],
        ['name' => 'Scab Trapper',         'class' => 'specialist',  'hp' => 900],

        // Elites
        ['name' => 'Bulwark',              'class' => 'elite',       'hp' => 4800],
        ['name' => 'Crusher',              'class' => 'elite',       'hp' => 6500],
        ['name' => 'Dreg Gunner',          'class' => 'elite',       'hp' => 1400],
        ['name' => 'Dreg Rager',           'class' => 'elite',       'hp' => 2000],
        ['name' => 'Dreg Shotgunner',      'class' => 'elite',       'hp' => 1000],
        ['name' => 'Reaper',               'class' => 'elite',       'hp' => 4000],
        ['name' => 'Scab Gunner',          'class' => 'elite',       'hp' => 1700],
        ['name' => 'Scab Mauler',          'class' => 'elite',       'hp' => 3700],
        ['name' => 'Scab Plasma Gunner',   'class' => 'elite',       'hp' => 1800],
        ['name' => 'Scab Radio Operator',  'class' => 'elite',       'hp' => 2000],
        ['name' => 'Scab Rager',           'class' => 'elite',       'hp' => 2500],
        ['name' => 'Scab Shotgunner',      'class' => 'elite',       'hp' => 1500],

        // Monstrosities
        ['name' => 'Chaos Spawn',          'class' => 'monstrosity', 'hp' => 47250],
        ['name' => 'Plague Ogryn',         'class' => 'monstrosity', 'hp' => 60000],
        ['name' => 'Beast of Nurgle',      'class' => 'monstrosity', 'hp' => 52500],
        ['name' => 'Daemonhost',           'class' => 'monstrosity', 'hp' => 40000],

        // Captains
        ['name' => 'Scab Captain',         'class' => 'captain',     'hp' => 50000],
        ['name' => 'Rinda Karnak',         'class' => 'captain',     'hp' => 72000],
        ['name' => 'Rodin Karnak',         'class' => 'captain',     'hp' => 72000],
        ['name' => 'Admonition Champion',  'class' => 'captain',     'hp' => 50000],

        // Ritualists
        ['name' => 'Dreg Ritualist',       'class' => 'ritualist',   'hp' => 2500],
    ];

    public function createMonster(): Monster
    {
        $data = self::MONSTERS[array_rand(self::MONSTERS)];

        return new Monster($data['name'], $data['class'], $data['hp']);
    }

    /**
     * Résout le combat tour par tour entre un héros et un monstre.
     *
     * Mécanique Sprint 2 :
     *  - Énergie : récupération de 20/tour, attaque spéciale déclenchée automatiquement.
     *  - Effets de statut : freeze, stun, weakness, poison.
     *  - Faiblesse de classe : ×2 dégâts sur certaines combinaisons.
     *  - Guérison partielle : +20 HP si le héros survit.
     *
     * @return array{
     *     log:   string[],
     *     stats: array{turns: int, specials: int, dealt: int, taken: int, healed: int}
     * }
     */
    public function fight(Hero $hero, Monster $monster): array
    {
        $log  = [];
        $turn = 0;

        $specialsUsed = 0;
        $dmgDealt     = 0;
        $dmgTaken     = 0;
        $healed       = 0;

        // Effets de statut actifs sur le monstre
        $status = [
            'freeze'  => 0,
            'stun'    => 0,
            'weakness' => 0,
            'poison'  => ['turns' => 0, 'damage' => 0],
        ];

        while ($hero->isAlive() && $monster->isAlive()) {
            $turn++;
            $log[] = '── Tour ' . $turn . ' ──';

            // ── Poison ──────────────────────────────────────────────────────
            if ($status['poison']['turns'] > 0) {
                $poisonDmg = $status['poison']['damage'];
                $monster->setHealthPoint($monster->getHealthPoint() - $poisonDmg);
                $dmgDealt += $poisonDmg;
                $log[] = sprintf('☠️  %s subit %s dégâts de poison.', $monster->getName(), number_format($poisonDmg));
                $status['poison']['turns']--;

                if (!$monster->isAlive()) {
                    $log[] = sprintf('🏆 %s a vaincu %s par le poison !', $hero->getName(), $monster->getName());
                    break;
                }
            }

            // ── Attaque du monstre ───────────────────────────────────────────
            if ($status['stun'] > 0) {
                $log[] = sprintf('😵 %s est étourdi et passe son tour !', $monster->getName());
                $status['stun']--;
            } else {
                $freezeMult = 1.0;
                if ($status['freeze'] > 0) {
                    $freezeMult = 0.5;
                    $log[] = sprintf('❄️  %s est ralenti par le gel (dégâts –50%%).', $monster->getName());
                    $status['freeze']--;
                }

                $heroBefore = $hero->getHealthPoint();
                $log[]      = $monster->hit($hero, $freezeMult);
                $dmgTaken  += $heroBefore - $hero->getHealthPoint();
            }

            if (!$hero->isAlive()) {
                $log[] = sprintf('💀 %s est tombé au combat après %d tours.', $hero->getName(), $turn);
                break;
            }

            // ── Attaque du héros ─────────────────────────────────────────────
            $weaknessMult = 1.0;
            if ($status['weakness'] > 0) {
                $weaknessMult = 1.3;
                $log[] = sprintf('💫 %s est affaibli (dégâts reçus +30%%).', $monster->getName());
                $status['weakness']--;
            }

            $monsterBefore = $monster->getHealthPoint();

            if ($hero->canUseSpecial()) {
                $specialsUsed++;
                $result = $hero->specialHit($monster);
                $log[]  = $result['message'];
                $dmgDealt += $monsterBefore - $monster->getHealthPoint();

                // Application de l'effet de statut
                switch ($result['effect']) {
                    case 'freeze':
                        $status['freeze'] = $result['duration'];
                        $log[] = sprintf('❄️  %s est gelé pour %d tour(s) !', $monster->getName(), $result['duration']);
                        break;
                    case 'stun':
                        $status['stun'] = $result['duration'];
                        $log[] = sprintf('😵 %s est étourdi pour %d tour(s) !', $monster->getName(), $result['duration']);
                        break;
                    case 'weakness':
                        $status['weakness'] = $result['duration'];
                        $log[] = sprintf('💫 %s est affaibli pour %d tour(s) !', $monster->getName(), $result['duration']);
                        break;
                    case 'poison':
                        $status['poison'] = ['turns' => $result['duration'], 'damage' => $result['value']];
                        $log[] = sprintf(
                            '☠️  %s est empoisonné (%s dmg × %d tours) !',
                            $monster->getName(),
                            number_format($result['value']),
                            $result['duration']
                        );
                        break;
                }
            } else {
                $log[]     = $hero->hit($monster, $weaknessMult);
                $dmgDealt += $monsterBefore - $monster->getHealthPoint();
            }

            // ── Récupération d'énergie ───────────────────────────────────────
            $hero->recoverEnergy();

            if (!$monster->isAlive()) {
                $log[] = sprintf('🏆 %s a vaincu %s !', $hero->getName(), $monster->getName());
                break;
            }
        }

        // ── Guérison partielle après victoire ────────────────────────────────
        if ($hero->isAlive()) {
            $healAmount = 20;
            $hpBefore   = $hero->getHealthPoint();
            $hero->setHealthPoint(min(100, $hpBefore + $healAmount));
            $healed = $hero->getHealthPoint() - $hpBefore;

            if ($healed > 0) {
                $log[] = sprintf(
                    '🌿 %s récupère %d HP après la victoire (%d → %d HP).',
                    $hero->getName(),
                    $healed,
                    $hpBefore,
                    $hero->getHealthPoint()
                );
            }

            $hero->incrementVictories();
        }

        return [
            'log'   => $log,
            'stats' => [
                'turns'    => $turn,
                'specials' => $specialsUsed,
                'dealt'    => $dmgDealt,
                'taken'    => $dmgTaken,
                'healed'   => $healed,
            ],
        ];
    }
}
