<?php


namespace RandomState\LaravelApi\Http\Response;


use RandomState\Api\Versioning\Version;

class ResponseFactory {

	/**
	 * @var Version
	 */
	protected $version;

	public function __construct(Version $version = null)
	{
		$this->version = $version;
	}

	public function build($response)
	{
		// get the current version
		// ask for the data to be transformed
		// build into response
		if($this->version) {
			$response = $this->version->transform($response);
		}

		return $response;
	}
}