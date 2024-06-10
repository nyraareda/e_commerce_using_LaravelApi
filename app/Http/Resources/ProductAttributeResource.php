<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attribute_name' => $this->attribute_name,
            'attribute_value' => $this->attribute_value,
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
