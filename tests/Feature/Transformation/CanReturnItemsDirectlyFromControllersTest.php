<?php


namespace RandomState\Tests\LaravelApi\Feature\Transformation;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use RandomState\Tests\LaravelApi\Feature\Namespacing\Entity;
use RandomState\Tests\LaravelApi\TestCase;

class CanReturnItemsDirectlyFromControllersTest extends TestCase {

//	/**
//	 * @test
//	 */
//	public function can_return_entity_from_controller_using_default_transformers()
//	{
//		Route::get('/', EntityController::class . '@show');
//
//		$response = $this->get('/');
//
//
//		$this->assertEquals([
//			'data' => [
//				'name' => 'john',
//			],
//		], $response->decodeResponseJson());
//	}
}

class EntityController extends Controller {

	public function show()
	{
		return new Entity();
	}
}