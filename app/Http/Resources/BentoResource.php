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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->getImageUrl(),
            'price' => $this->price,
            'calories' => $this->calories,
            'availability' => $this->availability,
            'discount_percentage' => $this->discount_percentage,
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'store_id' => $this->store_id,
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
    }
    


    /**
     * Get the full URL for the image.
     *
     * @return string|null
     */
    private function getImageUrl()
    {
        return $this->image_url ? URL::to(Storage::url($this->image_url)) : null;
    }
}
