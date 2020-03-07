<?php


namespace RandomState\Tests\LaravelApi;


use Illuminate\Contracts\Http\Kernel;

class TestCase extends \Tests\TestCase {


	protected function setUp() : void
	{
		parent::setUp();
		$this->app->bind(Kernel::class, \RandomState\Tests\LaravelApi\Model\Kernel::class);
	}
}