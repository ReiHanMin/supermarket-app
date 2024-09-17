<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class BentoListResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name, // Updated from 'title' to 'name'
            'description' => $this->description, // Added description field
            'image_url' => $this->getImageUrl(), // Updated to dynamically generate the image URL
            'price' => $this->price,
            'calories' => $this->calories, // Added calories field
            'availability' => $this->availability, // Added availability field
            'discount_percentage' => $this->discount_percentage, // Added discount field
            'rating' => $this->rating, // Added rating field
            'reviews_count' => $this->reviews_count, // Added reviews count field
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
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
