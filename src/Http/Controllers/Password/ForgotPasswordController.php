<?php

namespace Future\LaraApiAuth\Http\Controllers\Password;

use Future\LaraApiAuth\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
	/**
	 * Send a reset link to the given user.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws ValidationException
	 */
	public function sendResetLinkEmail(Request $request): JsonResponse
	{
		$this->validateEmail($request);

		// We will send the password reset link to this user. Once we have attempted
		// to send the link, we will examine the response then see the message we
		// need to show to the user. Finally, we'll send out a proper response.
		$response = $this->broker()->sendResetLink(
			$this->credentials($request)
		);

		return $response == Password::RESET_LINK_SENT
			? $this->sendResetLinkResponse($request, $response)
			: $this->sendResetLinkFailedResponse($request, $response);
	}

	/**
	 * Validate the email for the given request.
	 *
	 * @param Request $request
	 * @return void
	 * @throws ValidationException
	 */
	protected function validateEmail(Request $request): void
	{
		$userModel = $this->getUserModel();

		$messages = [
			'exists' => trans('validation.we_cant_find_email'), // todo: lang
		];

		$this->validate(
			$request,
			['email' => 'required|email|exists:' . $userModel->getTable() . ',email'],
			$messages
		);
	}

	/**
	 * Get the needed authentication credentials from the request.
	 *
	 * @param Request $request
	 * @return array
	 */
	protected function credentials(Request $request): array
	{
		return $request->only('email');
	}

	/**
	 * @param Request $request
	 * @param $response
	 * @return JsonResponse
	 */
	protected function sendResetLinkResponse(Request $request, $response): JsonResponse
	{
		return Response::json('ok', HttpResponse::HTTP_OK);
	}

	/**
	 * @param Request $request
	 * @param $response
	 * @return JsonResponse
	 */
	protected function sendResetLinkFailedResponse(Request $request, $response): JsonResponse
	{
		return Response::json(['error' => trans($response)], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
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
}