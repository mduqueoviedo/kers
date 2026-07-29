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
                'name' => 'Graniteback',
                'category' => KaijuCategory::Terrestrial,
                'threat_level' => 4,
                'description' => 'A heavily armored creature that emerges along fault lines.',
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
