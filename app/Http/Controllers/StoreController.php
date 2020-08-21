<?php

namespace App\Http\Controllers;

use App\Helpers\Auth;
use App\Helpers\Response;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Repositories\ProductDetailRepository;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Arr;
use Validator;
use Webpatser\Uuid\Uuid;

class StoreController
{
	const ITEM_PER_PAGE = 15;

	protected $product_repository;

    public function __construct(){
    	$this->product_repository = new ProductRepository();
    }

    /**
     * Display a listing of the resource.
     * @param  AMQP request $request
     * @return App\Helpers\Response
     */
    public function list($request){
        $products = $this->product_repository->paginated($request, static::ITEM_PER_PAGE);
        $data = [];
        $total = 0;
        if ($products){
            $collection = ProductResource::collection($products);
            $data = json_decode($collection->toJson(), true);
            $total = $this->product_repository->count($request);
        }
        return Response::data($data, $total);
    }

    /**
     * Store a newly created resource in storage.
     * @param  AMQP request $request
     * @return App\Helpers\Response
     */
    public function store($request){
        $validation = Validator::make($request, [
            'name' => 'required|max:255',
            'code' => 'required|max:50',
            'image' => 'required|array|min:1',
            'product_category_id' => 'required|max:50'
        ]);

        if ($validation->fails()){
            return Response::dataError($validation->messages()->first());
        }
        return DB::transaction(function () use ($request) {
        	try {
        		$product = new Product();
		        $product->id = (string)Uuid::generate(4);
		        $product->name = $request['name'];
                $product->image = array_map(function($image){
                    return str_replace(config('app.url').'/', '', $image);
                }, $request['image']);
		        $product->code = $request['code'];
		        $product->product_category_id = $request['product_category_id'];
		        $product->price = Arr::get($request, 'price', 0);
		        $product->number = Arr::get($request, 'number', 0);
                $product->setOwner(Auth::user()['uuid']);
		        $product->save();

		        // add details
		        $details = Arr::get($request, 'details', []);
		        if ($details){
		        	$detail_data = [];
		        	foreach ($details as $detail) {
		        		$detail_data[] = [
		        			'product_id' => $product->id,
		        			'product_attribute_id' => isset($detail['product_attribute_id']) ? $detail['product_attribute_id'] : '',
		        			'value' => isset($detail['value']) ? $detail['value'] : ''
		        		];
		        	}
		        	if ($detail_data){
		        		ProductDetail::insert($detail_data);
		        	}
		        }

		        $resource = new ProductResource($product);
		        $data = $resource->resolve();

		        return Response::data($data);
        	} catch (Exception $e) {
        		return Response::dataError('Error');
        	}
	    });
    }

    /**
     * Show the form for editing the specified resource.
     * @param  AMQP request $request
     * @return App\Helpers\Response
     */
    public function show($id){
        $product = $this->product_repository->find($id);
        $data = [];

        if ($product){
            $resource = new ProductResource($product);
            $data = $resource->resolve();
        }
        return Response::data($data);
    }

    /**
     * Update the specified resource in storage.
     * @param  $id
     * @param  AMQP request $request
     * @return App\Helpers\Response
     */
    public function update($id, $request){
        $product = $this->product_repository->find($id);
        if (!$product){
            return Response::dataError('Product not found');
        }

        $validation = Validator::make($request, [
            'name' => 'required|max:255',
            'code' => 'required|max:50',
            'image' => 'required|array|min:1',
            'product_category_id' => 'required|max:50'
        ]);

        if ($validation->fails()){
            return Response::dataError($validation->messages()->first());
        }
        return DB::transaction(function () use ($product, $request) {
        	try {
        		$product->name = $request['name'];
		        $product->code = $request['code'];
                $product->image = array_map(function($image){
                    return str_replace(config('app.url').'/', '', $image);
                }, $request['image']);
		        $product->product_category_id = $request['product_category_id'];
		        $product->price = Arr::get($request, 'price', 0);
		        $product->number = Arr::get($request, 'number', 0);
                $product->setUpdater(Auth::user()['uuid']);
		        $product->save();

		        // remove all details
		        $product_detail_repository = new ProductDetailRepository();
		        $product_detail_repository->deleteByProduct($product->id);

		        // add details
		        $details = Arr::get($request, 'details', []);
		        if ($details){
		        	$detail_data = [];
		        	foreach ($details as $detail) {
		        		$detail_data[] = [
		        			'product_id' => $product->id,
		        			'product_attribute_id' => isset($detail['product_attribute_id']) ? $detail['product_attribute_id'] : '',
		        			'value' => isset($detail['value']) ? $detail['value'] : ''
		        		];
		        	}
		        	if ($detail_data){
		        		ProductDetail::insert($detail_data);
		        	}
		        }

		        $resource = new ProductResource($product);
		        $data = $resource->resolve();

		        return Response::data($data);
        	} catch (Exception $e) {
        		return Response::dataError('Error');
        	}
	    });
    }

    /**
     * Remove the specified resource from storage.
     * @param  $id
     * @return App\Helpers\Response
     */
    public function destroy($id){
        $product = $this->product_repository->find($id);
        if (!$product){
            return Response::dataError('Product not found');
        }
        $product->setDeleter(Auth::user()['uuid']);
        $product->save();

        $product->delete();
        return Response::data();
    }

    /**
     * Update number product
     * @param  AMQP request $request
     * @return App\Helpers\Response
     */
    public function updateNumber($request){
        $validation = Validator::make($request, [
            'product_id' => 'required',
            'number' => 'required|numeric',
            'type' => 'required'
        ]);

        if ($validation->fails()){
            return Response::dataError($validation->messages()->first());
        }

        $product = $this->product_repository->find($request['product_id']);
        if (!$product){
            return Response::dataError('Product not found');
        }

        $tpye = $request['type'];
        if (!in_array($tpye, ['increase', 'decrease'])){
            return Response::dataError('Update type not correct');
        }

        // Increase
        if ($type === 'increase'){
            $product->number = $product->number + $request['number'];
        }

        // Decrease
        if ($type === 'decrease'){
            if ($request['number'] > $product->number){
                return Response::dataError('Update number is larger than the current number');
            }
            $product->number = $product->number - $request['number'];
        }

        $product->save();

        $resource = new ProductResource($product);
        $data = $resource->resolve();

        return Response::data($data);
    }

    public function getByListId($request){
        $validation = Validator::make($request, [
            'list_id' => 'required|array|min:1'
        ]);

        if ($validation->fails()){
            return Response::dataError($validation->messages()->first());
        }

        $products = $this->product_repository->getByListId($request['list_id']);
        $data = [];
        if ($products){
            $collection = ProductResource::collection($products);
            $data = json_decode($collection->toJson(), true);
        }
        return Response::data($data, count($data));
    }
}
