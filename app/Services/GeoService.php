<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GeoService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Get coordinates (latitude and longitude) for a given address using Nominatim API.
     *
     * @param string $address
     * @return array|null
     */
    public function getCoordinatesFromAddress($address)
    {
        try {
            $response = $this->client->get('https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1
                ],
                'headers' => [
                    'User-Agent' => 'SupermarketApp1.0'
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (!empty($data)) {
                return [
                    'latitude' => $data[0]['lat'],
                    'longitude' => $data[0]['lon']
                ];
            }
        } catch (\Exception $e) {
            Log::error("Failed to get coordinates for address: $address", ['error' => $e->getMessage()]);
        }

        return null;
    }
}
