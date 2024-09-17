<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BentoRequest;
use App\Http\Resources\BentoListResource;
use App\Http\Resources\BentoResource;
use App\Models\Bento;
use App\Models\BentoCategory;
use App\Models\BentoImage;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BentoController extends Controller
{
    // Existing methods...

    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Bento::query()
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return BentoListResource::collection($query);
    }

    public function store(BentoRequest $request)
    {
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
    }

    public function show(Bento $bento)
    {
        return new BentoResource($bento);
    }

    public function update(BentoRequest $request, Bento $bento)
    {
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        $images = $data['images'] ?? [];
        $deletedImages = $data['deleted_images'] ?? [];
        $imagePositions = $data['image_positions'] ?? [];
        $categories = $data['categories'] ?? [];

        $this->saveCategories($categories, $bento);
        $this->saveImages($images, $imagePositions, $bento);

        if (count($deletedImages) > 0) {
            $this->deleteImages($deletedImages, $bento);
        }

        $bento->update($data);

        return new BentoResource($bento);
    }

    public function destroy(Bento $bento)
    {
        $bento->delete();

        return response()->noContent();
    }

    private function saveCategories($categoryIds, Bento $bento)
    {
        BentoCategory::where('bento_id', $bento->id)->delete();
        $data = array_map(fn($id) => ['category_id' => $id, 'bento_id' => $bento->id], $categoryIds);

        BentoCategory::insert($data);
    }

    private function saveImages($images, $positions, Bento $bento)
    {
        foreach ($positions as $id => $position) {
            BentoImage::where('id', $id)->update(['position' => $position]);
        }

        foreach ($images as $id => $image) {
            $name = Str::random() . '.' . $image->getClientOriginalExtension();

            if (!Storage::putFileAs('public/images', $image, $name)) {
                throw new \Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
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

    // Remove any existing like/dislike from this user
    $bento->likes()->where('user_id', $user->id)->delete();

    // Add a new like
    $bento->likes()->create([
        'user_id' => $user->id,
        'type' => 'like' // Set type as 'like'
    ]);

    return response()->json(['message' => 'Bento liked successfully.'], 200);
}

public function dislikeBento(Request $request, Bento $bento)
{
    $user = Auth::user();

    // Remove any existing like/dislike from this user
    $bento->likes()->where('user_id', $user->id)->delete();

    // Add a new dislike
    $bento->likes()->create([
        'user_id' => $user->id,
        'type' => 'dislike' // Set type as 'dislike'
    ]);

    return response()->json(['message' => 'Bento disliked successfully.'], 200);
}


    public function commentOnBento(Request $request, Bento $bento)
    {
        $request->validate([
            'comment' => 'required|string|max:500'
        ]);

        $user = Auth::user();

        // Add a new comment
        $bento->comments()->create([
            'user_id' => $user->id,
            'content' => $request->input('comment')
        ]);

        return response()->json(['message' => 'Comment added successfully.'], 201);
    }

}
