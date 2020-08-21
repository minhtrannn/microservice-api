<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductAttributeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'attribute' => new ProductAttributeResource($this->attribute),
            'value' => $this->value
        ];
    }
}
