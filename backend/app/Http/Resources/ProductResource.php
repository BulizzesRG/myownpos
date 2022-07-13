<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
		return [
			'status' => 'success',
			'data' => [
				'product' => [
					'id' => $this->id,
					'description' => $this->description,
					'barcode' => $this->barcode,
					'alternative_code' => $this->alternative_code,
					'purchase_price' => (float) $this->purchase_price,
					'sale_price' => (float) $this->sale_price,
					'int_purchase_price' => (int) ($this->purchase_price * 100),
					'int_sale_price' => (int) ($this->sale_price * 100),
					'format_of_sell' => $this->format_of_sell,
					'is_active' => (int) $this->is_active
				]
			]
		];
    }
}
