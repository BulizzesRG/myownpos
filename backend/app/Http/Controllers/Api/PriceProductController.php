<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductHistory;
use Illuminate\Support\Facades\Auth;

class PriceProductController extends Controller
{
    public function update(Product $product, Request $request)
	{
		$request->validate([
			'purchase_price' => ['required', 'numeric', 'min:0.20', 'max:1000000'],
			'sale_price' => ['required', 'numeric', 'min:0.20', 'max:1000000' ]
		]);

		ProductHistory::create([
			'information' => json_encode($product->toArray()),
			'sale_price' => $product->sale_price ,
			'purchase_price' => $product->purchase_price ,
			'system_purchase_price' => $product->purchase_price ,
			'user_id' => Auth::user()->id ,
			'product_id' => $product->id,			
		]);		

		$product->update([
			'purchase_price' => $request->input('purchase_price'),
			'sale_price' => $request->input('sale_price')
		]);
		
		return response()->json([
			'status' => 'success',
			'data' => [
				'product' => $product
			]
		]);
	}
}
