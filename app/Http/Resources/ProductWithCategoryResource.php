<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductWithCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->image,
            'category' => CategoryResource::collection($this->whenLoaded('category')),
            'attributes'=>ProductAttributeResource::collection($this->whenLoaded('attributes'))
        ];
        
        return $data;

    }
}
