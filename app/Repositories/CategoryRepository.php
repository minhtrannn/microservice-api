<?php 

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Arr;

/**
 * Class CategoryRepository.
 */
class CategoryRepository extends BaseRepository
{
	/**
     * Associated Repository Model.
     */
    const MODEL = Category::class;

    public function paginated($search_params, $limit, $order_by = 'created_at', $sort = 'desc'){
    	$limit = Arr::get($search_params, 'limit', $limit);
    	$page = Arr::get($search_params, 'page', 1);
        $keyword = Arr::get($search_params, 'keyword', '');
        $from = Arr::get($search_params, 'from', '');

        $query = $this->query();

        if ($keyword){
            $query->where('name', 'LIKE', '%' . $keyword . '%');
        }

        if ($from === 'cms'){
        	$query->orderBy($order_by, $sort);
	        $query->skip(($page-1)*$limit);
	        $query->take($limit);
        }
        return $query->get();
    }

    public function count($search_params){
        $keyword = Arr::get($search_params, 'keyword', '');

        $query = $this->query();

        if ($keyword){
            $query->where('name', 'LIKE', '%' . $keyword . '%');
        }

        return $query->count();
    }
}