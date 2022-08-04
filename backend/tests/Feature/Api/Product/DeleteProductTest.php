<?php

namespace Tests\Feature\Api\Product;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteProductTest extends TestCase
{

	use RefreshDatabase, WithFaker;
	private User $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = User::factory()->create(['is_staff' => 1])->first();		
	}

    /** @test **/	
    public function can_delete_product()
    {
		Sanctum::actingAs($this->user);

		$product =  Product::factory()->create([
			'format_of_sell' => 'pieza'
		]);		
		
		$response = $this->deleteJson(
			route('api.v1.products.delete', $product->id)
		);

		$response->assertExactJson([
			'status' => 'success',
			'data' => []
		]);

		$this->assertDatabaseCount('products',1);

		$this->assertSoftDeleted('products',[
			'id' => $product->id
		]);
        // $response = $this->get('/');
        // $response->assertStatus(200);
    }
}
