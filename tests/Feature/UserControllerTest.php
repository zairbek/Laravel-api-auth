<?php

namespace Future\LaraApiAuth\Tests\Feature;

use Future\LaraApiAuth\Adapters\Passport;
use Future\LaraApiAuth\Tests\Mocks\User;
use Illuminate\Contracts\Hashing\Hasher;
use Laravel\Passport\Database\Factories\ClientFactory;

class UserControllerTest extends FeatureTestCase
{
	private User $user;

	private mixed $tokens;

	protected function setUp(): void
	{
		parent::setUp();

		$this->user = new User();
		$this->user->email = 'test@gmail.com';
		$this->user->password = $this->app->make(Hasher::class)->make('12345678');
		$this->user->save();

		$client = ClientFactory::new()->asPasswordClient()->create();

		$this->tokens = Passport::getTokenAndRefreshToken([
			'client_id' => $client->id,
			'client_secret' => $client->secret,
			'email' => $this->user->email,
			'password' => '12345678'
		]);
	}

	protected function tearDown(): void
	{
		unset($this->user, $this->tokens);

		parent::tearDown();
	}

	public function test(): void
	{
		$response = $this
			->withHeaders([
				'Authorization' => $this->tokens['token_type'] . ' ' . $this->tokens['access_token']
			])
			->getJson(route('laraApiAuth.me'));

		$response->assertSuccessful();
		$this->assertEquals($this->user->email, $response['email']);
	}
}