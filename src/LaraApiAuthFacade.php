<?php

namespace Future\LaraApiAuth;

use Illuminate\Support\Facades\Facade;

class LaraApiAuthFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'lara-api-auth';
	}
}