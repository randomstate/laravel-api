<?php


namespace RandomState\LaravelApi\Exceptions;


interface Exception {

	public function getContent();
	public function getHttpStatusCode() : int;
	public function getMessages() : array;
}