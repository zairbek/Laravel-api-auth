<?php

namespace Future\LaraApiAuth\Tests\Feature;

use Future\LaraApiAuth\Tests\Mocks\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Database\Factories\ClientFactory;

class SignUpControllerTest extends FeatureTestCase
{
	private Collection|Model $client;

	protected function setUp(): void
	{
		parent::setUp();

		$this->client = ClientFactory::new()->asPasswordClient()->create();
	}

	protected function tearDown(): void
	{
		unset($this->client);

		parent::tearDown();
	}

	public function testSuccessfullySignUp(): void
	{
		$response = $this
			->withHeaders([
				'client-id' => $this->client->id,
				'client-secret' => $this->client->secret
			])
			->postJson('/api/auth/sign-up', [
				'email' => 'test@gmail.com',
				'password' => '12345678',
				'password_confirmation' => '12345678'
			]);

		$response->assertSuccessful();

		// Testing tokens
		$this->assertArrayHasKey('token', $response->json());
		$this->assertArrayHasKey('token_type', $response->json('token'));
		$this->assertArrayHasKey('expires_in', $response->json('token'));
		$this->assertArrayHasKey('access_token', $response->json('token'));

		// Testing cookies
		$response->assertCookie('refresh-token');

		// Testing Created User
		$user = User::whereEmail('test@gmail.com')->first();
		$this->assertNotNull($user);
	}

	public function testSignUpWhenUserAlreadyExists(): void
	{
		$user = new User();
		$user->email = 'test@gmail.com';
		$user->password = $this->app->make(Hasher::class)->make('12345678');
		$user->save();

		$response = $this
			->withHeaders([
				'client-id' => $this->client->id,
				'client-secret' => $this->client->secret
			])
			->postJson('/api/auth/sign-up', [
				'email' => $user->email,
				'password' => '12345678',
				'password_confirmation' => '12345678'
			]);

		$response->assertStatus(422);
		$response->assertJsonValidationErrors('email');
	}

	/**
	 * @test
	 * @dataProvider providerValidation
	 */
	public function validation($params, $errors)
	{
		$this->postJson('/api/auth/sign-up', $params)
			->assertJsonValidationErrors($errors)
		;
	}

	public function providerValidation()
	{
		return [
			'Отсутствует email' => [['password' => '12345678', 'password_confirmation' => '12345678'], ['email']],
			'Email не email' => [['email' => 'dfasfsdf', 'password' => '12345678', 'password_confirmation' => '12345678'], ['email']],
			'Отсутствует password' => [['email' => 'test@mail.ru', 'password_confirmation' => '12345678'], ['password']],
			'Пароль слишком маленький' => [['email' => 'test@mail.ru', 'password' => '123', 'password_confirmation' => '123'], ['password']],
			'Пароль слишком большой' => [['email' => 'test@mail.ru', 'password' => '12345678901234567890123456789', 'password_confirmation' => '12345678901234567890123456789'], ['password']],
			'Пароли не совпадают' => [['email' => 'test@mail.ru', 'password' => '12345678', 'password_confirmation' => 'zazazazazaza'], ['password']],
		];
	}

}