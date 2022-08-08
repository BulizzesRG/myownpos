<?php

namespace Tests\Feature\Api\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaginateProductTest extends TestCase
{

	use RefreshDatabase, WithFaker;
	private User $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = User::factory()->create(['is_staff' => 1])->first();		
	}

    /** @test **/	
    public function can_paginate_products()
    {
		Sanctum::actingAs($this->user);
		$products = Product::factory()->count(6)->create();

		$url = route('api.v1.products.index', [
			'page' => [
				'size' => 2,
				'number' => 2
			]
		]);
		
        $response = $this->get($url);

		$response->assertOk();

		$response->assertSee([
			$products[2]->description,
			$products[3]->description
		]);

		$response->assertDontSee([
			$products[0]->description,
			$products[1]->description,
			$products[4]->description,
			$products[5]->description
		]);
		//dd($response);

		$response->assertJsonStructure([
			'status',
			'data' => [
				'products' => [
					'data',
					'links'
				]
			]
		]);
    }
}
