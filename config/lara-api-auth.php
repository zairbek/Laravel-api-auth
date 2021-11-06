<?php

return [
	'access_token_lifetime' => env('ACCESS_TOKEN_LIFETIME', 86400),
	'refresh_token_lifetime' => env('REFRESH_TOKEN_LIFETIME', 86400 * 7),
	'refresh_token_cache_domain' => env('REFRESH_TOKEN_COOKIE_DOMAIN', 'http://localhost'),
];