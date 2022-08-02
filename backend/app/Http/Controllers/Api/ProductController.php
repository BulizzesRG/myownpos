<?php

namespace App\Http\Controllers\Api;

use JsonSerializable;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;

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
	public function update(Product $product, Request $request)
	{
		$request->validate([
			'description' => ['required', 'min:3', 'max:150'],
			'barcode' => ['required', 'alpha_num', 'min:3', 'max:20', 'unique:products,barcode,' . $product->id, 'unique:products,alternative_code'],
			'alternative_code' => ['required', 'alpha_num', 'min:3', 'max:20', 'unique:products,alternative_code,' . $product->id, 'unique:products,barcode,' . $product->id],
			'format_of_sell' => ['required', 'in:pieza,servicio,granel,caja']
		]);
		
		$product->update($request->all());

		return new ProductResource($product);

	}
}
