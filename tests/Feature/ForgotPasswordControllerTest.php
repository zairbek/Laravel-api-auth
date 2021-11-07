<?php

namespace Future\LaraApiAuth\Tests\Feature;

use Future\LaraApiAuth\Tests\Mocks\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

class ForgotPasswordControllerTest extends FeatureTestCase
{
	private User $user;

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
	}

	protected function tearDown(): void
	{
		@unlink($this->user);
		Schema::dropIfExists('password_resets');

		parent::tearDown();
	}

	public function testSendResetPasswordNotification(): void
	{
		$response = $this->postJson(route('password.email'), [
			'email' => $this->user->email
		]);

		// Assert send mail to this user
		$response->assertSuccessful();
		Notification::assertSentTo($this->user, ResetPassword::class);

		// Assert not sent mail to another users
		$user = new User();
		$user->email = 'fake@gmail.com';
		$user->password = $this->app->make(Hasher::class)->make('fake12345678');
		$user->save();
		Notification::assertNotSentTo($user, ResetPassword::class);
	}
}