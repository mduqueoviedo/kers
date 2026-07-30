<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Kaiju;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DemoDataController extends Controller
{
    /**
     * Remove all current KERS domain data without changing the schema.
     */
    public function destroy(): JsonResponse
    {
        $deleted = DB::transaction(function (): array {
            $currentRecords = [
                'kaijus' => Kaiju::query()->count(),
                'incidents' => Incident::query()->count(),
            ];

            Kaiju::query()->delete();

            return $currentRecords;
        });

        return response()->json([
            'message' => 'Demo data wiped.',
            'deleted' => $deleted,
        ]);
    }

    /**
     * Run the repeatable application seeders.
     */
    public function seed(): JsonResponse
    {
        DB::transaction(
            fn () => Artisan::call('db:seed', ['--force' => true]),
        );

        return response()->json([
            'message' => 'Demo data seeded.',
            'records' => [
                'kaijus' => Kaiju::query()->count(),
                'incidents' => Incident::query()->count(),
            ],
        ]);
    }
}
