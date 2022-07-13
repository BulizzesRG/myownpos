<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
			'description' => $this->faker->sentence(),
			'barcode' => $this->faker->ean13(),
			'alternative_code' => $this->faker->ean13(), 
			'purchase_price' => $purchase = $this->faker->randomFloat(2),
			'sale_price' => $purchase + $this->faker->randomFloat(2,1,2),
			'is_active' => true
        ];
    }
}
