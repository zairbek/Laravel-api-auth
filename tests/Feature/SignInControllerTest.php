<?php

namespace Future\LaraApiAuth\Tests\Feature;

use Future\LaraApiAuth\Tests\Mocks\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Database\Factories\ClientFactory;

class SignInControllerTest extends FeatureTestCase
{
	private User $user;
	private Collection|Model $client;

	protected function setUp(): void
	{
		parent::setUp();

		$this->user = new User();
		$this->user->email = 'test@gmail.com';
		$this->user->password = $this->app->make(Hasher::class)->make('12345678');
		$this->user->save();

		$this->client = ClientFactory::new()->asPasswordClient()->create();
	}

	protected function tearDown(): void
	{
		unset($this->user, $this->client);

		parent::tearDown();
	}

	public function testSuccessfullySignIn()
	{
		$response = $this
			->withHeaders([
				'client-id' => $this->client->id,
				'client-secret' => $this->client->secret
			])
			->postJson('/api/auth/sign-in', [
				'email' => $this->user->email,
				'password' => '12345678'
			]);

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