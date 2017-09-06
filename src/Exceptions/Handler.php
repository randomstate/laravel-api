<?php


namespace RandomState\LaravelApi\Exceptions;


use Exception;
use Illuminate\Contracts\Container\Container;
use RandomState\LaravelApi\Http\Response\ResponseFactory;
use RandomState\LaravelApi\Http\Routing\Router;

class Handler extends \App\Exceptions\Handler {

	/**
	 * @var Router
	 */
	protected $router;

	public function __construct(Container $container, Router $router)
	{
		parent::__construct($container);
		$this->router = $router;
	}

	public function render($request, Exception $exception)
	{
		// must boot up response factory at this point otherwise it will be too early.
		return $this->container->make(ResponseFactory::class)->build($exception);
	}
}