<?php 

namespace App\Repositories;

use App\Models\ProductAttribute;
use Illuminate\Support\Arr;

/**
 * Class ProductAttributeRepository.
 */
class ProductAttributeRepository extends BaseRepository
{
	/**
     * Associated Repository Model.
     */
    const MODEL = ProductAttribute::class;

    public function paginated($search_params, $limit, $order_by = 'created_at', $sort = 'desc'){
    	$limit = Arr::get($search_params, 'limit', $limit);
    	$page = Arr::get($search_params, 'page', 1);
        $keyword = Arr::get($search_params, 'keyword', '');

        $query = $this->query();

        if (!empty($keyword)){
            $query->where('name', 'LIKE', '%' . $keyword . '%');
        }

        $query->orderBy($order_by, $sort);
        $query->skip(($page-1)*$limit);
        $query->take($limit);
        return $query->get();
    }
}