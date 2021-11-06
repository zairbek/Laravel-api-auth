<?php

namespace Future\LaraApiAuth\Tests\Feature;

use Future\LaraApiAuth\Adapters\Passport;
use Future\LaraApiAuth\Tests\Mocks\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Database\Factories\ClientFactory;

class RefreshTokenControllerTest extends FeatureTestCase
{
	private array $tokens;
	private Collection|Model $client;

	protected function setUp(): void
	{
		parent::setUp();

		$user = new User();
		$user->email = 'test@gmail.com';
		$user->password = $this->app->make(Hasher::class)->make('12345678');
		$user->save();

		$this->client = ClientFactory::new()->asPasswordClient()->create();

		$this->tokens = Passport::getTokenAndRefreshToken([
			'client_id' => $this->client->id,
			'client_secret' => $this->client->secret,
			'email' => $user->email,
			'password' => '12345678'
		]);
	}

	public function test(): void
	{
		$this->encryptCookies = false;

		$response = $this
			->withHeaders([
				'Accept' => 'application/json',
				'Client-Id' => $this->client->id,
				'Client-Secret' => $this->client->secret
			])
			->withCookie('refresh-token', $this->tokens['refresh_token'])
			->post('/api/auth/refresh-token')
		;

		$response->assertSuccessful();

		// Testing tokens
		$this->assertArrayHasKey('token', $response->json());
		$this->assertArrayHasKey('token_type', $response->json('token'));
		$this->assertArrayHasKey('expires_in', $response->json('token'));
		$this->assertArrayHasKey('access_token', $response->json('token'));

		// Testing cookies
		$response->assertCookie('refresh-token');
	}
}