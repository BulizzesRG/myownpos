<?php

namespace Tests\Feature\Api\Product;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateProductTest extends TestCase
{

	use RefreshDatabase, WithFaker;
	private User $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = User::factory()->create(['is_staff' => 1])->first();		
	}

    /** @test **/	
    public function can_update_product()
    {
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create([
			'format_of_sell' => 'pieza'
		]);

		$parameters = [
			'description' => $this->faker()->paragraph(1),
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13(), 
			'format_of_sell' => 'caja'
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$productUpdate = Product::first();

		$response->assertStatus(Response::HTTP_OK);

		$response->assertExactJson([
			'status' => 'success',
			'data' => [
				'product' => [
					'id' => $product->id,
					'description' => $parameters['description'],
					'barcode' => $parameters['barcode'],
					'alternative_code' => $parameters['alternative_code'],
					'purchase_price' => $product->purchase_price,
					'sale_price' => $product->sale_price,
					'int_purchase_price' => (int) ($product->purchase_price * 100),
					'int_sale_price' => (int) ($product->sale_price * 100),
					'format_of_sell' => $parameters['format_of_sell'],					
					'is_active' => (int) $product->is_active
				]
			]
		]);		


		$this->assertEquals($parameters['description'], $productUpdate->description);
		$this->assertEquals($parameters['barcode'], $productUpdate->barcode);
		$this->assertEquals($parameters['alternative_code'], $productUpdate->alternative_code);
		$this->assertEquals($parameters['format_of_sell'], $productUpdate->format_of_sell);

		$this->assertNotEquals($parameters['description'], $product->description);
		$this->assertNotEquals($parameters['barcode'], $product->barcode);
		$this->assertNotEquals($parameters['alternative_code'], $product->alternative_code);
		$this->assertNotEquals($parameters['format_of_sell'], $product->format_of_sell);
    }

	/** @test **/
	public function description_is_required_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();

		$parameters = [
			'barcode' => $product->barcode,
			'alternative_code' => $product->alternative_code, 
			'format_of_sell' => 'caja'
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);
		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['description' => [__('validation.required',['attribute' => __('validation.attributes.description')])]]		
		]);		
	}

    /** @test **/
    public function description_must_be_at_least_3_characters_for_update_a_product()
    {
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => 'ab',
			'barcode' => $code = $this->faker()->ean13(),
			'alternative_code' => $code, 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => [
				'description' => [
					__('validation.min.string', [
						'attribute' => __('validation.attributes.description'),
						'min' => '3'
					])
				]
			]		
		]);
    }
	
	/** @test **/
	public function description_must_have_a_maximum_150_characters_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $this->faker()->paragraph(16),
			'barcode' => $code = $this->faker()->ean13(),
			'alternative_code' => $code, 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => [
				'description' => [
					__('validation.max.string', [
						'attribute' => __('validation.attributes.description'),
						'max' => '150'
					])
				]
			]		
		]);
	}

	/** @test **/
	public function barcode_is_required_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $this->faker()->paragraph(1),
			'barcode' => '',
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => [
				'barcode' => [
					__('validation.required',[
						'attribute' => __('validation.attributes.barcode')
					])
				]
			]		
		]);
	}

	/** @test **/
	public function barcode_must_only_contains_letters_and_numbers_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $this->faker()->paragraph(1),
			'barcode' => $this->faker()->ean13() . '!@',
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['barcode' => [
				__('validation.alpha_num',['attribute' => __('validation.attributes.barcode')])
			]]		
		]);
	}
	
	/** @test **/
	public function barcode_must_be_at_least_3_characters_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $this->faker()->paragraph(1),
			'barcode' => 'ab',
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['barcode' => [
				__('validation.min.string', [
					'attribute' => __('validation.attributes.barcode'),
					'min' => '3'
				])
			]]		
		]);
	}
	
	/** @test **/
	public function barcode_must_have_a_maximum_20_characters_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $this->faker()->paragraph(1),
			'barcode' => $this->faker()->ean13() . $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['barcode' => [
				__('validation.max.string', [
					'attribute' => __('validation.attributes.barcode'),
					'max' => '20'
				])
			]]		
		]);
	}
	
	/** @test **/
	public function barcode_must_be_unique_for_update_a_product()
	{
		Sanctum::actingAs($this->user);
		$oldProduct = Product::factory()->create();

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $this->faker()->paragraph(1),
			'barcode' => $oldProduct->barcode,
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['barcode' => [
				__('validation.unique',['attribute' => __('validation.attributes.barcode')])
			]]		
		]);
	}
	
	/** @test **/
	public function barcode_must_be_different_from_an_existing_alternative_code_for_update_a_product()
	{
		Sanctum::actingAs($this->user);
		$oldProduct = Product::factory()->create();

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $this->faker()->paragraph(1),
			'barcode' => $oldProduct->alternative_code,
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['barcode' => [
				__('validation.unique',['attribute' => __('validation.attributes.barcode')])
			]]		
		]);
	}

	/** @test **/
	public function alternative_code_is_required_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $this->faker()->paragraph(1),
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => '', 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['alternative_code' => [
				__('validation.required',['attribute' => __('validation.attributes.alternative_code')])
			]]		
		]);
	}
	
	/** @test **/
	public function alternative_code_must_only_contains_letters_and_numbers_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $this->faker()->paragraph(1),
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => 'abc#$!', 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['alternative_code' => [
				__('validation.alpha_num',['attribute' => __('validation.attributes.alternative_code')])
			]]		
		]);
	}

	/** @test **/
	public function alternative_code_must_be_at_least_3_characters_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $this->faker()->paragraph(1),
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => 'ab', 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['alternative_code' => [
				__('validation.min.string', [
					'attribute' => __('validation.attributes.alternative_code'),
					'min' => '3'
				])
			]]		
		]);
	}
	
	/** @test **/
	public function alternative_code_must_have_a_maximum_20_characters_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $this->faker()->paragraph(1),
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13() . $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['alternative_code' => [
				__('validation.max.string', [
					'attribute' => __('validation.attributes.alternative_code'),
					'max' => '20'
				])
			]]		
		]);
	}
	
	/** @test **/
	public function alternative_code_must_be_unique_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$oldProduct = Product::factory()->create([
			'format_of_sell' => 'pieza'
		]);

		$product =  Product::factory()->create([
			'format_of_sell' => 'pieza'
		]);     

		$parameters = [
			'description' => $product->description,
			'barcode' => $product->barcode,
			'alternative_code' => $oldProduct->alternative_code, 
			'purchase_price' => $product->purchase_price,
			'sale_price' => $product->sale_price,
			'format_of_sell' => $product->format_of_sell
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['alternative_code' => [
				__('validation.unique',['attribute' => __('validation.attributes.alternative_code')])
			]]		
		]);
	}
	
	/** @test **/
	public function alternative_code_must_be_different_from_an_existing_barcode_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$oldProduct = Product::factory()->create([
			'format_of_sell' => 'pieza'
		]);

		$product =  Product::factory()->create([
			'format_of_sell' => 'pieza'
		]);      

		$parameters = [
			'description' => $product->description,
			'barcode' => $product->barcode,
			'alternative_code' => $oldProduct->barcode, 
			'purchase_price' => $product->purchase_price,
			'sale_price' => $product->sale_price,
			'format_of_sell' => $product->format_of_sell
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['alternative_code' => [
				__('validation.unique',['attribute' => __('validation.attributes.alternative_code')])
			]]		
		]);
	}
	
	/** @test **/
	public function format_of_sell_is_required_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $product->description,
			'barcode' => $product->barcode,
			'alternative_code' => $product->alternative_code, 
			'purchase_price' => $product->purchase_price,
			'sale_price' => $product->sale_price,
			'format_of_sell' => ''
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['format_of_sell' => [
				__('validation.required', [
					'attribute' => __('validation.attributes.format_of_sell')
				])
			]]		
		]);
	}
	
	/** @test **/
	public function format_of_sell_must_be_only_valid_values_for_update_a_product()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();      

		$parameters = [
			'description' => $product->description,
			'barcode' => $product->barcode,
			'alternative_code' => $product->alternative_code, 
			'purchase_price' => $product->purchase_price,
			'sale_price' => $product->sale_price,
			'format_of_sell' => 'no valid'
		];

        $response = $this->putJson(
			route('api.v1.products.update', $product->id),
			$parameters
		);

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['format_of_sell' => [
				__('validation.in', [
					'attribute' => __('validation.attributes.format_of_sell')
				])
			]]		
		]);
	}	
}
