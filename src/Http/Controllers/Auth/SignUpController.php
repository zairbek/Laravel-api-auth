<?php

namespace Future\LaraApiAuth\Http\Controllers\Auth;

use Future\LaraApiAuth\Adapters\Cookie as CookieAdapter;
use Future\LaraApiAuth\Adapters\Passport as PassportAdapter;
use Future\LaraApiAuth\Http\Controllers\Controller;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class SignUpController extends Controller
{
	/**
	 * @throws ValidationException
	 * @throws \League\OAuth2\Server\Exception\OAuthServerException
	 * @throws \JsonException
	 */
	public function signUp(Request $request)
	{
		$userModel = $this->getUserModel();

		$request->replace(
			['email' => strtolower($request->email)] + $request->toArray()
		);

		$this->validate($request, [
			'email' => ["required", "email", "unique:" . $userModel->getTable() . ",email"],
			'password' => ['required', 'between:8,24', 'confirmed'],
		]);

        $userModel->email    = $request->email;
        $userModel->password = app(Hasher::class)->make($request->password);
        $userModel->save();

		$tokens = PassportAdapter::getTokenAndRefreshToken([
			'client_id' => $request->header('Client-Id'),
			'client_secret' => $request->header('Client-Secret'),
			'email' => $userModel->email,
			'password' => $request->password,
		]);

		return Response::json([
				'token' => [
					'token_type' => $tokens['token_type'],
					'expires_in' => $tokens['expires_in'],
					'access_token' => $tokens['access_token'],
				]
			], 201)
			->withCookie(CookieAdapter::make($tokens['refresh_token']))
		;
	}
}