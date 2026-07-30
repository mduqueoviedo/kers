<?php

namespace App\Services\Usgs;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class UsgsEarthquakeClient
{
    /**
     * Fetch recent earthquake events from the USGS catalog.
     *
     * @return array<string, mixed>
     */
    public function fetchRecentEvents(): array
    {
        $response = Http::acceptJson()
            ->timeout((int) config('services.usgs.timeout'))
            ->get((string) config('services.usgs.url'), [
                'format' => 'geojson',
                'orderby' => 'time',
                'limit' => (int) config('services.usgs.limit'),
            ]);

        return $this->decodeResponse($response);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeResponse(Response $response): array
    {
        $payload = $response->throw()->json();

        if (! is_array($payload)) {
            throw new \UnexpectedValueException('USGS returned an invalid response payload.');
        }

        return $payload;
    }
}
