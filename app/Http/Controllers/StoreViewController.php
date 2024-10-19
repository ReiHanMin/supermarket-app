<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Bento;
use App\Services\StoreStatusService;
use App\Services\StoreService;
use App\Services\GeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreViewController extends Controller
{
    protected $storeStatusService;
    protected $storeService;
    protected $geoService;

    public function __construct(StoreStatusService $storeStatusService, StoreService $storeService, GeoService $geoService)
    {
        $this->storeStatusService = $storeStatusService;
        $this->storeService = $storeService;
        $this->geoService = $geoService;
    }

    public function productIndex()
{
    // Fetch all bentos
    $bentos = Bento::with(['updates' => function ($query) {
        $query->latest()->limit(1);
    }])->get();

    // Split bentos into different sections
    $closestBentos = $bentos->take(6); // Assuming these are the closest bentos for demonstration purposes
    $hotDeals = $bentos->filter(function($bento) {
        return $bento->updates->first() && $bento->updates->first()->discount_percentage > 20; // Filter bentos with more than 20% discount
    })->take(6); // Assuming 6 hot deals

    $exploreMoreBentos = $bentos->slice(6); // Rest of the bentos for "Explore More" section

    // Fetch stores
    $stores = Store::all();

    foreach ($stores as $store) {
        // Get the store coordinates or fetch from Nominatim if missing
        if (empty($store->latitude) || empty($store->longitude)) {
            $coordinates = $this->geoService->getCoordinatesFromAddress($store->address);
            if ($coordinates) {
                $store->latitude = $coordinates['latitude'];
                $store->longitude = $coordinates['longitude'];
                $store->save();
            }
        }

        $openingHours = is_string($store->opening_hours) ? json_decode($store->opening_hours, true) : $store->opening_hours;
        if (is_array($openingHours)) {
            $store->status = $this->storeStatusService->getStoreStatus($openingHours);
        } else {
            $store->status = "Status not available";
        }
    }

    return view('product.index', compact('closestBentos', 'hotDeals', 'exploreMoreBentos', 'stores'));
}

    

    public function updateLocation(Request $request)
    {
        $userLat = $request->input('userLat');
        $userLng = $request->input('userLng');
        Log::info("User Latitude: $userLat, User Longitude: $userLng");

        if (!$userLat || !$userLng) {
            return response()->json(['success' => false, 'message' => 'Invalid location data'], 400);
        }

        // Get stores with distance using the service
        $stores = $this->storeService->getStoresWithDistance($userLat, $userLng);
        $bentos = Bento::with(['updates' => function ($query) {
            $query->latest()->limit(1);
        }])->get();

        return response()->json([
            'success' => true,
            'stores' => $stores,
            'bentos' => $bentos
        ]);
    }

    public function showStoreDetail($storeId)
{
    // Find the store by ID, if not found, throw a 404 error
    $store = Store::findOrFail($storeId);

    // Load bentos associated with this store
    $bentos = $store->bentos;

    // Get user's location (latitude, longitude)
    // Assuming you have a mechanism to get user location (e.g., from request/session)
    $userLat = session('userLat'); // Example: pulling from session
    $userLng = session('userLng');

    if ($userLat && $userLng) {
        // Calculate the distance using GeoHelper or GeoService
        $geoHelper = new \App\Helpers\GeoHelper(); // Or inject GeoHelper using dependency injection
        $store->distance = round($geoHelper->calculateDistance($userLat, $userLng, $store->latitude, $store->longitude), 2);
    } else {
        $store->distance = 'N/A';
    }

    // Pass store and bentos data to the view
    return view('product.store-detail', compact('store', 'bentos'));
}


}
