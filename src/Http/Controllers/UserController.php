<?php

namespace Future\LaraApiAuth\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
	public function __construct()
	{
		$this->middleware(['auth:api']);
	}

	/**
	 * @return JsonResponse
	 */
	public function user(): JsonResponse
	{
		return Response::json(
			Auth::guard('api')->user()
		);
	}
}