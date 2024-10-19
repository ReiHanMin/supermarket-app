<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\BentoRequest;
use App\Http\Resources\BentoListResource;
use App\Http\Resources\BentoResource;
use App\Models\Bento;
use App\Models\Store;
use App\Models\BentoUpdate;
use App\Models\BentoCategory;
use App\Models\BentoImage;
use App\Models\BentoStore;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Validation\ValidationException;


class BentoController extends Controller
{

    public function index(Request $request)
{
    try {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        // Fetch bento data with optional eager loading
        $query = Bento::with(['stores', 'relatedItems', 'reviews'])
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection);

        // Paginate the results
        $bentos = $query->paginate($perPage);

        // Use the BentoResource for consistent formatting
        return BentoResource::collection($bentos);
    } catch (\Exception $e) {
        \Log::error('Error fetching bentos: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to fetch bentos'], 500);
    }
}

 
    public function store(BentoRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = $request->user()->id;
            $data['updated_by'] = $request->user()->id;

            $images = $data['images'] ?? [];
            $imagePositions = $data['image_positions'] ?? [];
            $categories = $data['categories'] ?? [];

            $bento = Bento::create($data);

            $this->saveCategories($categories, $bento);
            $this->saveImages($images, $imagePositions, $bento);

            return new BentoResource($bento);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeBatch(Request $request)
    {
        // Validate the fields coming from the form, including id validation
        $validator = Validator::make($request->all(), [
            'bentos.*.name' => 'required|string|max:255',
            'bentos.*.original_price' => 'required|numeric',
            'bentos.*.usual_discounted_price' => 'required|numeric',
            'bentos.*.calories' => 'nullable|numeric',
            'bentos.*.description' => 'required|string',
            'bentos.*.image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bentos.*.availability' => 'required|integer|min:0|max:2',
            'bentos.*.discount_percentage' => 'nullable|numeric',
            'bentos.*.store_id' => 'required|exists:stores,id',  // Validate that store_id is required and must exist in stores table
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Loop through each bento input and save the data
        foreach ($request->input('bentos') as $index => $bentoData) {
            // Check if the bento already exists in the bentos table
            $existingBento = Bento::where('name', $bentoData['name'])->first();
    
            if (!$existingBento) {
                // If the bento doesn't exist, create a new record in the bentos table
                $bento = new Bento();
                $bento->name = $bentoData['name'];
                $bento->original_price = $bentoData['original_price'];
                $bento->calories = $bentoData['calories'] ?? null;
                $bento->description = $bentoData['description'];
                $bento->availability = $bentoData['availability']; // Save the availability field
                $bento->usual_discounted_price = $bentoData['usual_discounted_price'] ?? null; // Save discounted price
                $bento->discount_percentage = $bentoData['discount_percentage'] ?? null; // Save discount percentage
    
                // Handle image upload if it exists
                if ($request->hasFile('bentos.' . $index . '.image_url')) {
                    $image = $request->file('bentos.' . $index . '.image_url');
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->storeAs('public/images', $imageName);
                    $bento->image_url = 'images/' . $imageName;
                }
    
                // Save the new bento to the bentos table
                $bento->save();
            } else {
                // Use the existing bento record
                $bento = $existingBento;
    
                // Update existing bento with new data
                $bento->update([
                    'original_price' => $bentoData['original_price'],
                    'calories' => $bentoData['calories'] ?? null,
                    'description' => $bentoData['description'],
                    'availability' => $bentoData['availability'],
                    'usual_discounted_price' => $bentoData['usual_discounted_price'] ?? null,
                    'discount_percentage' => $bentoData['discount_percentage'] ?? null,
                ]);
            }
    
            // Map availability to stock_level
            $stockLevel = $bentoData['availability'];  // Directly assign the string value
    
            // Check if the bento-store relationship already exists in bento_store
            $bentoStoreExists = BentoStore::where('bento_id', $bento->id)
                                          ->where('store_id', $bentoData['store_id'])
                                          ->exists();
    
            if (!$bentoStoreExists) {
                // Save store-specific data in the bento_store table
                BentoStore::create([
                    'bento_id' => $bento->id,
                    'store_id' => $bentoData['store_id'],
                    'current_discount' => $bentoData['discount_percentage'] ?? null, // Store-specific discount
                    'stock_level' => $stockLevel,  // Directly map stock_level to availability
                ]);
            }
        }
    
        return response()->json(['message' => 'Bentos saved successfully'], 201);
    }
    

    

    

    
public function checkBentoExists(Request $request) {
    $name = $request->query('name');
    $store_id = $request->query('store_id');

    // Check if a bento with this name exists in the `bentos` table
    $bento = Bento::where('name', $name)->first();

    if ($bento) {
        // Check if this bento is already linked to the same store in `bento_store` table
        $bentoStore = BentoStore::where('bento_id', $bento->id)
                                ->where('store_id', $store_id)
                                ->exists();

        if ($bentoStore) {
            // Bento exists in the same store
            return response()->json(['exists_in_same_store' => true, 'bento_id' => $bento->id]);
        } else {
            // Bento exists but not for this store
            return response()->json(['exists_in_other_store' => true, 'bento_id' => $bento->id]);
        }
    }

    return response()->json(['exists' => false]);
}






public function show($id)
{
    // Fetch the bento with the corresponding id
    $bento = Bento::findOrFail($id);

    // Fetch the associated store data from the bento_store table
    $bentoStore = BentoStore::where('bento_id', $id)->first();

    // If there is an associated store, include the store_id in the response
    if ($bentoStore) {
        $bento->store_id = $bentoStore->store_id;
        $bento->current_discount = $bentoStore->current_discount;
        $bento->stock_level = $bentoStore->stock_level;
    }

    return response()->json($bento);
}


public function getBentos(Request $request)
{
    $storeId = $request->query('store_id');
    \Log::info('Received Store ID: ' . $storeId);  // Log the store_id received

    if ($storeId) {
        // Fetch bentos that are linked to the selected store using the bento_store table
        $bentos = DB::table('bentos')
                    ->join('bento_store', 'bentos.id', '=', 'bento_store.bento_id')
                    ->where('bento_store.store_id', $storeId)
                    ->select('bentos.*')  // Select only bento fields from bentos table
                    ->get();
                    
        \Log::info('Bentos found for Store ID ' . $storeId . ': ' . $bentos->count());  // Log the count of filtered bentos
    } else {
        // If no store_id is provided, return all bentos
        $bentos = Bento::all();
        \Log::info('Returning all bentos');
    }

    return response()->json($bentos);
}


    

public function update(BentoRequest $request, Bento $bento)
{
    \Log::info('Update Request Raw Data:', $request->all());  // Log incoming data for debugging

    // Loop through each bento in the request, including the index
    foreach ($request->input('bentos') as $index => $bentoData) {
        // Update the bento details
        $bento->update([
            'name' => $bentoData['name'],  
            'original_price' => $bentoData['original_price'],  
            'calories' => $bentoData['calories'],  
            'description' => $bentoData['description'],  
            'availability' => $bentoData['availability'],  
            'usual_discounted_price' => $bentoData['usual_discounted_price'],
            'discount_percentage' => $bentoData['discount_percentage'],
        ]);

        // Handle image upload if it exists
        if ($request->hasFile('bentos.' . $index . '.image_url')) {
            $image = $request->file('bentos.' . $index . '.image_url');
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Store the image in 'storage/app/public/images'
            $image->storeAs('public/images', $imageName);

            // Save only the relative path to the database
            $bento->image_url = 'images/' . $imageName;
            $bento->save();  // Save the updated bento with the new image URL
        }
    }

    \Log::info('Updated Bento Data:', $bento->toArray());  // Log the updated bento data

    return response()->json(['message' => 'Bento updated successfully!']);
}


   
    
    

public function storeUpdate(Request $request, $bentoId)
{
    // Validate the incoming request
    $validatedData = $request->validate([
        'store_id' => 'required|exists:stores,id', // Validate that store_id is required and must exist in stores table
        'discounted_price' => 'required|numeric',
        'discount_percentage' => 'required|numeric',
        'availability' => 'required|integer|min:0|max:2',
        'visit_time' => 'required|date',
    ]);

    // Update the bento_store table for the current store-bento relationship
    $bentoStore = BentoStore::where('bento_id', $bentoId)
                            ->where('store_id', $validatedData['store_id'])
                            ->first();

    if ($bentoStore) {
        // Update the current values in the bento_store table
        $bentoStore->update([
            'current_discount' => $validatedData['discount_percentage'],
            'stock_level' => $validatedData['availability'],
            'visit_time' => $validatedData['visit_time'],
        ]);
    } else {
        // If no entry exists in bento_store, create one (fallback case)
        BentoStore::create([
            'bento_id' => $bentoId,
            'store_id' => $validatedData['store_id'],
            'current_discount' => $validatedData['discount_percentage'],
            'stock_level' => $validatedData['availability'],
            'visit_time' => $validatedData['visit_time'],
        ]);
    }

    // Log the update in the BentoUpdates table
    BentoUpdate::create([
        'bento_id' => $bentoId,
        'store_id' => $validatedData['store_id'], // Set the store ID
        'discounted_price' => $validatedData['discounted_price'],
        'discount_percentage' => $validatedData['discount_percentage'],
        'availability' => $validatedData['availability'],
        'visit_time' => $validatedData['visit_time'],
    ]);

    return response()->json(['message' => 'Bento updated successfully']);
}

public function logUpdate(Request $request)
{
    // Validate the incoming request
    $validatedData = $request->validate([
        'bento_id' => 'required|exists:bentos,id',
        'store_id' => 'required|exists:stores,id',  // Ensure the store exists
        'discounted_price' => 'required|numeric',
        'discount_percentage' => 'required|numeric',
        'availability' => 'required|integer|min:0|max:2',
        'visit_time' => 'required|date',
    ]);

    // Check if an update for the same bento, store, and visit time already exists
    $existingUpdate = BentoUpdate::where('bento_id', $validatedData['bento_id'])
                                 ->where('store_id', $validatedData['store_id'])
                                 ->where('visit_time', $validatedData['visit_time'])
                                 ->first();

    // If no such update exists, create a new one
    if (!$existingUpdate) {
        // Create a new BentoUpdate record
        BentoUpdate::create([
            'bento_id' => $validatedData['bento_id'],
            'store_id' => $validatedData['store_id'],
            'discounted_price' => $validatedData['discounted_price'],
            'discount_percentage' => $validatedData['discount_percentage'],
            'availability' => $validatedData['availability'],
            'visit_time' => $validatedData['visit_time'],
        ]);
    }

    return response()->json(['message' => 'Bento update logged successfully']);
}
}
