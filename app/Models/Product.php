<?php

namespace App\Models;

use App\Models\ProductDetail;
use App\Models\Category;
use App\Models\Traits\Setter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use Setter, SoftDeletes;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $casts = [
        'image' => 'array'
    ];
    
    public function categories(){
    	return $this->belongsToMany(Category::class, 'product_category');
    }

    public function details(){
    	return $this->hasMany(ProductDetail::class)
    		->with('attribute');
    }
}
