<?php

namespace Tests\Feature\Api\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FilterProductTest extends TestCase
{

	use RefreshDatabase, WithFaker;
	private User $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = User::factory()->create(['is_staff' => 1])->first();		
	}

    /** @test **/	
    public function can_filter_product_by_term_of_search()
    {
		Sanctum::actingAs($this->user);
		$this->artisan('scout:flush', ['model' => 'App\Models\Product']);

		Product::factory()->create([
			'description' => 'Coca cola 3L'
		]);

		Product::factory()->create([
			'description' => 'Paleta Payaso'
		]);

		Product::factory()->create([
			'description' => 'Conchas bimbo'
		]);

		Product::factory()->create([
			'description' => 'Pelon pelo rico'
		]);

		$route = route('api.v1.products.index',[
			'query' => 'Pelon'
		]);
		sleep(1);

        $response = $this->getJson($route)
		->assertJsonCount(1,'data.products.data')
		->assertSee('Pelon pelo rico');
        $response->assertOk();
    }
}
