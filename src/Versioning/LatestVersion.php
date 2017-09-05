<?php


namespace RandomState\LaravelApi\Versioning;


use RandomState\Api\Api;
use RandomState\LaravelApi\VersionSwitch;

class LatestVersion implements VersionSwitch {

	/**
	 * @var Api
	 */
	protected $api;

	public function __construct(Api $api)
	{
		$this->api = $api;
	}

	public function getVersionIdentifier()
	{
		return $this->api->versions()->current()->identifier();
	}
}