<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'image' => $this->image ? array_map(function($image){
                return config('app.url').'/'.$image;
            }, $this->image) : [],
            'category' => CategoryResource::collection($this->categories),
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => $this->price,
            'promotion_price' => $this->promotion_price,
            'number' => $this->number,
            'status' => $this->status,
            'details' => ProductDetailResource::collection($this->details)
        ];
    }
}
