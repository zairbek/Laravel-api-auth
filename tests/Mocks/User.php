<?php

namespace Future\LaraApiAuth\Tests\Mocks;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @property string $email
 * @property string $password
 * @property null|string $remember_token
 *
 * @package Future\LaraApiAuth\Tests\Mocks
 */
class User extends \Illuminate\Foundation\Auth\User
{
	use HasApiTokens;
	use Notifiable;
}