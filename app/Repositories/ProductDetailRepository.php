<?php 

namespace App\Repositories;

use App\Models\ProductDetail;

/**
 * Class ProductDetailRepository.
 */
class ProductDetailRepository extends BaseRepository
{
	/**
     * Associated Repository Model.
     */
    const MODEL = ProductDetail::class;

    public function deleteByProduct($product_id){
    	return $this->query()
    		->where('product_id', $product_id)
    		->delete();
    }
}