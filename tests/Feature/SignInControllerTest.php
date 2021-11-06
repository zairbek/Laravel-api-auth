<?php

namespace Future\LaraApiAuth\Tests\Feature;

use Future\LaraApiAuth\Adapters\Cookie;
use Future\LaraApiAuth\Tests\Mocks\User;
use Future\LaraApiAuth\Tests\TestCase;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Database\Factories\ClientFactory;

class SignInControllerTest extends TestCase
{
	private User $user;
	private Collection|Model $client;

	protected function setUp(): void
	{
		parent::setUp();

		Schema::create('users', function (Blueprint $table) {
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('password');
			$table->timestamps();
		});

		$this->user = new User();
		$this->user->email = 'test@gmail.com';
		$this->user->password = $this->app->make(Hasher::class)->make('12345678');
		$this->user->save();

		$this->client = ClientFactory::new()->asPasswordClient()->create(['user_id' => $this->user->id]);

	}

	protected function tearDown(): void
	{
		@unlink($this->user);
		Schema::dropIfExists('users');

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