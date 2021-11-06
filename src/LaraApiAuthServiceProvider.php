<?php

namespace Future\LaraApiAuth;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class LaraApiAuthServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->definePassportConfigs();

		$this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

		$this->publishes([
			__DIR__ . '/../config/lara-api-auth.php' => config_path('lara-api-auth.php')
		]);
	}

	public function register()
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/lara-api-auth.php', 'lara-api-auth');
	}

	private function definePassportConfigs(): void
	{
		Passport::tokensExpireIn(Carbon::now()->addSeconds(config('lara-api-auth.access_token_lifetime')));
		Passport::refreshTokensExpireIn(Carbon::now()->addSeconds(config('lara-api-auth.refresh_token_lifetime')));
	}
}