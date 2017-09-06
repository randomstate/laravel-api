<?php


namespace RandomState\LaravelApi\Adapters;


use RandomState\Api\Transformation\Adapters\Adapter;

interface Driver {

	/**
	 * @param array $driverConfig
	 * @param array $versionConfig
	 *
	 * @return array|Adapter[]
	 */
	public function buildAdapters($driverConfig, $versionConfig);
}