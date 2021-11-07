<?php

namespace Future\LaraApiAuth\Http\Controllers\Auth;

use Future\LaraApiAuth\Adapters\Cookie as CookieAdapter;
use Future\LaraApiAuth\Adapters\Passport;
use Future\LaraApiAuth\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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
	public function refreshToken(Request $request): JsonResponse
	{
		$tokens = Passport::generateRefreshToken(
			[
				'client_id' => $request->header('client-id'),
				'client_secret' => $request->header('client-secret'),
			],
			$request->cookie('refresh-token')
		);

		return $this->sendLoginResponse($tokens);
	}
}