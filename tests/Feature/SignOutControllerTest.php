<?php

namespace Future\LaraApiAuth\Tests\Feature;

use Carbon\Carbon;
use Future\LaraApiAuth\Adapters\Passport;
use Future\LaraApiAuth\Tests\Mocks\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Database\Factories\ClientFactory;
use Laravel\Passport\Passport as PassportModel;

class SignOutControllerTest extends FeatureTestCase
{
	private mixed $tokens;

	private Collection|Model $client;

	private User $user;

	protected function setUp(): void
	{
		parent::setUp();

		$this->user = new User();
		$this->user->email = 'test@gmail.com';
		$this->user->password = $this->app->make(Hasher::class)->make('12345678');
		$this->user->save();

		$this->client = ClientFactory::new()->asPasswordClient()->create();

		$this->tokens = Passport::getTokenAndRefreshToken([
			'client_id' => $this->client->id,
			'client_secret' => $this->client->secret,
			'email' => $this->user->email,
			'password' => '12345678'
		]);
	}

	public function test()
	{
		$this->encryptCookies = false;

		$response = $this
			->withHeaders([
				'Accept' => 'application/json',
				'Authorization' => $this->tokens['token_type'] . ' ' . $this->tokens['access_token']
			])
			->withCookie('refresh-token', $this->tokens['refresh_token'])
			->getJson('/api/auth/sign-out')
		;

		$response->assertSuccessful();

		// Testing Cookie Refresh-Token
		$response->assertCookie('refresh-token');
		$cookie = $response->getCookie('refresh-token');
		$this->assertNull($cookie->getValue());
		$this->assertTrue($cookie->getExpiresTime() < Carbon::now()->timestamp);

		// Testing Access-Token
		$accessToken = $this->user->tokens()->first(['id', 'revoked']);
		$this->assertNotNull($accessToken);
		$this->assertTrue($accessToken['revoked']);

		// Testing Refresh-Token
		$passportModel = PassportModel::refreshToken()->where('access_token_id', $accessToken['id'])->first();
		$this->assertTrue($passportModel->revoked);
	}
}