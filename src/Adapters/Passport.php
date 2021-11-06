<?php

namespace Future\LaraApiAuth\Adapters;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\ServerRequest as GuzzleRequest;
use Illuminate\Support\Facades\App;
use JsonException;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;

class Passport
{
	/**
	 * @param array $request
	 * @return mixed
	 * @throws JsonException
	 * @throws \League\OAuth2\Server\Exception\OAuthServerException
	 */
	public static function getTokenAndRefreshToken(array $request)
	{
		/** @var AuthorizationServer $server */
		$server = App::make(AuthorizationServer::class);

		$psrResponse = $server
			->respondToAccessTokenRequest(
				(new GuzzleRequest('POST', ''))
					->withParsedBody([
						'grant_type' => 'password',
						'client_id' => $request['client_id'],
						'client_secret' => $request['client_secret'],
						'username' => $request['email'],
						'password' => $request['password'],
						'scope' => '',
					]),
				new GuzzleResponse()
			)
		;

		return json_decode((string)$psrResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);
	}

	/**
	 * @param array $request
	 * @param $refreshToken
	 * @return array
	 * @throws JsonException
	 * @throws \League\OAuth2\Server\Exception\OAuthServerException
	 */
	public static function generateRefreshToken(array $request, $refreshToken): array
	{
		/** @var AuthorizationServer $server */
		$server = App::make(AuthorizationServer::class);

		$psrResponse = $server
			->respondToAccessTokenRequest(
				(new GuzzleRequest('POST', ''))
					->withParsedBody([
						'grant_type' => 'refresh_token',
						'refresh_token' => $refreshToken,
						'client_id' => $request['client_id'],
						'client_secret' => $request['client_secret'],
						'scope' => '',
					]),
				new GuzzleResponse()
			)
		;

		return json_decode((string)$psrResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);
	}

	/**
	 * @param string $tokenId
	 */
	public static function revokeAccessAndRefreshTokens(string $tokenId): void
	{
		$tokenRepository = app(TokenRepository::class);
		$refreshTokenRepository = app(RefreshTokenRepository::class);

		$tokenRepository->revokeAccessToken($tokenId);
		$refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);
	}
}