<?php


namespace RandomState\LaravelApi\Adapters;


use Illuminate\Contracts\Container\Container;
use League\Fractal\Manager;
use League\Fractal\Serializer\SerializerAbstract;
use RandomState\Api\Transformation\Fractal\Resolver;
use RandomState\Api\Transformation\Fractal\Switchboard;
use RandomState\LaravelApi\Adapters\Driver;

class Fractal implements Driver {

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

		$transformers = $versionConfig['transformers'];
		$adapters = $driverConfig['adapters'];

		$this->app->bind(Manager::class, function() {
			return new Manager();
		});

		$this->app->bind(Resolver::class, function() use($transformers) {
			$resolver =  new Resolver(function($transformer) {
				return $this->app->make($transformer);
			});

			foreach($transformers as $transformer => $classes) {
				// If transformer, then not an auto-bind... otherwise try to discover.
				if(!is_numeric($transformer)) {
					if(!is_array($classes))
					{
						$classes = [$classes];
					}

					foreach($classes as $class) {
						$resolver->bind($class, $transformer);
					}
				} else {
					$resolver->bind($classes);
				}
			}

			return $resolver;
		});

		$this->app->bind(Switchboard::class, function() {
			return new Switchboard(
				$this->app->make(Resolver::class)
			);
		});

		$this->app->bind(SerializerAbstract::class, $driverConfig['serializer']);

		$built = [];

		foreach($adapters as $adapter) {
			$built[] = $this->app->make($adapter);
		}

		return $built;
	}


}