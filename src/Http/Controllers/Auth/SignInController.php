<?php

namespace Future\LaraApiAuth\Http\Controllers\Auth;

use Future\LaraApiAuth\Adapters\Cookie as CookieAdapter;
use Future\LaraApiAuth\Adapters\Passport as PassportAdapter;
use Future\LaraApiAuth\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class SignInController extends Controller
{
	/**
	 * @throws ValidationException
	 * @throws \League\OAuth2\Server\Exception\OAuthServerException
	 * @throws \JsonException
	 */
	public function signIn(Request $request)
	{
		$this->validate($request, [
			'email' => ['required', 'email', 'string'],
			'password' => ['required', 'string'],
		]);

		$credentials = [
			'email' => $request->email,
			'password' => $request->password,
		];

		if (! $this->attemptLogin($credentials)) {
			return $this->sendFailedLoginResponse($credentials);
		}

		$clientCredentials = [
			'client_id' => $request->header('Client-Id'),
			'client_secret' => $request->header('Client-Secret'),
		];

		$tokens = PassportAdapter::getTokenAndRefreshToken(array_merge($clientCredentials, $credentials));

		return Response::json([
				'token' => [
					'token_type' => $tokens['token_type'],
					'expires_in' => $tokens['expires_in'],
					'access_token' => $tokens['access_token'],
				]
			])
			->withCookie(CookieAdapter::make($tokens['refresh_token']))
		;
	}

	/**
	 * Attempt to log the user into the application.
	 *
	 * @param array $credentials
	 * @return bool
	 */
	private function attemptLogin(array $credentials): bool
	{
		return Auth::guard('web')->attempt($credentials);
	}

	/**
	 * Get the failed login response instance.
	 * @param array $request
	 * @return ValidationException
	 * @throws ValidationException
	 */
	private function sendFailedLoginResponse(array $request): ValidationException
	{
		throw ValidationException::withMessages([
			'email' => [trans('auth.failed')],
		]);
	}
}