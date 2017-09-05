<?php


namespace RandomState\LaravelApi\Exceptions;


use Exception;
use Throwable;

class VersionNotSetException extends Exception {

	public function __construct($code = 0, Throwable $previous = null)
	{
		parent::__construct(
			<<<EOF
The API version for the current namespace has not been set.
You should bind the current API version in a service provider or before the request hits the controller.
EOF
, $code, $previous);
	}
}