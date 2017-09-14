<?php


namespace RandomState\Tests\LaravelApi\Feature\Transformation;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use League\Fractal\Serializer\DataArraySerializer;
use League\Fractal\TransformerAbstract;
use RandomState\Api\Api;
use RandomState\Api\Namespaces\CustomNamespace;
use RandomState\Api\Namespaces\Manager;
use RandomState\Api\Transformation\Adapters\Fractal\ItemAdapter;
use RandomState\Api\Transformation\Fractal\Switchboard;
use RandomState\LaravelApi\Adapters\Fractal;
use RandomState\LaravelApi\Adapters\Fractal\ResponseAdapter;
use RandomState\LaravelApi\LaravelApiServiceProvider;
use RandomState\Tests\LaravelApi\Model\OldUserTransformer;
use RandomState\Tests\LaravelApi\Model\User;
use RandomState\Tests\LaravelApi\TestCase;

class CanDependencyInjectTest extends TestCase {

	/**
	 * @test
	 */
	public function can_inject_fractal_switchboard_into_transformer()
	{
		$this->app->register(LaravelApiServiceProvider::class);
		$this->app['config']->set('api.namespaces.default.versions', [
			'latest' => [
				'transformers' => [
					InjectableTransformer::class,
				]
			],
		]);

		Route::get('/', InjectController::class . '@inject')->middleware('namespace:default');

		$this->assertInstanceOf(Switchboard::class, $switchboard = $this->get('/')->getOriginalContent()['data']['switchboard']);
	}

	/**
	 * @test
	 */
	public function can_inject_current_namespace_into_transformer()
	{
		$config = $this->app->make('config');
		$config->set('api.adapters.fractal', [
			'driver'   => Fractal::class,
			'serializer' => DataArraySerializer::class,
			'adapters' => [
			    ResponseAdapter::class,
				ItemAdapter::class,
			],
		]);

		$this->app->make('config')->set('api.namespaces.web', [
			'adapters' => 'fractal',
			'versions' => [
				'current' => [
					'transformers' => [
						NamespaceTransformer::class,
					]
				],
			],
		]);

		$this->app->register(LaravelApiServiceProvider::class);
		$this->withoutExceptionHandling();

		Route::get('/', InjectController::class . '@inject')->middleware('namespace:web');

		$this->assertInstanceOf(CustomNamespace::class, $switchboard = $this->get('/')->getOriginalContent()['data']['namespace']);
		$this->assertEquals($this->app->make(Manager::class)->getNamespace('web'), $switchboard = $this->get('/')->getOriginalContent()['data']['namespace']);
	}
}

class InjectController extends Controller {

	public function inject()
	{
		return new User();
	}
}

class InjectableTransformer extends TransformerAbstract {

	/**
	 * @var Switchboard
	 */
	protected $switchboard;

	public function __construct(Switchboard $switchboard)
	{
		$this->switchboard = $switchboard;
	}

	public function transform(User $user)
	{
		return ['switchboard' => $this->switchboard];
	}

	/**
	 * @return Switchboard
	 */
	public function getSwitchboard()
	{
		return $this->switchboard;
	}
}

class NamespaceTransformer extends TransformerAbstract {

	/**
	 * @var Api
	 */
	protected $api;

	public function __construct(Api $api)
	{
		$this->api = $api;
	}

	public function transform(User $user)
	{
		return ['namespace' => $this->api];
	}
}