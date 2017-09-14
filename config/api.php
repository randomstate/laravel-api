<?php

use League\Fractal\Serializer\DataArraySerializer;
use RandomState\Api\Transformation\Adapters\Fractal\CollectionAdapter;
use RandomState\Api\Transformation\Adapters\Fractal\ItemAdapter;
use RandomState\Api\Transformation\Adapters\Fractal\ScalarAdapter;
use RandomState\LaravelApi\Adapters\Fractal;
use RandomState\LaravelApi\Adapters\Fractal\ExceptionAdapter;
use RandomState\LaravelApi\Adapters\Fractal\PaginatorAdapter;
use RandomState\LaravelApi\Adapters\Fractal\ResponseAdapter;

return [
	/*
	 |------------------------------------------------------------
	 | Namespaces
	 |------------------------------------------------------------
	 |
	 | This section is used for defining your different APIs.
	 | Stick any (Fractal) Transformers you want in here.
	 | Namespaces and Versions are automatically wired up for you.
	 |
	 */
	'namespaces' => [
		'default' => [
			'adapters' => 'fractal',
			'versions' => [
				'current' => [
					'transformers' => [
						// RaffleTicketTransformer::class,
						// RaffleTicketTransformer::class => RaffleTicket::class
						// RaffleTicketTransformer::class => [RaffleTicket::class]
					],
					// 'inherit' => '1.0',
				],
			],
		],
	],

	/*
	 |------------------------------------------------------------
	 | Data Adapters
	 |------------------------------------------------------------
	 |
	 | Specify all adapters here that intercept data and transform
	 | it to a standardized format.
	 |
	 | You can create custom ones by implementing the interface:
	 | RandomState\Api\Transformation\Adapters\Adapter
	 |
	 */
	'adapters'   => [
		'fractal' => [
			'driver'     => Fractal::class,
			'serializer' => DataArraySerializer::class,
			'adapters'   => [
			    ResponseAdapter::class,
                ExceptionAdapter::class,
                PaginatorAdapter::class,
                CollectionAdapter::class,
                ItemAdapter::class,
                ScalarAdapter::class,
			],
		],
	],
];