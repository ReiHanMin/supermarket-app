<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;


class StoreController extends Controller
{
    

    public function getStoresApi(Request $request)
{
    $perPage = $request->get('per_page', 10);
    $stores = Store::paginate($perPage);

    foreach ($stores as $store) {
        // Log the current value of opening_hours
        \Log::info('Opening hours value:', ['opening_hours' => $store->opening_hours]);

        // Ensure opening_hours is a string before decoding
        if (is_string($store->opening_hours)) {
            $openingHours = json_decode($store->opening_hours, true);
        } elseif (is_array($store->opening_hours)) {
            $openingHours = $store->opening_hours; // Already an array
        } else {
            $openingHours = [];  // Set to an empty array if neither string nor array
            \Log::error('Invalid opening_hours format for store ID ' . $store->id);
        }

       
    }

    return response()->json($stores);
}

    
// Create a new store
public function store(Request $request)
{
    // Log the incoming request for debugging purposes
    Log::info('Store creation request:', $request->all());

    // Validate the incoming request
    $validatedData = $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'name' => 'required|string|max:255',
        'chain_name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'latitude' => 'nullable|numeric', // Add validation for latitude
        'longitude' => 'nullable|numeric', // Add validation for longitude
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:255',
        'opening_hours' => 'required|array',
        'opening_hours.*.start' => 'nullable|date_format:H:i',
        'opening_hours.*.end' => 'nullable|date_format:H:i',
    ]);

    // Handle photo upload if provided
    if ($request->hasFile('photo')) {
        $imageName = time() . '.' . $request->photo->extension();
        $request->photo->storeAs('public/images', $imageName);
        $validatedData['photo'] = '/storage/images/' . $imageName;
    }

    // Ensure opening_hours is properly formatted as JSON
    $openingHoursJson = json_encode($validatedData['opening_hours']);

    // Create the new store
    $store = Store::create([
        'name' => $validatedData['name'],
        'chain_name' => $validatedData['chain_name'],
        'address' => $validatedData['address'],
        'latitude' => $validatedData['latitude'], // Save latitude
        'longitude' => $validatedData['longitude'], // Save longitude
        'email' => $validatedData['email'],
        'phone' => $validatedData['phone'],
        'photo' => $validatedData['photo'],
        'opening_hours' => $openingHoursJson, // Save opening_hours as JSON
    ]);

    // Log the newly created store data for confirmation
    Log::info('Store created:', ['store' => $store]);

    // Return the new store as a JSON response
    return response()->json($store, 201);
}




    

    // Get a single store by ID
    public function show($id)
    {
        $store = Store::find($id);

        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }

        return response()->json($store);
    }
    public function update(Request $request, $id)
{
    $store = Store::find($id);
    
    if (!$store) {
        return response()->json(['message' => 'Store not found'], 404);
    }

    // Validate request
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'chain_name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
        'opening_hours' => 'nullable|array',
        'opening_hours.*.start' => 'nullable|date_format:H:i',
        'opening_hours.*.end' => 'nullable|date_format:H:i',
    ]);

    // Update store properties
    $store->name = $validatedData['name'];
    $store->chain_name = $validatedData['chain_name'];
    $store->address = $validatedData['address'];
    $store->latitude = $validatedData['latitude'];
    $store->longitude = $validatedData['longitude'];
    $store->email = $validatedData['email'];
    $store->phone = $validatedData['phone'];

    // Update opening hours only if provided
    if (isset($validatedData['opening_hours'])) {
        $store->opening_hours = json_encode($validatedData['opening_hours']);
    }

    // Save the store
    $store->save();

    Log::info('Store updated:', ['store' => $store]);

    // Return the updated store data
    return response()->json([
        'id' => $store->id,
        'name' => $store->name,
        'chain_name' => $store->chain_name,
        'address' => $store->address,
        'latitude' => $store->latitude,
        'longitude' => $store->longitude,
        'email' => $store->email,
        'phone' => $store->phone,
        'photo' => $store->photo ? URL::to($store->photo) : null,
        'opening_hours' => json_decode($store->opening_hours, true),
        'updated_at' => $store->updated_at->format('d M Y'),
    ]);
}

    
    

    

    

    

    

    
    


    // Delete a store
    public function destroy($id)
    {
        $store = Store::find($id);

        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }

        $store->delete();

        return response()->json(['message' => 'Store deleted successfully']);
    }

    
}
