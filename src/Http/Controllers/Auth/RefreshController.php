<?php

namespace Future\LaraApiAuth\Http\Controllers\Auth;

use Future\LaraApiAuth\Adapters\Cookie as CookieAdapter;
use Future\LaraApiAuth\Adapters\Passport;
use Future\LaraApiAuth\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RefreshController extends Controller
{
	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \JsonException
	 * @throws \League\OAuth2\Server\Exception\OAuthServerException
	 */
	public function refreshToken(Request $request)
	{
		$tokens = Passport::generateRefreshToken(
			[
				'client_id' => $request->header('client-id'),
				'client_secret' => $request->header('client-secret'),
			],
			$request->cookie('refresh-token')
		);

		return Response::json([
				'token' => [
					'token_type' => $tokens['token_type'],
					'expires_in' => $tokens['expires_in'],
					'access_token' => $tokens['access_token'],
				]
			])
			->withCookie(CookieAdapter::make($tokens['refresh_token']));
	}
}