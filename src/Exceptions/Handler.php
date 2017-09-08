<?php


namespace RandomState\LaravelApi\Exceptions;


use Exception;
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

	public function render($request, Exception $exception)
	{
		// must boot up response factory at this point otherwise it will be too early.

		//TODO if the exception is not mapped as an error in the current version/inheritance tree then just render as normal
        if(! $exception instanceof CustomException)
        {
            return parent::render($request, $exception);
        }

		return $this->container->make(ResponseFactory::class)->build($exception);
	}
}