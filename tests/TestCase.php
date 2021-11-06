<?php

namespace Future\LaraApiAuth\Tests;

use Future\LaraApiAuth\LaraApiAuthServiceProvider;
use Future\LaraApiAuth\Tests\Mocks\User;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\PassportServiceProvider;
use Orchestra\Testbench\TestCase as TestCaseAlias;

class TestCase extends TestCaseAlias
{
	use RefreshDatabase;

	const KEYS = __DIR__.'/keys';
	const PUBLIC_KEY = self::KEYS.'/oauth-public.key';
	const PRIVATE_KEY = self::KEYS.'/oauth-private.key';

	protected function setUp(): void
	{
		parent::setUp();

		$this->artisan('migrate:fresh');

		$this->artisan('passport:keys');
	}

	protected function getEnvironmentSetUp($app)
	{
		$config = $app->make(Repository::class);

		$config->set('auth.defaults.provider', 'users');

		$config->set('auth.providers.users.model', User::class);

		$config->set('auth.guards.api', ['driver' => 'passport', 'provider' => 'users']);

		$app['config']->set('database.default', 'testbench');

		$app['config']->set('passport.storage.database.connection', 'testbench');

		$app['config']->set('database.connections.testbench', [
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		]);
	}

	public function getPackageProviders($app)
	{
		return [LaraApiAuthServiceProvider::class, PassportServiceProvider::class];
	}
}