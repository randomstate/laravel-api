<?php


namespace RandomState\Tests\LaravelApi\Feature\Transformation;


use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use League\Fractal\Serializer\DataArraySerializer;
use League\Fractal\TransformerAbstract;
use RandomState\Api\Transformation\Adapters\Fractal\CollectionAdapter;
use RandomState\Api\Transformation\Adapters\Fractal\ItemAdapter;
use RandomState\LaravelApi\Adapters\Fractal;
use RandomState\LaravelApi\Adapters\Fractal\PaginatorAdapter;
use RandomState\LaravelApi\LaravelApiServiceProvider;
use RandomState\Tests\LaravelApi\Model\AnotherEntity;
use RandomState\Tests\LaravelApi\Model\AnotherEntityTransformer;
use RandomState\Tests\LaravelApi\Model\OldUserTransformer;
use RandomState\Tests\LaravelApi\Model\User;
use RandomState\Tests\LaravelApi\TestCase;

class PaginationTest extends TestCase {

	/**
	 * @test
	 */
	public function can_transform_paginator_into_meta_response()
	{
		$this->app->make('config')->set('api.adapters', [
			'fractal' => [
				'driver'     => Fractal::class,
				'serializer' => DataArraySerializer::class,
				'adapters'   => [
					PaginatorAdapter::class,
					CollectionAdapter::class,
					ItemAdapter::class,
				],
			],
		]);

		$this->app->make('config')->set('api.namespaces', [
			'default' => [
				'adapters' => 'fractal',
				'versions' => [
					'latest' => [
						'transformers' => [
							AnotherEntityTransformer::class,
							OldUserTransformer::class,
						]
					],
				],
			],
		]);

		$this->app->register(LaravelApiServiceProvider::class);
		Route::get('/', PaginationController::class . "@paginate")->middleware('namespace:default');

		$this->withoutExceptionHandling();

		$response = $this->get('/');

		$response->assertJsonStructure(
			[
				'data' => [
					['name'],
					['name'],
					['entity'],
				],
				'meta' => [
					'pagination' => [
						"total",
						"count",
						"per_page",
						"current_page",
						"total_pages",
						"links",
					],
				],
			]
		);
	}
}

class PaginationController extends Controller {

	public function paginate()
	{
		return new LengthAwarePaginator(
			[new User(), new User(), new AnotherEntity()],
			3,
			3
		);
	}
}