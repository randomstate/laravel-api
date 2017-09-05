<?php


namespace RandomState\LaravelApi\Adapters;


use Illuminate\Contracts\Container\Container;
use League\Fractal\Manager;
use League\Fractal\Serializer\SerializerAbstract;
use RandomState\Api\Transformation\Fractal\Resolver;
use RandomState\Api\Transformation\Fractal\Switchboard;
use RandomState\LaravelApi\AdapterDriver;

class Fractal implements AdapterDriver {

	/**
	 * @var Container
	 */
	protected $app;

	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	public function buildAdapters($driverConfig, $versionConfig)
	{
		// build up new adapters for the version

		// Process for Fractal
		// ---------------------
		// Bind common dependencies specifically for this version
		// Build the adapters
		// Forget the common dependencies

		$this->app->bind(Manager::class, function() {
			return new Manager();
		});

		$this->app->bind(Resolver::class, function() {
			return new Resolver(function($transformer) {
				return $this->app->make($transformer);
			});
		});

		$this->app->bind(Switchboard::class, function() {
			return new Switchboard(
				$this->app->make(Resolver::class)
			);
		});

		$this->app->bind(SerializerAbstract::class, $driverConfig['serializer']);

		$transformers = $versionConfig;
		$adapters = $driverConfig['adapters'];

		$this->app->resolving(Resolver::class, function(Resolver $resolver) use($transformers) {
			foreach($transformers as $class => $transformer) {
				// If transformer, then not an auto-bind... otherwise try to discover.
				if(!is_numeric($class)) {
					$resolver->bind($class, $transformer);
				} else {
					$resolver->bind($transformer);
				}
			}
		});

		$built = [];

		foreach($adapters as $adapter) {
			$built[] = $this->app->make($adapter);
		}

		return $built;
	}


}