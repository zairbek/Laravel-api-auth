<?php

namespace Future\LaraApiAuth\Http\Controllers\Password;

use Future\LaraApiAuth\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
	/**
	 * Reset the given user's password.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws ValidationException
	 */
	public function reset(Request $request)
	{
		$this->getEmailFromPasswordReset($request);

		$request->validate($this->rules(), $this->validationErrorMessages());

		// Here we will attempt to reset the user's password. If it is successful we
		// will update the password on an actual user model and persist it to the
		// database. Otherwise we will parse the error and return the response.
		$response = $this->broker()->reset(
			$this->credentials($request),
			function ($user, $password) {
				$this->resetPassword($user, $password);
			}
		);

		// If the password was successfully reset, we will redirect the user back to
		// the application's home authenticated view. If there is an error we can
		// redirect them back to where they came from with their error message.
		return $response === Password::PASSWORD_RESET
			? $this->sendResetResponse($request, $response)
			: $this->sendResetFailedResponse($request, $response);
	}

	/**
	 * @param Request $request
	 */
	private function getEmailFromPasswordReset(Request $request): void
	{
		$tokens = DB::table('password_resets')
			->whereTime('created_at','>' , now()->addHours(-1))
			->get();

		$email = 'q@fake.fake';
		foreach ($tokens as $user) {
			if (app(Hasher::class)->check($request->token, $user->token)) {
				$email = $user->email;
				break;
			}
		}
		$request['email'] = $email;
	}

	/**
	 * Get the password reset validation rules.
	 *
	 * @return array
	 */
	protected function rules(): array
	{
		return [
			'token' => 'required',
			'email' => 'required|email',
			'password' => 'required|confirmed|min:8',
		];
	}

	/**
	 * Get the password reset validation error messages.
	 *
	 * @return array
	 */
	protected function validationErrorMessages(): array
	{
		return [];
	}

	/**
	 * Reset the given user's password.
	 *
	 * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
	 * @param  string  $password
	 * @return void
	 */
	protected function resetPassword($user, $password)
	{
		$this->setUserPassword($user, $password);

		$user->setRememberToken(Str::random(60));

		$user->save();

		event(new PasswordReset($user));

		$this->apiGuard()->login($user);
	}

	/**
	 * Set the user's password.
	 *
	 * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
	 * @param  string  $password
	 * @return void
	 */
	protected function setUserPassword($user, $password): void
	{
		$user->password = App::make(Hasher::class)->make($password);
	}

	/**
	 * Get the password reset credentials from the request.
	 *
	 * @param Request $request
	 * @return array
	 */
	protected function credentials(Request $request): array
	{
		return $request->only(
			'email', 'password', 'password_confirmation', 'token'
		);
	}

	/**
	 * Get the response for a successful password reset.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $response
	 * @return JsonResponse
	 */
	protected function sendResetResponse(Request $request, $response)
	{
		return Response::json(trans($response));
	}

	/**
	 * Get the response for a failed password reset.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param string $response
	 * @return JsonResponse
	 * @throws ValidationException
	 */
	protected function sendResetFailedResponse(Request $request, $response)
	{
		throw ValidationException::withMessages([
			'email' => [trans($response)],
		]);
	}


	/**
	 * Get the broker to be used during password reset.
	 *
	 * @return PasswordBroker
	 */
	public function broker(): PasswordBroker
	{
		return Password::broker();
	}

	/**
	 * Get the guard to be used during password reset.
	 *
	 * @return StatefulGuard
	 */
	protected function apiGuard(): StatefulGuard
	{
		return Auth::guard();
	}
}