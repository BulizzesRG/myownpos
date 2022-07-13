<?php

namespace Tests\Feature\Api\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
	use RefreshDatabase, WithFaker;
	private User $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = User::factory()->create(['is_staff' => 1])->first();		
	}

    /** @test **/
    public function can_create_new_product()
    {
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => $this->faker()->sentence(),
			'barcode' => $code = $this->faker()->ean13(),
			'alternative_code' => $code, 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja']),
			'is_active' => 1
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		$product = Product::first();

		$response->assertCreated();
		$response->assertExactJson([
			'status' => 'success',
			'data' => [
				'product' => [
					'id' => $product->id,
					'description' => $parameters['description'],
					'barcode' => $parameters['barcode'],
					'alternative_code' => $parameters['alternative_code'],
					'purchase_price' => $parameters['purchase_price'],
					'sale_price' => $parameters['sale_price'],
					'int_purchase_price' => (int) ($parameters['purchase_price'] * 100),
					'int_sale_price' => (int) ($parameters['sale_price'] * 100),
					'format_of_sell' => $parameters['format_of_sell'],					
					'is_active' => $product->is_active
				]
			]
		]);

		$this->assertCount(1, Product::all());
		$this->assertEquals($product->description, $parameters['description']);
    }

	/** @test **/
	public function description_is_required_for_create_a_product()
	{
		Sanctum::actingAs($this->user);
		
		$parameters = [
			'barcode' => $code = $this->faker()->ean13(),
			'alternative_code' => $code, 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['description' => [__('validation.required',['attribute' => __('validation.attributes.description')])]]		
		]);
	}

    /** @test **/
    public function description_must_be_at_least_3_characters_for_create_a_product()
    {
        /** @var Authenticatable $adminUser  */
        Sanctum::actingAs($this->user);       

		$parameters = [
			'description' => 'ab',
			'barcode' => $code = $this->faker()->ean13(),
			'alternative_code' => $code, 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));	

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
    public function description_must_have_a_maximum_150_characters_for_create_a_product()
    {
        /** @var Authenticatable $adminUser  */
        Sanctum::actingAs($this->user);       

		$parameters = [
			'description' => $this->faker()->paragraph(6),
			'barcode' => $code = $this->faker()->ean13(),
			'alternative_code' => $code, 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));	

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
	public function barcode_is_required_for_create_a_product()
	{
		Sanctum::actingAs($this->user);
		
		$parameters = [
			'description' => 'product a',
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['barcode' => [__('validation.required',['attribute' => __('validation.attributes.barcode')])]]		
		]);
	}

    /** @test **/
    public function barcode_must_only_contains_letters_and_numbers()
    {
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => 'abc'.'@%-_',
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['barcode' => [
				__('validation.alpha_num',['attribute' => __('validation.attributes.barcode')])
			]]		
		]);		
	}
	
	/** @test **/
	public function barcode_must_be_at_least_3_characters()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => 'ab',
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		
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
	public function barcode_must_have_a_maximum_20_characters()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(). $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));
		
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
	public function barcode_must_be_unique()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();

		$parameters = [
			'description' => 'product a',
			'barcode' => $product->barcode,
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['barcode' => [
				__('validation.unique',['attribute' => __('validation.attributes.barcode')])
			]]		
		]);		
	}

	/** @test **/
	public function barcode_must_be_different_from_an_existing_alternative_code()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();

		$parameters = [
			'description' => 'product a',
			'barcode' => $product->alternative_code,
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['barcode' => [
				__('validation.unique',['attribute' => __('validation.attributes.barcode')])
			]]		
		]);
	}

	/** @test **/
	public function alternative_code_is_required()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['alternative_code' => [
				__('validation.required',['attribute' => __('validation.attributes.alternative_code')])
			]]		
		]);		
	}

    /** @test **/
    public function alternative_code_must_only_contains_letters_and_numbers()
    {
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => 'abc'.'@%-_',
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['alternative_code' => [
				__('validation.alpha_num',['attribute' => __('validation.attributes.alternative_code')])
			]]		
		]);		
	}

	/** @test **/
	public function alternative_code_must_be_at_least_3_characters()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => 'ab', 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		
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
	public function alternative_code_must_have_a_maximum_20_characters()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13() . $this->faker()->ean13(), 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));
		
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
	public function alternative_code_must_be_unique()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $product->alternative_code, 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['alternative_code' => [
				__('validation.unique',['attribute' => __('validation.attributes.alternative_code')])
			]]		
		]);		
	}
	
	/** @test **/
	public function alternative_code_must_be_different_from_an_existing_barcode()
	{
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create();

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $product->barcode, 
			'purchase_price' => $purchase = $this->faker()->randomFloat(2,1,999999),
			'sale_price' => $purchase + $this->faker()->randomFloat(2,1,2),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));

		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['alternative_code' => [
				__('validation.unique',['attribute' => __('validation.attributes.alternative_code')])
			]]		
		]);
	}

	/** @test **/
	public function sale_price_is_required()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $this->faker()->randomFloat(2,1,999999),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));
		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['sale_price' => [
				__('validation.required', [
					'attribute' => __('validation.attributes.sale_price')
				])
			]]		
		]);
	}

	/** @test **/
	public function sale_price_must_be_numeric()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $this->faker()->randomFloat(2,1,999999),
			'sale_price' => '0.00a',
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));
		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['sale_price' => [
				__('validation.numeric', [
					'attribute' => __('validation.attributes.sale_price')
				])
			]]		
		]);
	}	

	/** @test **/
	public function sale_price_must_be_greater_than_twenty_cents()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $this->faker()->randomFloat(2,1,999999),
			'sale_price' => 0.01,
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));
		
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
	public function sale_price_must_be_less_than_one_million()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13(), 
			'purchase_price' => $this->faker()->randomFloat(2,1,999999),
			'sale_price' => 1000000.01,
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));
		
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
	public function purchase_price_is_required()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13(),
			'sale_price' => $this->faker()->randomFloat(2,1,999999),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));
		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['purchase_price' => [
				__('validation.required', [
					'attribute' => __('validation.attributes.purchase_price')
				])
			]]		
		]);
	}

	/** @test **/
	public function purchase_price_must_be_numeric()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13(),
			'purchase_price' => '12an',
			'sale_price' => $this->faker()->randomFloat(2,1,999999),
			'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));
		
		$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
		$response->assertExactJson([
			'status' => 'fail',
			'data' => ['purchase_price' => [
				__('validation.numeric', [
					'attribute' => __('validation.attributes.purchase_price')
				])
			]]		
		]);
	}
	
	// /** @test **/
	// public function purchase_price_must_be_less_than_sale_price()
	// {
	// 	Sanctum::actingAs($this->user);

	// 	$parameters = [
	// 		'description' => 'product a',
	// 		'barcode' => $this->faker()->ean13(),
	// 		'alternative_code' => $this->faker()->ean13(),
	// 		'purchase_price' => $this->faker()->randomFloat(2,99990,99996),
	// 		'sale_price' => $salePrice = $this->faker()->randomFloat(2,1,99989),
	// 		'format_of_sell' => $this->faker()->randomElement(['pieza','servicio', 'granel', 'caja'])
	// 	];

    //     $response = $this->postJson(
	// 		route('api.v1.products.store',
	// 		$parameters
	// 	));
		
	// 	$response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
	// 	$response->assertExactJson([
	// 		'status' => 'fail',
	// 		'data' => ['purchase_price' => [
	// 			__('validation.max.numeric', [
	// 				'attribute' => __('validation.attributes.purchase_price'),
	// 				'max' => $salePrice
	// 			])
	// 		]]		
	// 	]);
	// }
	
	/** @test **/
	public function format_of_sell_is_required()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13(),
			'purchase_price' => $purchasePrice = $this->faker()->randomFloat(2,99990,99996),
			'sale_price' => $purchasePrice + $this->faker()->randomFloat(2,1,3)
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));
		
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
	public function format_of_sell_must_be_only_valid_values()
	{
		Sanctum::actingAs($this->user);

		$parameters = [
			'description' => 'product a',
			'barcode' => $this->faker()->ean13(),
			'alternative_code' => $this->faker()->ean13(),
			'purchase_price' => $purchasePrice = $this->faker()->randomFloat(2,99990,99996),
			'sale_price' => $purchasePrice + $this->faker()->randomFloat(2,1,3),
			'format_of_sell' => 'invalid'
		];

        $response = $this->postJson(
			route('api.v1.products.store',
			$parameters
		));
		
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
