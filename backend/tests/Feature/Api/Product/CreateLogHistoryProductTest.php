<?php

namespace Tests\Feature\Api\Product;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use App\Models\ProductHistory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateLogHistoryProductTest extends TestCase
{

	use RefreshDatabase, WithFaker;
	private User $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = User::factory()->create(['is_staff' => 1])->first();		
	}

    /** @test **/	
    public function can_update_product_purchase_price_and_sale_price()
    {
		Sanctum::actingAs($this->user);
		$product = Product::factory()->create();

		$this->withExceptionHandling();
		
        $response = $this->putJson(route('api.v1.prices.update', $product->id),[
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999), 
			'sale_price' => $salePrice = $purchase + $this->faker()->randomFloat(2,1,2)
		]);

		$response->assertOk();
		$updateProduct = Product::find($product->id);
		$logProduct = ProductHistory::first();

		$this->assertEquals($purchase, $updateProduct->purchase_price);
		$this->assertEquals($salePrice, $updateProduct->sale_price);

		$this->assertDatabaseCount('product_histories',1);

		$this->assertEquals($logProduct->purchase_price, $product->purchase_price);
		$this->assertEquals($logProduct->sale_price, $product->sale_price);
    }

	/** @test **/
	public function purchase_price_is_required_for_update_only_this()
	{
		Sanctum::actingAs($this->user);
		$product = Product::factory()->create();

		$this->withExceptionHandling();
		
        $response = $this->putJson(route('api.v1.prices.update', $product->id),[
			'purchase_price' => '', 
			'sale_price' => $this->faker()->randomFloat(2,1,2)
		]);
		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['purchase_price' => [__('validation.required',['attribute' => __('validation.attributes.purchase_price')])]]		
		]);		
	}
	
	/** @test **/
	public function purchase_price_must_be_numeric_for_update_this()
	{
		Sanctum::actingAs($this->user);
		$product = Product::factory()->create();

		$this->withExceptionHandling();
		
        $response = $this->putJson(route('api.v1.prices.update', $product->id),[
			'purchase_price' => 'veinte pesos', 
			'sale_price' => $this->faker()->randomFloat(2,1,2)
		]);
		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => [
				'purchase_price' => [
					__('validation.numeric',[
						'attribute' => __('validation.attributes.purchase_price')
					])
				]
			]		
		]);		
	}

	/** @test **/
	public function purchase_price_must_be_greater_than_twenty_cents_for_update_this()
	{
		Sanctum::actingAs($this->user);
		$product = Product::factory()->create();

		$this->withExceptionHandling();

        $response = $this->putJson(route('api.v1.prices.update', $product->id),[
			'purchase_price' => 0.01, 
			'sale_price' => $this->faker()->randomFloat(2,1,2)
		]);
		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['purchase_price' => [
				__('validation.min.numeric', [
					'attribute' => __('validation.attributes.purchase_price'),
					'min' => '0.20'
				])
			]]		
		]);
	}
	
	/** @test **/
	public function purchase_price_must_be_less_than_one_million_for_update_this()
	{
		Sanctum::actingAs($this->user);
		$product = Product::factory()->create();

		$this->withExceptionHandling();

        $response = $this->putJson(route('api.v1.prices.update', $product->id),[
			'purchase_price' => 1000000.01, 
			'sale_price' => $this->faker()->randomFloat(2,1,2)
		]);		

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['purchase_price' => [
				__('validation.max.numeric', [
					'attribute' => __('validation.attributes.purchase_price'),
					'max' => '1000000'
				])
			]]		
		]);			
	}

	/** @test **/
	public function sale_price_is_required_for_update_only_this()
	{
		Sanctum::actingAs($this->user);
		$product = Product::factory()->create();

		$this->withExceptionHandling();
		
        $response = $this->putJson(route('api.v1.prices.update', $product->id),[
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999), 
			'sale_price' => ''
		]);
		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => [
				'sale_price' => [
					__('validation.required',[
						'attribute' => __('validation.attributes.sale_price')
					])
				]
			]		
		]);		
	}

	/** @test **/
	public function sale_price_must_be_numeric_for_update_this()
	{
		Sanctum::actingAs($this->user);
		$product = Product::factory()->create();

		$this->withExceptionHandling();
		
        $response = $this->putJson(route('api.v1.prices.update', $product->id),[
			'purchase_price' => $this->faker()->randomFloat(2,1,2), 
			'sale_price' => 'dos pesos'
		]);
		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => [
				'sale_price' => [
					__('validation.numeric',[
						'attribute' => __('validation.attributes.sale_price')
					])
				]
			]		
		]);		
	}

	/** @test **/
	public function sale_price_must_be_greater_than_twenty_cents_for_update_this()
	{
		Sanctum::actingAs($this->user);
		$product = Product::factory()->create();

		$this->withExceptionHandling();

        $response = $this->putJson(route('api.v1.prices.update', $product->id),[
			'purchase_price' => $this->faker()->randomFloat(2,1,2), 
			'sale_price' => 0.01
		]);
		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['sale_price' => [
				__('validation.min.numeric', [
					'attribute' => __('validation.attributes.sale_price'),
					'min' => '0.20'
				])
			]]		
		]);
	}
	
	/** @test **/
	public function sale_price_must_be_less_than_one_million_for_update_this()
	{
		Sanctum::actingAs($this->user);
		$product = Product::factory()->create();

		$this->withExceptionHandling();

        $response = $this->putJson(route('api.v1.prices.update', $product->id),[
			'purchase_price' => $this->faker()->randomFloat(2,1,2), 
			'sale_price' => 1000000.01
		]);		

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['sale_price' => [
				__('validation.max.numeric', [
					'attribute' => __('validation.attributes.sale_price'),
					'max' => '1000000'
				])
			]]		
		]);			
	}

	/** @test **/
	// public function sale_price_must_be_greater_than_purchase_price()
	// {
	// 	Sanctum::actingAs($this->user);
	// 	$product = Product::factory()->create();

	// 	$this->withExceptionHandling();

    //     $response = $this->putJson(route('api.v1.prices.update', $product->id),[
	// 		'purchase_price' => $purchasePrice = 1000, 
	// 		'sale_price' => 999
	// 	]);		

	// 	$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
	// 	$response->assertExactJson([
	// 		'status' => 'fail',
	// 		'data' => ['sale_price' => [
	// 			__('validation.gt.numeric', [
	// 				'attribute' => __('validation.attributes.sale_price'),
	// 				'value' => $purchasePrice
	// 			])
	// 		]]		
	// 	]);
	// }
}
