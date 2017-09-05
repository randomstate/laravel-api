<?php


namespace RandomState\LaravelApi\Exceptions;


use Exception;
use Illuminate\Contracts\Container\Container;
use RandomState\LaravelApi\Http\Response\ResponseFactory;
use RandomState\LaravelApi\Http\Routing\Router;

class Handler extends \App\Exceptions\Handler {

	/**
	 * @var ResponseFactory
	 */
	protected $responseFactory;

	/**
	 * @var Router
	 */
	protected $router;

	public function __construct(Container $container, ResponseFactory $responseFactory, Router $router)
	{
		parent::__construct($container);
//		$this->errorMap = $errorMap;
		$this->responseFactory = $responseFactory;
		$this->router = $router;
	}

	public function render($request, Exception $exception)
	{
		//morph to custom exception if needed
//		$interceptedException = $this->exceptionInterceptor->intercept($exception);
//
//		if($this->getErrorCode($interceptedException) && $this->router->shouldReturnJson($request))
//		{
//			if(! $interceptedException instanceof CustomException)
//			{
//				throw new BaseException("Custom API errors must implement ".CustomException::class. ", " . ($interceptedException ? get_class($interceptedException) : json_encode($interceptedException)) . " was given.");
//			}
//
//			return $this->responseFactory->error($interceptedException);
//		}

		// try to find a transformer for the exception

		return $this->responseFactory->build($exception);

		return parent::render($request, $exception);
	}
}