<?php
use App\Helpers\GeoHelper;

public function updateLocation(Request $request)
{
    $userLat = $request->input('userLat');
    $userLng = $request->input('userLng');
    Log::info("User Latitude: $userLat, User Longitude: $userLng");

    if (!$userLat || !$userLng) {
        return response()->json(['success' => false, 'message' => 'Invalid location data'], 400);
    }

    $bentos = Bento::with(['updates' => function ($query) {
        $query->latest()->limit(1);
    }])->get();

    $stores = Store::all();

    foreach ($stores as $store) {
        if (!empty($store->latitude) && !empty($store->longitude)) {
            $store->distance = round(GeoHelper::calculateDistance($userLat, $userLng, $store->latitude, $store->longitude), 2);
            Log::info("Calculated distance for store " . $store->name . ": " . $store->distance . " km");
        } else {
            $store->distance = 'Unknown';
            Log::warning("Could not calculate distance for store: " . $store->name);
        }
    }

    return response()->json([
        'success' => true,
        'stores' => $stores,
        'bentos' => $bentos
    ]);
}
