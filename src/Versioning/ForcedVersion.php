<?php


namespace RandomState\LaravelApi\Versioning;


use RandomState\LaravelApi\VersionSwitch;

class ForcedVersion implements VersionSwitch {

	/** @var  string $version */
	protected $version;

	public function __construct($version)
	{
		$this->version = $version;
	}

	public function getVersionIdentifier()
	{
		return $this->version;
	}
}