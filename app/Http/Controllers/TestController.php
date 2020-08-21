<?php

namespace App\Http\Controllers;

use Validator;
use App\Products;
use App\Helpers\Auth;
use Webpatser\Uuid\Uuid;
use App\Helpers\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TestResource;
use App\Repositories\TestRepository;

class TestController
{

    protected $test_repository;

    public function __construct(){
        $this->test_repository = new TestRepository();
    }

    /**
     * Display a listing of the resource.
     * @param  AMQP request $request
     * @return App\Helpers\Response
     */
    public function list($request){
        
    }

    /**
     * Store a newly created resource in storage.
     * @param  AMQP request $request
     * @return App\Helpers\Response
     */
    public function store($request){
        $validation = Validator::make($request, [
            'name' => 'required|max:255',
            'detail' => 'required|max:255',
            'price' => 'required|max:50'
        ]);

        if($validation->fails())
        {
            return Response::dataError($validation->messages()->first());
        }

        return DB::transaction(function() use ($request){
            $product = new Products();
            $product->id = (string)Uuid::generate(4);
            $product->name = $request['name'];
            $product->detail = $request['detail'];
            $product->price = Arr::get($request, 'price', 0);
            // $product->setOwner(Auth::user()['uuid']);
            $product->save();

            $resource = new TestResource($product);
            // $data = $resource->resolve();
            return Response::data($resource);
        });
    }

    /**
     * Show the form for editing the specified resource.
     * @param  AMQP request $request
     * @return App\Helpers\Response
     */
    public function show($id){
        //
    }

    /**
     * Update the specified resource in storage.
     * @param  $id
     * @param  AMQP request $request
     * @return App\Helpers\Response
     */
    public function update($id, $request){
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param  $id
     * @return App\Helpers\Response
     */
    public function destroy($id){
        $product = $this->test_repository->find($id);
        if(!$product)
        {
            return Response::dataError('Product not found');
        }

        $product->setDeleter(Auth::user()['uuid']);
        $product->save();

        $product->delete();
        return Response::data();
    }
}
