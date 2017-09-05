<?php


namespace RandomState\Tests\LaravelApi\Feature\Versioning;


use RandomState\Api\Api;
use RandomState\Api\Versioning\Manager;
use RandomState\Api\Versioning\Version;
use RandomState\Tests\LaravelApi\Model\CurrentUserTransformer;
use RandomState\Tests\LaravelApi\Model\OldUserTransformer;
use RandomState\Tests\LaravelApi\Model\User;
use RandomState\Tests\LaravelApi\TestCase;

class CanVersionTest extends TestCase {

	/**
	 * @test
	 */
	public function can_build_versions_from_config()
	{
		$this->app->make('config')->set('api.namespaces.default.versions', [
			'1.0' => [
				OldUserTransformer::class
			],
			'2.0' => [
				CurrentUserTransformer::class,
			]
		]);

		/** @var Manager $versionManager */
		$versionManager = $this->app->make(Api::class)->versions();

		$this->assertInstanceOf(Version::class, $versionManager->get('1.0'));
		$this->assertInstanceOf(Version::class, $versionManager->get('2.0'));

		try {
			$this->assertNull($versionManager->get('3.0'));
		} catch (\ErrorException $e) {
			$this->assertEquals("Undefined index: 3.0", $e->getMessage());
		}
	}

	/**
	 * @test
	 */
	public function each_version_is_independent_from_each_other()
	{
		$this->app->make('config')->set('api.namespaces.default.versions', [
			'1' => [
				OldUserTransformer::class,
			],
			'2' => [
				CurrentUserTransformer::class,
			]
		]);

		/** @var Manager $versionManager */
		$versionManager = $this->app->make(Api::class)->versions();

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
				OldUserTransformer::class,
			]
		]);

		/** @var Manager $versionManager */
		$versionManager = $this->app->make(Api::class)->versions();

		$v1 = $versionManager->get('1.0.0');
		$this->assertNotNull($v1);
	}
}
