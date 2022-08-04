<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
			'description' => [
				'required',
				'min:3', 
				'max:150'
			],
			'barcode' => [
				'required', 
				'alpha_num', 
				'min:3', 
				'max:20' , 
				'unique:products,barcode' , 
				'unique:products,alternative_code'
			],
			'alternative_code' => [
				'required' , 
				'alpha_num', 
				'min:3', 
				'max:20', 
				'unique:products,alternative_code', 
				'unique:products,barcode'
			],
			'sale_price' => [
				'required', 
				'numeric' ,
				'min:0.20', 
				'max:1000000'
			],
			'purchase_price' => [
				'required', 
				'numeric'
			],
			'format_of_sell' => [
				'required', 
				'in:pieza,servicio,granel,caja'
			]
        ];
    }
}
