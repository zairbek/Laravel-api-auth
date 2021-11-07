<?php

namespace Future\LaraApiAuth\Tests\Feature;

use Future\LaraApiAuth\Tests\Mocks\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

class ResetPasswordControllerTest extends FeatureTestCase
{
	private User $user;
	private ?ResetPassword $resetPassword;

	protected function setUp(): void
	{
		parent::setUp();
		Notification::fake();

		Schema::create('password_resets', function (Blueprint $table) {
			$table->string('email')->index();
			$table->string('token');
			$table->timestamp('created_at')->nullable();
		});

		$this->user = new User();
		$this->user->email = 'test@gmail.com';
		$this->user->password = $this->app->make(Hasher::class)->make('12345678');
		$this->user->save();

		$this->postJson(route('password.email'), [
			'email' => $this->user->email
		]);

		Notification::assertSentTo(
			$this->user,
			ResetPassword::class,
			function (ResetPassword $resetPassword) {
				$this->resetPassword = $resetPassword;
				return true;
			}
		);
	}

	protected function tearDown(): void
	{
		unset($this->user, $this->resetPassword);
		Schema::dropIfExists('password_resets');

		parent::tearDown();
	}

	public function testSendResetPasswordNotification(): void
	{
		$response = $this->postJson(route('password.reset'), [
			'token' => $this->resetPassword->token,
			'password' => 'newpassword',
			'password_confirmation' => 'newpassword',
		]);

		// Check response
		$response->assertSuccessful();
		// Check New User Password
		$this->user->refresh();
		$this->assertTrue(
			$this->app->make(Hasher::class)->check('newpassword', $this->user->password)
		);
	}
}