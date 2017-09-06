<?php


namespace RandomState\LaravelApi\Http\Middleware;


use Closure;
use Illuminate\Contracts\Container\Container;
use RandomState\Api\Api;
use RandomState\Api\Namespaces\Manager;
use RandomState\Api\Versioning\Version;
use RandomState\LaravelApi\Versioning\ForcedVersion;
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
	 * @param null $version
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next, $name = null, $version = null)
	{
		$this->app->bind(Api::class, function() use($name) {
			return $this->app->make(Manager::class)->getNamespace($name);
		});

		if($version) {
			$this->app->instance(VersionSwitch::class, new ForcedVersion($version));
		}

		return $next($request);
	}

	protected function bindManagerInstances()
	{
		$this->app->alias(Api::class, Manager::class);

		$this->app->bind(\RandomState\Api\Versioning\Manager::class, function() {
			return $this->app->make(Api::class)->versions();
		});

		$this->app->bind(\RandomState\Api\Transformation\Manager::class, function() {
			return $this->app->make(Api::class)->versions()->get(
				$this->app->make(VersionSwitch::class)->getVersionIdentifier()
			)->getTransformationManager();
		});
	}
}