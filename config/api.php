<?php

use League\Fractal\Serializer\DataArraySerializer;
use RandomState\Api\Transformation\Adapters\Fractal\CollectionAdapter;
use RandomState\Api\Transformation\Adapters\Fractal\ItemAdapter;
use RandomState\Api\Transformation\Adapters\Fractal\ScalarAdapter;
use RandomState\LaravelApi\Adapters\Fractal;
use RandomState\LaravelApi\Adapters\Fractal\ExceptionAdapter;
use RandomState\LaravelApi\Adapters\Fractal\PaginatorAdapter;

return [
	'adapters' => [
		'fractal' => [
			'driver' => Fractal::class,
			'serializer' => DataArraySerializer::class,
			'adapters' => [
				PaginatorAdapter::class,
				CollectionAdapter::class,
				ItemAdapter::class,
				ScalarAdapter::class,
				ExceptionAdapter::class,
			]
		]
	],
	'namespaces' => [
		'default' => [
			'adapters' => 'fractal',
			'versions' => [
				'current' => [
					// RaffleTicketTransformer::class,
				]
			]
		]
	]
];