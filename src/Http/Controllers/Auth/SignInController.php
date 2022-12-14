<?php

namespace Future\LaraApiAuth\Http\Controllers\Auth;

use Future\LaraApiAuth\Adapters\Cookie as CookieAdapter;
use Future\LaraApiAuth\Adapters\Passport as PassportAdapter;
use Future\LaraApiAuth\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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
		$clientCredentials = [
			'client_id' => $request->header('Client-Id'),
			'client_secret' => $request->header('Client-Secret'),
		];

		if (! $this->validateClientCredentials($clientCredentials)) {
			return $this->sendUnauthorizedResponse('Unauthorized: Check please Client Id and Client Secret');
		}

		$this->validateCredentials($request);

		$credentials = [
			'email' => $request->email,
			'password' => $request->password,
		];

		if (! $this->attemptLogin($credentials)) {
			return $this->sendFailedLoginResponse($credentials);
		}

		$tokens = PassportAdapter::getTokenAndRefreshToken(array_merge($clientCredentials, $credentials));

		return $this->sendLoginResponse($tokens);
	}

	protected function validateCredentials(Request $request): void
	{
		$this->validate($request, [
			'email' => ['required', 'email', 'string'],
			'password' => ['required', 'string'],
		]);
	}

	/**
	 * Attempt to log the user into the application.
	 *
	 * @param array $credentials
	 * @return bool
	 */
	protected function attemptLogin(array $credentials): bool
	{
		return Auth::guard()->attempt($credentials);
	}

	/**
	 * Get the failed login response instance.
	 * @param array $request
	 * @return ValidationException
	 * @throws ValidationException
	 */
	protected function sendFailedLoginResponse(array $request): ValidationException
	{
		throw ValidationException::withMessages([
			'email' => [trans('auth.failed')],
		]);
	}
}