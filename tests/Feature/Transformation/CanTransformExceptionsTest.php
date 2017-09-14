<?php


namespace RandomState\Tests\LaravelApi\Feature\Transformation;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use League\Fractal\Serializer\DataArraySerializer;
use RandomState\Api\Transformation\Adapters\Fractal\ItemAdapter;
use RandomState\Api\Transformation\Adapters\Fractal\ScalarAdapter;
use RandomState\Api\Transformation\Fractal\ScalarTransformer;
use RandomState\LaravelApi\Adapters\Fractal;
use RandomState\LaravelApi\Adapters\Fractal\ExceptionAdapter;
use RandomState\LaravelApi\Adapters\Fractal\ResponseAdapter;
use RandomState\LaravelApi\Exceptions\Exception;
use RandomState\LaravelApi\LaravelApiServiceProvider;
use RandomState\Tests\LaravelApi\Model\OldUserTransformer;
use RandomState\Tests\LaravelApi\Model\User;
use RandomState\Tests\LaravelApi\TestCase;

class CanTransformExceptionsTest extends TestCase {

	/**
	 * @test
	 */
	public function can_transform_thrown_exception()
	{
		$config = $this->app->make('config');
		$config->set('api.adapters.fractal', [
			'driver'   => Fractal::class,
			'serializer' => DataArraySerializer::class,
			'adapters' => [
			    ResponseAdapter::class,
				ExceptionAdapter::class,
                ItemAdapter::class,
            ],
		]);

		$this->app->make('config')->set('api.namespaces.default', [
			'adapters' => 'fractal',
			'versions' => [
				'current' => [
					'transformers' => [
						OldUserTransformer::class,
					]
				],
			],
		]);

		$this->app->register(LaravelApiServiceProvider::class);

		Route::get('/', ExceptionController::class . '@throw')->middleware('namespace:default');

		$response = $this->get('/');

		$response->assertJsonStructure([
			'data',
			'meta' => [
				'errors' => ['400']
			]
		]);
	}

    /**
     * @test
     */
    public function can_transform_thrown_exception_with_null_content()
    {
        $config = $this->app->make('config');
        $config->set('api.adapters.fractal', [
            'driver'   => Fractal::class,
            'serializer' => DataArraySerializer::class,
            'adapters' => [
                ScalarAdapter::class,
                ExceptionAdapter::class,
            ],
        ]);

        $this->app->make('config')->set('api.namespaces.default', [
            'adapters' => 'fractal',
            'versions' => [
                'current' => [
                    'transformers' => [
                        OldUserTransformer::class
                    ]
                ],
            ],
        ]);

        $this->app->register(LaravelApiServiceProvider::class);

        Route::get('/', ExceptionController::class . '@empty')->middleware('namespace:default');

        $response = $this->get('/');

        $response->assertJsonStructure([
            'data',
            'meta' => [
                'errors' => ['400']
            ]
        ]);
    }
}

class ExceptionController extends Controller {

	public function throw()
	{
		throw new class extends \Exception implements Exception {

			public function getContent()
			{
				return new User();
			}

			public function getHttpStatusCode(): int
			{
				return 400;
			}

			public function getMessages(): array
			{
				return [
					"You idiot!"
				];
			}

		};
	}

    public function empty()
    {
        throw new class extends \Exception implements Exception {

            public function getContent()
            {
                return null;
            }

            public function getHttpStatusCode(): int
            {
                return 400;
            }

            public function getMessages(): array
            {
                return [
                    "You idiot!"
                ];
            }

        };
	}
}