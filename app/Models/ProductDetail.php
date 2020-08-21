<?php

namespace App\Models;

use App\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    public $timestamp = false;

    public function attribute(){
    	return $this->belongsTo(ProductAttribute::class, 'product_attribute_id', 'id')
    		->whereNull('deleted_at');
    }
}
