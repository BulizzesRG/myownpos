<?php

namespace App\Http\Controllers\Api;

use JsonSerializable;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Product\StoreProductRequest;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
	public function index()
	{
		if(request()->has('query')){
			$products = Product::search(request()->input('query'))
			->paginate(
				$perPage = request('page.size',10),
				$pageName = 'page[number]',
				$page = request('page.number',1)
			)->appends(request()->only('page.size'));
		}
		else{
			$products = Product::query()->paginate(
				$perPage = request('page.size',10),
				$columns = ['*'],
				$pageName = 'page[number]',
				$currentPage = request('page.number',1)				
			)->appends(request()->only('page.size'));
		}

		return response()->json([
			'status' => 'success', 
			'data' => [
				'products' => $products
			]
		]);
	}
	
	/**
	 * show product resource
	 *
	 * @param  \App\Models\Product $product
	 * @return array|\Illuminate\Contracts\Support\JsonSerializable
	 */
	public function show(Product $product): JsonSerializable
	{
		return new ProductResource($product);
	}
    /**
     * Store a newly created resource in products.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return array|\Illuminate\Contracts\Support\JsonSerializable
     */
    public function store(StoreProductRequest $request): JsonSerializable
	{
		$product = Product::create($request->all());
		return new ProductResource($product);
	}

    /**
     * Update the specified resource in products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
	public function update(Product $product, UpdateProductRequest $request)
	{
		$product->update($request->validated());
		return new ProductResource($product);
	}

		
	/**
	 * destroy a specified resource product
	 *
	 * @param  \App\Models\Product $product
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function destroy(Product $product): JsonResponse 
	{
		$product->delete();

		return response()->json([
			'status' => 'success',
			'data' => []
		]);
	}
}
