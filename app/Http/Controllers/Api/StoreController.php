<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    // Fetch all stores
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $stores = Store::paginate($perPage);

        return response()->json($stores);
    }

    // Create a new store
    public function store(Request $request)
{
    // Log the incoming request
    Log::info('Store creation request:', $request->all());

    $validatedData = $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'name' => 'required|string|max:255',
        'chain_name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:255',
    ]);

    if ($request->hasFile('photo')) {
        $imageName = time().'.'.$request->photo->extension();
        $request->photo->storeAs('public/images', $imageName);
        $validatedData['photo'] = '/storage/images/' . $imageName;
    }

    $store = Store::create($validatedData);

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

    // Update a store
    public function update(Request $request, $id)
    {
        $store = Store::find($id);
    
        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }
    
        // Log the incoming request data for debugging purposes
        Log::info('Update store request data: ', $request->all());
    
        // Validate request
        $validator = Validator::make($request->all(), [
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'chain_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);
    
        if ($validator->fails()) {
            Log::error('Validation errors: ', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();
    
        // Check if photo exists and move it to the 'public/images' directory
        if ($request->hasFile('photo')) {
            $imageName = time().'.'.$request->photo->extension();  
            $request->photo->storeAs('public/images', $imageName);  // Save to storage/app/public/images
            $validatedData['photo'] = '/storage/images/' . $imageName;  // Use the correct URL to reference the file
        }
        
        
        
    
        // Update store with only the validated data
        $store->update($validatedData);
    
        return response()->json([
            'id' => $store->id,
            'name' => $store->name,
            'chain_name' => $store->chain_name,
            'address' => $store->address,
            'email' => $store->email,
            'phone' => $store->phone,
            'photo' => $store->photo ? URL::to($store->photo) : null,  // Ensure full URL
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
