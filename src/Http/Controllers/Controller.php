<?php

namespace Future\LaraApiAuth\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelBaseController;

abstract class Controller extends LaravelBaseController
{
	use AuthorizesRequests;
	use DispatchesJobs;
	use ValidatesRequests;

	protected function getUserModel(): Model
	{
		$userModel = config('auth.providers.users.model', 'App\Models\User');

		return app($userModel);
	}
}