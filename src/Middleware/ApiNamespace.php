<?php


namespace RandomState\LaravelApi\Middleware;


use Closure;
use Illuminate\Contracts\Container\Container;
use RandomState\Api\Api;
use RandomState\Api\Namespaces\Manager;
use RandomState\LaravelApi\Versioning\LatestVersion;
use RandomState\LaravelApi\VersionSwitch;

class ApiNamespace {

	/**
	 * @var Container
	 */
	protected $app;

	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @param null $name
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next, $name = null)
	{
		$this->app->bind(Api::class, function() use($name) {
			return $this->app->make(Manager::class)->getNamespace($name);
		});

		return $next($request);
	}


}