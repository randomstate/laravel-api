<?php


namespace RandomState\Tests\LaravelApi\Model;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use RandomState\LaravelApi\Middleware\ApiNamespace;

class Kernel extends \App\Http\Kernel {

	public function __construct(Application $app, Router $router)
	{
		$this->routeMiddleware = $this->routeMiddleware + ['namespace' => ApiNamespace::class];
		parent::__construct($app, $router);
	}
}