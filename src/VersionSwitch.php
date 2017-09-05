<?php


namespace RandomState\LaravelApi;


interface VersionSwitch {

	/**
	 * Get the identifier of the version that should be used on the current route.
	 * This is often used to resolve a version from the logged-in user or headers.
	 *
	 * @return string
	 */
	public function getVersionIdentifier();
}