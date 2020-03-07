<?php


namespace RandomState\LaravelApi\Exceptions;


use Throwable;
use Illuminate\Contracts\Container\Container;
use RandomState\LaravelApi\Exceptions\Exception as CustomException;
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

	public function render($request, Throwable $throwable)
	{
		// must boot up response factory at this point otherwise it will be too early.

		//TODO if the exception is not mapped as an error in the current version/inheritance tree then just render as normal
        if(! $throwable instanceof CustomException)
        {
            return parent::render($request, $throwable);
        }

		return $this->container->make(ResponseFactory::class)->build($throwable);
	}
}