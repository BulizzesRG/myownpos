<?php

namespace Tests\Feature\Api\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListProductTest extends TestCase
{

	use RefreshDatabase, WithFaker;
	private User $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = User::factory()->create(['is_staff' => 1])->first();		
	}

    /** @test **/	
    public function can_fetch_a_simple_product()
    {
		Sanctum::actingAs($this->user);

		$product = Product::factory()->create([
			'format_of_sell' => 'pieza'
		]);

        $response = $this->getJson(route('api.v1.products.show', $product->id));
        $response->assertOk();

		$response->assertExactJson([
			'status' => 'success',
			'data' => [
				'product' => [
					'id' => $product->id,
					'description' => $product->description,
					'barcode' => $product->barcode,
					'alternative_code' => $product->alternative_code,
					'purchase_price' => $product->purchase_price,
					'sale_price' => $product->sale_price,
					'int_purchase_price' => (int) ($product->purchase_price * 100),
					'int_sale_price' => (int) ($product->sale_price * 100),
					'format_of_sell' => $product->format_of_sell,			
					'is_active' => (int) $product->is_active
				]
			]
		]);
    }

	/** @test **/
	public function can_fetch_products()
	{
		Sanctum::actingAs($this->user);

		$product = Product::factory()->count(3)->create([
			'format_of_sell' => 'pieza'
		]);

		$response =  $this->getJson(route('api.v1.products.index'));

		$response->assertOk();
		$response->assertJsonStructure([
			'status', 
			'data' => [
				'products' => [
					'*' => [
						'id',
						'description',
						'barcode',
						'sale_price'
					]
				]
			]
		]);
	}

	/** @test **/
	public function it_returns_a_json_api_error_object_when_a_product_is_not_found()
	{
		Sanctum::actingAs($this->user);

		$response = $this->getJson(route('api.v1.products.show', 23));

		$response->assertExactJson([
			'status' => 'fail',
			'data' => __('Not Found')
		])->assertStatus(Response::HTTP_NOT_FOUND);
	}
}
