<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function store(Request $request)
	{
		$request->validate([
			'description' => ['required', 'min:3', 'max:150'],
			'barcode' => ['required', 'alpha_num', 'min:3', 'max:20' , 'unique:products,barcode' , 'unique:products,alternative_code'],
			'alternative_code' => ['required' , 'alpha_num', 'min:3', 'max:20', 'unique:products,alternative_code', 'unique:products,barcode'],
			'sale_price' => ['required', 'numeric' ,'min:0.20', 'max:1000000'],
			'purchase_price' => ['required', 'numeric'],
			'format_of_sell' => ['required', 'in:pieza,servicio,granel,caja']
		]);
		$product = Product::create($request->all());

		return new ProductResource($product);
	}
}
