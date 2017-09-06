<?php


namespace RandomState\LaravelApi\Http\Routing;


use Illuminate\Contracts\Routing\Registrar;
use RandomState\LaravelApi\Http\Response\ResponseFactory;

class Router extends \Illuminate\Routing\Router implements Registrar {

	/**
	 * @var \Illuminate\Routing\Router
	 */
	protected $router;

	public static function prepareResponse($request, $response)
	{
		$response = app(ResponseFactory::class)->build($response);

		return parent::prepareResponse($request,$response);
	}
}