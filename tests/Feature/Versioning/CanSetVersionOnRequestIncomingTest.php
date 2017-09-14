<?php


namespace RandomState\Tests\LaravelApi\Feature\Versioning;


use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Route;
use League\Fractal\Serializer\DataArraySerializer;
use League\Fractal\TransformerAbstract;
use RandomState\Api\Api;
use RandomState\Api\Transformation\Adapters\Fractal\ItemAdapter;
use RandomState\Api\Versioning\Manager;
use RandomState\Api\Versioning\Version;
use RandomState\LaravelApi\Adapters\Fractal;
use RandomState\LaravelApi\Adapters\Fractal\ResponseAdapter;
use RandomState\LaravelApi\LaravelApiServiceProvider;
use RandomState\LaravelApi\Versioning\ForcedVersion;
use RandomState\LaravelApi\VersionSwitch;
use RandomState\Tests\LaravelApi\TestCase;

class CanSetVersionOnRequestIncomingTest extends TestCase {

	/**
	 * @test
	 */
	public function can_set_version_using_middleware()
	{
	    $this->markTestSkipped("Versioning does not currently work due to exceptions thrown by fractal. Working on a solution");
		$this->withoutExceptionHandling();

		$config = $this->app->make('config');
		$config->set('api.adapters.fractal', [
			'driver'     => Fractal::class,
			'serializer' => DataArraySerializer::class,
			'adapters'   => [
			    ResponseAdapter::class,
				ItemAdapter::class,
			],
		]);

		$this->app->make('config')->set('api.namespaces.web', [
			'adapters' => 'fractal',
			'versions' => [
				'1' => [
					'transformers' => [
						VersionSwitchTransformer::class => [ForcedVersion::class],
					],
				],
				'2' => [
					'inherit'      => '1',
					'transformers' => [
					],
				],
			],
		]);

		$this->app->register(LaravelApiServiceProvider::class);

		Route::get('/v1', VersionController::class . "@version")->middleware('namespace:web,1');
		Route::get('/v2', VersionController::class . "@version")->middleware('namespace:web,2');

		$v1 = $this->get('/v1');
		$v1->assertJson(
			['data' => ['version' => '1.0']]
		);;

		$v2 = $this->get('/v2');
		$v2->assertJson(
			['data' => ['version' => '2.0']]
		);
	}

}

class VersionController extends Controller {

	public function version(VersionSwitch $switch)
	{
		return $switch;
	}
}

class VersionSwitchTransformer extends TransformerAbstract {

	/**
	 * @var Manager
	 */
	protected $manager;

	public function __construct(Api $api)
	{
		$this->manager = $api->versions();
	}

	public function transform(VersionSwitch $switch)
	{
		return [
			'version' => $switch->getVersionIdentifier(),
		];
	}
}