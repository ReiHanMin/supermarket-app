<?php

namespace App\Services;

use App\Models\Store;

class StoreService
{
    /**
     * Get store details by store ID, including bentos if necessary.
     *
     * @param int $storeId
     * @return Store
     */
    public function getStoreDetails($storeId)
    {
        return Store::with('bentos')->findOrFail($storeId);
    }

    /**
     * Get stores with calculated distance from the user's location.
     *
     * @param float $userLat
     * @param float $userLng
     * @return Collection
     */
    public function getStoresWithDistance($userLat, $userLng)
    {
        $stores = Store::all();

        foreach ($stores as $store) {
            if (!empty($store->latitude) && !empty($store->longitude)) {
                $store->distance = round($this->calculateDistance($userLat, $userLng, $store->latitude, $store->longitude), 2);
            } else {
                $store->distance = 'Unknown';
            }
        }

        return $stores;
    }

    /**
     * Calculate the distance between two sets of coordinates using the Haversine formula.
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $earthRadius * $angle;
    }
}
