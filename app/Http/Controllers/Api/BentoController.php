<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\BentoRequest;
use App\Http\Resources\BentoListResource;
use App\Http\Resources\BentoResource;
use App\Models\Bento;
use App\Models\BentoUpdate;
use App\Models\BentoCategory;
use App\Models\BentoImage;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
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
    // Validate the fields coming from the form
    $validator = Validator::make($request->all(), [
        'bentos.*.name' => 'required|string|max:255',
        'bentos.*.original_price' => 'required|numeric',
        'bentos.*.usual_discounted_price' => 'required|numeric',
        'bentos.*.calories' => 'nullable|numeric',
        'bentos.*.description' => 'required|string',
        'bentos.*.image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'bentos.*.stock_message' => 'nullable|string|max:255',
        'bentos.*.availability' => 'required|string|in:Many,A few left,Sold out',
        'bentos.*.store_id' => 'required|exists:stores,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Loop through each bento input and save the data
    foreach ($request->input('bentos') as $index => $bentoData) {
        // Create a new Bento instance
        $bento = new Bento();
        $bento->name = $bentoData['name'];
        $bento->original_price = $bentoData['original_price'];
        $bento->usual_discounted_price = $bentoData['usual_discounted_price'];

        // Automatically calculate discount percentage if original price and discounted price are provided
        if (!empty($bentoData['original_price']) && !empty($bentoData['usual_discounted_price'])) {
            $bento->discount_percentage = round(((float)$bentoData['original_price'] - (float)$bentoData['usual_discounted_price']) / (float)$bentoData['original_price'] * 100, 2);
        } else {
            $bento->discount_percentage = null;
        }

        $bento->calories = $bentoData['calories'] ?? null;
        $bento->description = $bentoData['description'];
        $bento->stock_message = $bentoData['stock_message'] ?? null;
        $bento->availability = $bentoData['availability'];
        $bento->store_id = $bentoData['store_id'];

        // Handle image upload if it exists
        if ($request->hasFile('bentos.' . $index . '.image_url')) {
            $image = $request->file('bentos.' . $index . '.image_url');
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Store the image in 'storage/app/public/images'
            $image->storeAs('public/images', $imageName);

            // Save only the relative path to the database
            $bento->image_url = 'images/' . $imageName;
        }

        // Save the bento data to the database
        $bento->save();
    }

    return response()->json(['message' => 'Bentos saved successfully'], 201);
}

    


    public function show(Bento $bento)
    {
        $bento->load(['stores', 'relatedItems', 'reviews']); // Eager load relationships
        return new BentoResource($bento);
    }

    public function getBentos(Request $request)
{
    $storeId = $request->query('store_id');
    \Log::info('Received Store ID: ' . $storeId);  // Log the store_id received

    if ($storeId) {
        // Filter bentos by store_id
        $bentos = Bento::where('store_id', $storeId)->get();
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

    // Loop through each bento in the request and update the relevant record
    foreach ($request->input('bentos') as $bentoData) {
        $bento->update([
            'name' => $bentoData['name'],  // Static field: name
            'original_price' => $bentoData['original_price'],  // Static field: original_price
            'calories' => $bentoData['calories'],  // Static field: calories
            'description' => $bentoData['description'],  // Static field: description
            'availability' => $bentoData['availability'],  // Dynamic field: availability
            'usual_discounted_price' => $bentoData['usual_discounted_price'],
            'discount_percentage' => $bentoData['discount_percentage'],
            'stock_message' => $bentoData['stock_message'] ?? null,
            'store_id' => $bentoData['store_id'],  // Ensure the correct store_id is updated
        ]);

        // Handle image upload if provided
        if (!empty($bentoData['image_url'])) {
            $bento->image_url = $bentoData['image_url'];
            $bento->save();
        }
    }

    \Log::info('Updated Bento Data:', $bento->toArray());  // Log the updated bento data

    return response()->json(['message' => 'Bento updated successfully!']);
}

   
    
    

    public function storeUpdate(Request $request, $bentoId)
{
    // Validate the incoming request
    $validatedData = $request->validate([
        'discounted_price' => 'required|numeric',
        'discount_percentage' => 'required|numeric',
        'stock_message' => 'nullable|string',
        'availability' => 'nullable|string',
        'visit_time' => 'required|date',
    ]);

    // Create a new update entry in the BentoUpdates table
    $bentoUpdate = new BentoUpdate();
    $bentoUpdate->bento_id = $bentoId;
    $bentoUpdate->discounted_price = $validatedData['discounted_price'];
    $bentoUpdate->discount_percentage = $validatedData['discount_percentage'];
    $bentoUpdate->stock_message = $validatedData['stock_message'];
    $bentoUpdate->availability = $validatedData['availability'];
    $bentoUpdate->visit_time = $validatedData['visit_time'];
    $bentoUpdate->save();

    return response()->json(['message' => 'Bento updated successfully']);
}


    

    





    public function destroy(Bento $bento)
    {
        $bento->delete();
        return response()->noContent();
    }

    private function saveCategories($categoryIds, Bento $bento)
    {
        if (!empty($categoryIds)) {
            BentoCategory::where('bento_id', $bento->id)->whereNotIn('category_id', $categoryIds)->delete();
            $existingIds = BentoCategory::where('bento_id', $bento->id)->pluck('category_id')->toArray();
            $newCategoryIds = array_diff($categoryIds, $existingIds);
            $data = array_map(fn($id) => ['category_id' => $id, 'bento_id' => $bento->id], $newCategoryIds);
            BentoCategory::insert($data);
        }
    }

    private function saveImages($images, $positions, Bento $bento)
    {
        foreach ($positions as $id => $position) {
            BentoImage::where('id', $id)->update(['position' => $position]);
        }

        foreach ($images as $id => $image) {
            $this->validate(request(), [
                "images.{$id}" => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $name = Str::random() . '.' . $image->getClientOriginalExtension();

            if (!Storage::putFileAs('public/images', $image, $name)) {
                throw new Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
            }

            $relativePath = 'images/' . $name;

            BentoImage::create([
                'bento_id' => $bento->id,
                'path' => $relativePath,
                'url' => URL::to(Storage::url($relativePath)),
                'mime' => $image->getClientMimeType(),
                'size' => $image->getSize(),
                'position' => $positions[$id] ?? $id + 1
            ]);
        }
    }

    private function deleteImages($imageIds, Bento $bento)
    {
        $images = BentoImage::where('bento_id', $bento->id)->whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            if ($image->path) {
                Storage::delete('public/' . $image->path);
            }
            $image->delete();
        }
    }

    public function likeBento(Request $request, Bento $bento)
    {
        $user = Auth::user();

        $bento->likes()->where('user_id', $user->id)->delete();

        $bento->likes()->create([
            'user_id' => $user->id,
            'type' => 'like'
        ]);

        return response()->json(['message' => 'Bento liked successfully.'], 200);
    }

    public function dislikeBento(Request $request, Bento $bento)
    {
        $user = Auth::user();

        $bento->likes()->where('user_id', $user->id)->delete();

        $bento->likes()->create([
            'user_id' => $user->id,
            'type' => 'dislike'
        ]);

        return response()->json(['message' => 'Bento disliked successfully.'], 200);
    }

    public function commentOnBento(Request $request, Bento $bento)
    {
        $request->validate([
            'comment' => 'required|string|max:500'
        ]);

        $user = Auth::user();

        $bento->comments()->create([
            'user_id' => $user->id,
            'content' => $request->input('comment')
        ]);

        return response()->json(['message' => 'Comment added successfully.'], 201);
    }
}
