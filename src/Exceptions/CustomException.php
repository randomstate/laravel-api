<?php


namespace RandomState\LaravelApi\Exceptions;

class CustomException extends \Exception implements Exception {

	protected $content;
	protected $httpStatusCode;
	protected $messages = [];

	public function __construct($data, $messages = [], $httpStatusCode = null)
	{
		$this->content = $data;
		$this->messages = $this->messages + $messages;
		$this->httpStatusCode = $httpStatusCode;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function getHttpStatusCode(): int
	{
		return $this->httpStatusCode;
	}

	public function getMessages(): array
	{
		return $this->messages;
	}

}