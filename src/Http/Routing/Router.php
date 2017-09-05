<?php


namespace RandomState\LaravelApi\Http\Routing;


use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use ArrayObject;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\App;
use JsonSerializable;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use RandomState\Api\Api;
use RandomState\Api\Versioning\Version;
use RandomState\LaravelApi\Http\Response\ResponseFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Router extends \Illuminate\Routing\Router implements Registrar {

	/**
	 * @var \Illuminate\Routing\Router
	 */
	protected $router;

//	/**
//	 * Dispatch the request to a route and return the response.
//	 *
//	 * @param  \Illuminate\Http\Request  $request
//	 * @return mixed
//	 */
//	public function dispatchToRoute(Request $request)
//	{
//		// First we will find a route that matches this request. We will also set the
//		// route resolver on the request so middlewares assigned to the route will
//		// receive access to this route instance for checking of the parameters.
//		$route = $this->findRoute($request);
//
//		$request->setRouteResolver(function () use ($route) {
//			return $route;
//		});
//
//		$this->events->dispatch(new RouteMatched($route, $request));
//
//		$response = $this->runRouteWithinStack($route, $request);
//
//		$response = $this->app->make(ResponseFactory::class)->build($response);
//
//		return $this->prepareResponse($request, $response);
//	}

	public static function prepareResponse($request, $response)
	{
		$response = App::make(ResponseFactory::class)->build($response);

		if($response instanceof Responsable)
		{
			$response = $response->toResponse($request);
		}

		if($response instanceof PsrResponseInterface)
		{
			$response = (new HttpFoundationFactory)->createResponse($response);
		} elseif( ! $response instanceof SymfonyResponse &&
		          ($response instanceof Arrayable ||
		           $response instanceof Jsonable ||
		           $response instanceof ArrayObject ||
		           $response instanceof JsonSerializable ||
		           is_array($response)))
		{
			$response = new JsonResponse($response);
		} elseif( ! $response instanceof SymfonyResponse)
		{
			$response = new Response($response);
		}

		if($response->getStatusCode() === Response::HTTP_NOT_MODIFIED)
		{
			$response->setNotModified();
		}

		return $response->prepare($request);
	}
}