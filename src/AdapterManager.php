<?php


namespace RandomState\LaravelApi;


class AdapterManager {

	/**
	 * @var array | AdapterDriver[]
	 */
	protected $drivers = [];

	public function __construct()
	{
	}

	public function bind($name, AdapterDriver $driver, array $config)
	{
		$this->drivers[$name] = $driver->resolveFromConfig($config);

		return $this;
	}

	/**
	 * @param $driver
	 *
	 * @return mixed|null|AdapterDriver
	 */
	public function get($driver)
	{
		return $this->drivers[$driver] ?? null;
	}
}