<?php


namespace RandomState\LaravelApi;


use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use RandomState\Api\Api;
use RandomState\Api\Namespaces\CustomNamespace;
use RandomState\Api\Namespaces\Manager;
use RandomState\Api\Transformation\Manager as TransformManager;
use RandomState\Api\Versioning\Manager as VersionManager;
use RandomState\Api\Versioning\Version;
use RandomState\LaravelApi\Adapters\Driver;
use RandomState\LaravelApi\Adapters\RequiresAdapters;
use RandomState\LaravelApi\Exceptions\Handler;
use RandomState\LaravelApi\Exceptions\UndeclaredVersionException;
use RandomState\LaravelApi\Http\Response\ResponseFactory;
use RandomState\LaravelApi\Http\Routing\Router;
use RandomState\LaravelApi\Traits\ApiConfig;
use RandomState\LaravelApi\Versioning\ErrorSwitch;
use RandomState\LaravelApi\Versioning\LatestVersion;

class LaravelApiServiceProvider extends ServiceProvider {

	use ApiConfig;

	public function boot()
	{
		$this->publishes([
			self::getConfigPath() => config_path(self::getConfigName() . ".php")
		], 'laravel-api');
	}

	public function register()
	{
		$this->mergeConfigFrom(self::getConfigPath(), self::getConfigName());

		$this->replaceRouter();
		$this->bindNamespaceManager();
		$this->resolveLatestVersionAsDefaultForAnyNamespace();
		$this->bindNamespaces();
		$this->bindResponseFactory();
		$this->wrapExceptionHandler();
		$this->bindVersion();
	}

	protected function bindNamespaceManager()
	{
		$this->app->bind(Manager::class, function() {
			return new Manager();
		});
	}

	protected function replaceRouter()
	{
		//bind router
		$this->app->singleton(Router::class);
		$this->app->alias(Router::class, 'router');
	}

	protected function bindNamespaces()
	{
		$namespaces = $this->getConfig('namespaces');

		$this->app->resolving(Manager::class, function(Manager $manager) use($namespaces) {
			foreach($namespaces as $namespace => $config) {
				$manager->register($namespace, function() use($namespace) {
					return new CustomNamespace(
						$this->getVersionManagerForNamespace($namespace)
					);
				});
			}
		});
	}

	protected function getVersionManagerForNamespace($namespace)
	{
		$manager = new VersionManager();
		$versions = $this->getConfig("namespaces.{$namespace}.versions");

		foreach($versions as $versionIdentifier => $config) {
			$version = $manager->register($versionIdentifier, function() use($namespace, $versionIdentifier) {
				return new TransformManager(
					$this->getNewAdaptersForNamespaceAndVersion($namespace, $versionIdentifier)
				);
			});

			if($inherited = $versions[$versionIdentifier]['inherit'] ?? false) {
				$version->inherit($inherited);
			}
		}

		return $manager;
	}

	protected function getNewAdaptersForNamespaceAndVersion($namespace, $version)
	{
		// FractalAdapterResolver
		$adaptersName = $this->getConfig("namespaces.{$namespace}.adapters");
		$driverConfig = $this->getConfig("adapters.{$adaptersName}");
		$driver = $driverConfig['driver'];

		/** @var Driver $driver */
		$driver = $this->app->make($driver);
		$versionConfig = $this->getConfig($key = "namespaces.{$namespace}.versions")[$version] ?? null;

		if(is_null($versionConfig)) {
			throw new UndeclaredVersionException("The version {$version} does not have any configuration or transformers configured.");
		}

		$adapters = $driver->buildAdapters($driverConfig, $versionConfig);

		foreach($adapters as $adapter) {
		    if($adapter instanceof RequiresAdapters) {
		        $eligible = [];

		        foreach($adapters as $check) {
		            if($check !== $adapter) {
		                $eligible[] = $check;
                    }
                }

		        $adapter->setAdapters($eligible);
            }
        }

        return $adapters;
	}

	protected function throwErrorIfVersionNotSet()
	{
		$this->app->bind(VersionSwitch::class, ErrorSwitch::class);
	}

	protected function resolveLatestVersionAsDefaultForAnyNamespace()
	{
		$this->app->alias(LatestVersion::class, VersionSwitch::class);
		$this->app->bind(LatestVersion::class, function() {
			return new LatestVersion($this->app->make(Api::class));
		});
	}

	protected function bindResponseFactory()
	{
		$this->app->bind(ResponseFactory::class, function() {
			if($this->app->bound(Api::class)) {
				$api = $this->app->make(Api::class);
				$currentVersion = $this->app->make(VersionSwitch::class)->getVersionIdentifier();

				$version = $api->versions()->get($currentVersion);
				return new ResponseFactory($version);
			}

			return new ResponseFactory();
		});
	}

	protected function wrapExceptionHandler()
	{
		$this->app->bind(ExceptionHandler::class, Handler::class);
	}

	protected function bindVersion()
	{
		$this->app->bind(Version::class, function() {
			/** @var VersionSwitch $switch */
			$switch = $this->app->make(VersionSwitch::class);

			/** @var Api $api */
			$api = $this->app->make(Api::class);

			return $api->versions()->get($switch->getVersionIdentifier());
		});
	}
}