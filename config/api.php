<?php

use League\Fractal\Serializer\DataArraySerializer;
use RandomState\Api\Transformation\Adapters\Fractal\CollectionAdapter;
use RandomState\Api\Transformation\Adapters\Fractal\ItemAdapter;
use RandomState\Api\Transformation\Adapters\Fractal\ScalarAdapter;
use RandomState\LaravelApi\Adapters\Fractal;

return [
	'adapters' => [
		'fractal' => [
			'driver' => Fractal::class,
			'serializer' => DataArraySerializer::class,
			'adapters' => [
				CollectionAdapter::class,
				ItemAdapter::class,
				ScalarAdapter::class,
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