<?php


namespace RandomState\LaravelApi\Traits;


trait ApiConfig {

	protected function getConfig($key)
	{
		return $this->app->make('config')->offsetGet(self::getConfigName() . '.' . $key);
	}

	private static function getConfigName()
	{
		return 'api';
	}

	private static function getConfigPath()
	{
		return __DIR__ . '/../../config/' . self::getConfigName() . '.php';
	}
}