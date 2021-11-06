<?php

namespace Future\LaraApiAuth\Tests\Mocks;

use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @property string $email
 * @property string $password
 *
 * @package Future\LaraApiAuth\Tests\Mocks
 */
class User extends \Illuminate\Foundation\Auth\User
{
	use HasApiTokens;
}