<?php

namespace Future\LaraApiAuth\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelBaseController;

class Controller extends LaravelBaseController
{
	use AuthorizesRequests;
	use DispatchesJobs;
	use ValidatesRequests;
}