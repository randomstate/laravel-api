<?php


namespace RandomState\LaravelApi\Adapters;


use RandomState\LaravelApi\Adapters\Driver;

class Manager {

	/**
	 * @var array | Driver[]
	 */
	protected $drivers = [];

	public function __construct()
	{
	}

	public function bind($name, Driver $driver, array $config)
	{
		$this->drivers[$name] = $driver->resolveFromConfig($config);

		return $this;
	}

	/**
	 * @param $driver
	 *
	 * @return mixed|null|Driver
	 */
	public function get($driver)
	{
		return $this->drivers[$driver] ?? null;
	}
}