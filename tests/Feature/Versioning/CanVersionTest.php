<?php


namespace RandomState\Tests\LaravelApi\Feature\Versioning;


use RandomState\Api\Versioning\Manager;
use RandomState\Api\Versioning\Version;
use RandomState\LaravelApi\LaravelApiServiceProvider;
use RandomState\Tests\LaravelApi\Model\CurrentUserTransformer;
use RandomState\Tests\LaravelApi\Model\OldUserTransformer;
use RandomState\Tests\LaravelApi\Model\User;
use RandomState\Tests\LaravelApi\TestCase;

class CanVersionTest extends TestCase {

	protected function setUp() : void
	{
		parent::setUp();
		$this->app->register(LaravelApiServiceProvider::class);
	}

	/**
	 * @test
	 */
	public function can_build_versions_from_config()
	{
		$this->app->make('config')->set('api.namespaces.default.versions', [
			'1.0' => [
				'transformers' => [
					OldUserTransformer::class,
				]
			],
			'2.0' => [
				'transformers' => [
					CurrentUserTransformer::class,
				]
			]
		]);

		/** @var Manager $versionManager */
		$versionManager = $this->app->make(\RandomState\Api\Namespaces\Manager::class)->getNamespace()->versions();

		$this->assertInstanceOf(Version::class, $versionManager->get('1.0'));
		$this->assertInstanceOf(Version::class, $versionManager->get('2.0'));

		try {
			$this->assertNull($versionManager->get('3.0'));
		} catch (\ErrorException $e) {
			$this->assertEquals("Undefined array key \"3.0\"", $e->getMessage());
		}
	}

	/**
	 * @test
	 */
	public function each_version_is_independent_from_each_other()
	{
		$this->app->make('config')->set('api.namespaces.default.versions', [
			'1' => [
				'transformers' => [
					OldUserTransformer::class,
				]
			],
			'2' => [
				'transformers' => [
					CurrentUserTransformer::class,
				]
			]
		]);

		/** @var Manager $versionManager */
		$versionManager = $this->app->make(\RandomState\Api\Namespaces\Manager::class)->getNamespace()->versions();

		$v1 = $versionManager->get('1');
		$v2 = $versionManager->get('2');

		// Does not have the same transform manager
		// No adapters are the same
		$this->assertNotEquals($v1->getTransformManager(), $v2->getTransformManager());

		$user = new User();

		$this->assertNotEquals($output1 = $v1->transform($user), $output2 =$v2->transform($user));
	}

	/**
	 * @test
	 */
	public function can_configure_version_with_dot_notation()
	{
		$this->app->make('config')->set('api.namespaces.default.versions', [
			'1.0.0' => [
				'transformers' => [
					OldUserTransformer::class,
				]
			]
		]);

		/** @var Manager $versionManager */
		$versionManager = $this->app->make(\RandomState\Api\Namespaces\Manager::class)->getNamespace()->versions();

		$v1 = $versionManager->get('1.0.0');
		$this->assertNotNull($v1);
	}
}
