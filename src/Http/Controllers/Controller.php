<?php

namespace Future\LaraApiAuth\Http\Controllers;

use Future\LaraApiAuth\Adapters\Cookie as CookieAdapter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as LaravelBaseController;
use Illuminate\Support\Facades\Response;

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

	/**
	 * @param array $clientCredentials
	 * array(
	 * 		"client_id" => 1,
	 * 		"client_secret" => 'fake'
	 * )
	 * @return bool
	 */
	protected function validateClientCredentials(array $clientCredentials): bool
	{
		return isset($clientCredentials['client_id'], $clientCredentials['client_secret'])
			&& !is_null($clientCredentials['client_id'])
			&& !is_null($clientCredentials['client_secret']);
	}

	/**
	 * @param array $tokens
	 * @return JsonResponse
	 */
	protected function sendLoginResponse(array $tokens): JsonResponse
	{
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

	protected function sendUnauthorizedResponse(string $response): JsonResponse
	{
		return Response::json($response, 401);
	}
}