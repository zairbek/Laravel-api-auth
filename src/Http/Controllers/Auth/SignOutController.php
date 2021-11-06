<?php

namespace Future\LaraApiAuth\Http\Controllers\Auth;

use Future\LaraApiAuth\Adapters\Cookie as CookieAdapter;
use Future\LaraApiAuth\Adapters\Passport;
use Future\LaraApiAuth\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SignOutController extends Controller
{
	public function __construct()
	{
		$this->middleware(['auth:api']);
	}

	public function signOut()
	{
		if (Auth::guard('api')->check()) {
			$token = Auth::user()->token();

			Passport::revokeAccessAndRefreshTokens($token->id);
			$token->revoke();
		}

		return Response::json('Signed out successfully')
			->withCookie(CookieAdapter::forget())
		;
	}
}