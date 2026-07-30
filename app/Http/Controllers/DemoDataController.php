<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Kaiju;
use Database\Seeders\IncidentSeeder;
use Database\Seeders\KaijuSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DemoDataController extends Controller
{
    /**
     * Restore the canonical Kaiju and Incident demo data without changing users.
     */
    public function reset(): JsonResponse
    {
        $records = DB::transaction(function (): array {
            Kaiju::query()->delete();

            Artisan::call('db:seed', [
                '--class' => KaijuSeeder::class,
                '--force' => true,
            ]);
            Artisan::call('db:seed', [
                '--class' => IncidentSeeder::class,
                '--force' => true,
            ]);

            return [
                'kaijus' => Kaiju::query()->count(),
                'incidents' => Incident::query()->count(),
            ];
        });

        return response()->json([
            'message' => 'Demo data reset.',
            'records' => $records,
        ]);
    }
}
