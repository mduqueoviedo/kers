<?php

namespace Database\Seeders;

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use Illuminate\Database\Seeder;

class KaijuSeeder extends Seeder
{
    /**
     * Seed representative kaijus for local development.
     */
    public function run(): void
    {
        $kaijus = [
            [
                'name' => 'Abyssal Maw',
                'category' => KaijuCategory::Aquatic,
                'threat_level' => 5,
                'description' => 'A deep-sea predator detected near major shipping routes.',
            ],
            [
                'name' => 'Brinehide',
                'category' => KaijuCategory::Aquatic,
                'threat_level' => 2,
                'description' => 'A coastal scavenger protected by layers of salt-encrusted armor.',
            ],
            [
                'name' => 'Cinderhorn',
                'category' => KaijuCategory::Terrestrial,
                'threat_level' => 4,
                'description' => 'A volcanic burrower that leaves superheated tunnels behind it.',
            ],
            [
                'name' => 'Cloudtalon',
                'category' => KaijuCategory::Aerial,
                'threat_level' => 3,
                'description' => 'A migratory predator that conceals itself inside storm systems.',
            ],
            [
                'name' => 'Emberfin',
                'category' => KaijuCategory::Amphibious,
                'threat_level' => 2,
                'description' => 'A heat-resistant creature observed in volcanic island lagoons.',
            ],
            [
                'name' => 'Frostveil',
                'category' => KaijuCategory::Unknown,
                'threat_level' => 3,
                'description' => null,
            ],
            [
                'name' => 'Graniteback',
                'category' => KaijuCategory::Terrestrial,
                'threat_level' => 4,
                'description' => 'A heavily armored creature that emerges along fault lines.',
            ],
            [
                'name' => 'Ironjaw',
                'category' => KaijuCategory::Terrestrial,
                'threat_level' => 5,
                'description' => 'A relentless predator capable of crushing reinforced structures.',
            ],
            [
                'name' => 'Skybreaker',
                'category' => KaijuCategory::Aerial,
                'threat_level' => 3,
                'description' => 'A high-altitude hunter capable of producing violent downdrafts.',
            ],
            [
                'name' => 'Mireclaw',
                'category' => KaijuCategory::Amphibious,
                'threat_level' => 2,
                'description' => 'A marsh-dwelling creature that moves between rivers and land.',
            ],
            [
                'name' => 'Tidecaller',
                'category' => KaijuCategory::Amphibious,
                'threat_level' => 4,
                'description' => 'A territorial giant whose movements generate destructive coastal surges.',
            ],
            [
                'name' => 'Unknown Titan',
                'category' => KaijuCategory::Unknown,
                'threat_level' => 1,
                'description' => 'An unidentified organism observed only through distant seismic readings.',
            ],
        ];

        foreach ($kaijus as $kaiju) {
            Kaiju::query()->updateOrCreate(
                ['name' => $kaiju['name']],
                $kaiju,
            );
        }
    }
}
