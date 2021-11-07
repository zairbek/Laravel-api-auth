<?php

return [
	'access_token_lifetime' => env('ACCESS_TOKEN_LIFETIME', 86400),
	'refresh_token_lifetime' => env('REFRESH_TOKEN_LIFETIME', 86400 * 7),
	'refresh_token_cache_domain' => env('REFRESH_TOKEN_COOKIE_DOMAIN', 'http://localhost'),

	/**
	 * Не нужные роуты закомментируйте
	 */
	'routes' => [
		'laraApiAuth.signUp', // Route: /api/auth/sign-up
		'laraApiAuth.signIn', // Route: /api/auth/sign-in
		'laraApiAuth.refreshToken', // Route: /api/auth/refresh-token
		'laraApiAuth.signOut', // Route: /api/auth/sign-out
		'laraApiAuth.me', // Route: /api/me
		'password.email', // Route: /api/password/email
		'password.reset', // Route: /api/password/reset
	],
];