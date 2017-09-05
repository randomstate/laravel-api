<?php


namespace RandomState\LaravelApi\Http\Routing;


use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Contracts\Support\Responsable;
use ArrayObject;
use Illuminate\Support\Facades\App;
use JsonSerializable;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use RandomState\LaravelApi\Http\Response\ResponseFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Router extends \Illuminate\Routing\Router implements Registrar {

	/**
	 * @var \Illuminate\Routing\Router
	 */
	protected $router;

	public static function prepareResponse($request, $response)
	{
		if($response instanceof Responsable)
		{
			$response = $response->toResponse($request);
		}

		if($response instanceof PsrResponseInterface)
		{
			$response = (new HttpFoundationFactory)->createResponse($response);
		}
		elseif( ! $response instanceof SymfonyResponse)
		{
			$response = App::make(ResponseFactory::class)->build($response);
			$response = new Response($response);
		}
		elseif( ! $response instanceof SymfonyResponse &&
		          ($response instanceof Arrayable ||
		           $response instanceof Jsonable ||
		           $response instanceof ArrayObject ||
		           $response instanceof JsonSerializable ||
		           is_array($response)))
		{
			$response = new JsonResponse($response);
		}

		if($response->getStatusCode() === Response::HTTP_NOT_MODIFIED)
		{
			$response->setNotModified();
		}

		return $response->prepare($request);
	}
}