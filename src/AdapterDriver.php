<?php


namespace RandomState\LaravelApi;


use RandomState\Api\Transformation\Adapters\Adapter;

interface AdapterDriver {

	/**
	 * @param array $driverConfig
	 * @param array $versionConfig
	 *
	 * @return array|Adapter[]
	 */
	public function buildAdapters($driverConfig, $versionConfig);
}