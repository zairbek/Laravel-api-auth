<?php

namespace Future\LaraApiAuth\Adapters;

use Symfony\Component\HttpFoundation\Cookie as HttpCookie;

class Cookie
{
	public const REFRESH_TOKEN_COOKIE_NAME = 'refresh-token';

	/**
	 * Refresh-token токен положим в куки и ограничим с доменом и httponly
	 *
	 * @param string $refreshToken
	 * @return HttpCookie
	 */
	public static function make(string $refreshToken): HttpCookie
	{
		return HttpCookie::create(
			self::REFRESH_TOKEN_COOKIE_NAME,
			$refreshToken,
			now()->addSeconds(config('lara-api-auth.refresh_token_lifetime', 86400)),
			null,
			config('lara-api-auth.refresh_token_cache_domain'),
			true
		);
	}
}