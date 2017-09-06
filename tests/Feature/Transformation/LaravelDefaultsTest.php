<?php


namespace RandomState\Tests\LaravelApi\Feature\Transformation;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use RandomState\LaravelApi\LaravelApiServiceProvider;
use RandomState\Tests\LaravelApi\TestCase;

class LaravelDefaultsTest extends TestCase {

	protected function setUp()
	{
		parent::setUp();
		$this->app->register(LaravelApiServiceProvider::class);
	}

	/**
	 * @test
	 */
	public function normal_response_is_not_affected_by_transformation()
	{
		Route::get('/normal', DefaultsController::class . '@response')->middleware('namespace:default');

		$response = $this->get('/normal');
		$this->assertEquals("test normal response", $response->getContent());
	}

	/**
	 * @test
	 */
	public function routes_not_in_a_namespace_are_unaffected()
	{
		Route::get('/outwith', DefaultsController::class . '@outwithNamespace');
		Route::get('/view', DefaultsController::class . '@baseView');

		$response = $this->get('/outwith');
		$response->assertStatus(422);

		$response = $this->get('/view');
		$response->assertStatus(200);
	}
}

class DefaultsController extends Controller {

	public function response()
	{
		return response("test normal response");
	}

	public function outwithNamespace()
	{
		return response('test', 422);
	}

	public function baseView()
	{
		return view('welcome');
	}
}