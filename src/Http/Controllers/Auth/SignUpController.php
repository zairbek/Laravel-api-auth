<?php

namespace Future\LaraApiAuth\Http\Controllers\Auth;

use Future\LaraApiAuth\Adapters\Cookie as CookieAdapter;
use Future\LaraApiAuth\Adapters\Passport as PassportAdapter;
use Future\LaraApiAuth\Http\Controllers\Controller;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class SignUpController extends Controller
{
	private Model $userModel;

	public function __construct()
	{
		$this->userModel = $this->getUserModel();
	}

	/**
	 * @throws ValidationException
	 * @throws \League\OAuth2\Server\Exception\OAuthServerException
	 * @throws \JsonException
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function signUp(Request $request)
	{
		$request->replace(
			['email' => strtolower($request->email)] + $request->toArray()
		);

		$this->validateCredentials($request);

		$this->registerUser($request);

		$tokens = PassportAdapter::getTokenAndRefreshToken([
			'client_id' => $request->header('Client-Id'),
			'client_secret' => $request->header('Client-Secret'),
			'email' => $this->userModel->email,
			'password' => $request->password,
		]);

		return $this->sendLoginResponse($tokens);
	}

	/**
	 * @param Request $request
	 * @throws ValidationException
	 */
	protected function validateCredentials(Request $request): void
	{
		$this->validate($request, [
			'email' => ["required", "email", "unique:" . $this->userModel->getTable() . ",email"],
			'password' => ['required', 'between:8,24', 'confirmed'],
		]);
	}

	/**
	 * @param Request $request
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	protected function registerUser(Request $request): void
	{
		$this->userModel->email    = $request->email;
		$this->userModel->password = app(Hasher::class)->make($request->password);
		$this->userModel->save();
	}
}