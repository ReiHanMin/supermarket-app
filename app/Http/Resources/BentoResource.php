<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class BentoResource extends JsonResource
{
    public static $wrap = false;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
{
    $data = [
        'id' => $this->id,
        'name' => $this->name,
        'description' => $this->description,
        'image_url' => $this->getImageUrl(),
        'original_price' => $this->original_price,
        'usual_discounted_price' => $this->usual_discounted_price,
        'calories' => $this->calories,
        'availability' => $this->availability,
        'discount_percentage' => $this->discount_percentage,
        'store_id' => $this->store_id,
        'store_name' => $this->store->name ?? null,
        'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
        'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        'likes_count' => $this->likes()->where('type', 'like')->count(),
        'dislikes_count' => $this->likes()->where('type', 'dislike')->count(),
        'comments' => $this->comments()->get()->map(function ($comment) {
            return [
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
            ];
        }),
    ];

    \Log::info('BentoResource Data:', $data); // Log the data

    return $data;
}


    /**
     * Get the full URL for the image.
     *
     * @return string|null
     */
    private function getImageUrl()
{
    \Log::info('Image URL before processing:', [$this->image_url]);

    if ($this->image_url) {
        if (Storage::disk('public')->exists($this->image_url)) {
            \Log::info('File exists: ' . Storage::url($this->image_url));
            return Storage::url($this->image_url); // Generate URL to the image
        } else {
            \Log::error('File not found: ' . $this->image_url); // Log if file doesn't exist
            return null; // Return null if the file isn't found
        }
    }
    return null; // Return null if image_url is not set
}



}
