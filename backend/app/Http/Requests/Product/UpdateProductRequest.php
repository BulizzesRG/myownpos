<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
				'max:20', 
				'unique:products,barcode,' . $this->product->id, 
				'unique:products,alternative_code'
			],
			'alternative_code' => [
				'required', 
				'alpha_num', 
				'min:3', 
				'max:20', 
				'unique:products,alternative_code,' . $this->product->id, 
				'unique:products,barcode,' . $this->product->id
			],
			'format_of_sell' => [
				'required', 
				'in:pieza,servicio,granel,caja'
			]
        ];
    }
}
