<?php 

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Arr;

/**
 * Class ProductRepository.
 */
class ProductRepository extends BaseRepository
{
	/**
     * Associated Repository Model.
     */
    const MODEL = Product::class;

    public function paginated($search_params, $limit, $order_by = 'created_at', $sort = 'desc'){
    	$limit = Arr::get($search_params, 'limit', $limit);
    	$page = Arr::get($search_params, 'page', 1);
        $keyword = Arr::get($search_params, 'keyword', '');
        $category_id = Arr::get($search_params, 'category_id', '');

        $query = $this->query();

        if ($keyword){
            $query->where('name', 'LIKE', '%' . $keyword . '%');
        }

        if ($category_id){
            $query->whereHas('categories', function($q) use ($category_id) {
                $q->where('categories.id', $category_id);
            });
        }

        $query->with('categories');
        $query->with('details');
        $query->orderBy($order_by, $sort);
        $query->skip(($page-1)*$limit);
        $query->take($limit);
        return $query->get();
    }

    public function count($search_params){
        $keyword = Arr::get($search_params, 'keyword', '');
        $category_id = Arr::get($search_params, 'category_id', '');

        $query = $this->query();

        if ($keyword){
            $query->where('name', 'LIKE', '%' . $keyword . '%');
        }

        if ($category_id){
            $query->whereHas('categories', function($q) use ($category_id) {
                $q->where('categories.id', $category_id);
            });
        }

        return $query->count();
    }

    public function getByListId($list_id){
        return $this->query()
            ->whereIn('id', $list_id)
            ->with('categories')
            ->with('details')
            ->withTrashed()
            ->get();
    }
}