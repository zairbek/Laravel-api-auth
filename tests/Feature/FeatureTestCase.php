<?php

namespace Future\LaraApiAuth\Tests\Feature;

use Future\LaraApiAuth\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FeatureTestCase extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		Schema::create('users', function (Blueprint $table) {
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('password');
			$table->string('remember_token')->nullable();
			$table->timestamps();
		});
	}

	protected function tearDown(): void
	{
		Schema::dropIfExists('users');

		parent::tearDown();
	}
}