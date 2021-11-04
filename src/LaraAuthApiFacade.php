<?php

namespace Future\LaraAuthApi;

use Illuminate\Support\Facades\Facade;

class LaraAuthApiFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'lara-auth-api';
	}
}