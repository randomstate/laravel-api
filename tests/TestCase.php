<?php


namespace RandomState\Tests\LaravelApi;


use Illuminate\Contracts\Http\Kernel;
use RandomState\LaravelApi\LaravelApiServiceProvider;

class TestCase extends \Tests\TestCase {


	protected function setUp()
	{
		parent::setUp();
		$this->app->register(LaravelApiServiceProvider::class);
		$this->app->bind(Kernel::class, \RandomState\Tests\LaravelApi\Model\Kernel::class);
	}
}