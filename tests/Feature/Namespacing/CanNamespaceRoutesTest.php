<?php


namespace RandomState\Tests\LaravelApi\Feature\Namespacing;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use League\Fractal\TransformerAbstract;
use RandomState\Api\Api;
use RandomState\Api\Namespaces\CustomNamespace;
use RandomState\Api\Namespaces\Manager;
use RandomState\LaravelApi\LaravelApiServiceProvider;
use RandomState\Tests\LaravelApi\TestCase;

class CanNamespaceRoutesTest extends TestCase {

	/**
	 * @test
	 */
	public function namespace_interface_defaults_to_default_namespace()
	{
		$this->app->register(LaravelApiServiceProvider::class);

		$this->assertInstanceOf(CustomNamespace::class, $this->app->make(Api::class));
	    $this->assertEquals($this->app->make(Manager::class)->getNamespace('default'), $this->app->make(Api::class));
	}

	/**
	 * @test
	 */
	public function can_allocate_a_route_to_a_namespace()
	{
		$this->withoutExceptionHandling();

		$this->app->make('config')->set('api.namespaces', [
			'web' => [
				'adapters' => 'fractal',
				'versions' => [
					'latest' => [
						WebEntityTransformer::class,
					]
				]
			],
			'api' => [
				'adapters' => 'fractal',
				'versions' => [
					'latest' => [
						ApiEntityTransformer::class
					],
				]
			]
		]);

		$this->app->register(LaravelApiServiceProvider::class);

		Route::get('/v1', NamespaceController::class . '@test')->middleware('namespace:web');
		Route::get('/v2', NamespaceController::class . '@test')->middleware('namespace:api');

		$web = $this->get('/v1');
		$api = $this->get('/v2');

		$web->assertJsonStructure(['data' => ['name']]);
		$api->assertJsonStructure(['data' => ['name', 'apiToken']]);
	}
}

class Entity {
	public $name = 'john';
	public $apiToken = 'abdefghijk';
}

class WebEntityTransformer extends TransformerAbstract {

	public function transform(Entity $entity)
	{
		return [
			'name' => $entity->name
		];
	}
}

class ApiEntityTransformer extends TransformerAbstract {

	public function transform(Entity $entity)
	{
		return [
			'name' => $entity->name,
			'apiToken' => $entity->apiToken
		];
	}
}

class NamespaceController extends Controller {

	public function test(Request $request)
	{
		return new Entity();
	}
}