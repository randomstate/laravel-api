<?php


namespace RandomState\LaravelApi\Versioning;


use RandomState\LaravelApi\Exceptions\VersionNotSetException;
use RandomState\LaravelApi\VersionSwitch;

class ErrorSwitch implements VersionSwitch {

	public function getVersionIdentifier()
	{
		throw new VersionNotSetException();
	}
}