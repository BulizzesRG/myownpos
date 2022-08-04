<?php

namespace App\Http\Controllers\Api;

use JsonSerializable;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Product\StoreProductRequest;

class ProductController extends Controller
{
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
}
