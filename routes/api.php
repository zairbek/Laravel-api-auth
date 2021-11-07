<?php

use Future\LaraApiAuth\Http\Controllers\Auth\SignOutController;
use Future\LaraApiAuth\Http\Controllers\Auth\RefreshController;
use Future\LaraApiAuth\Http\Controllers\Auth\SignInController;
use Future\LaraApiAuth\Http\Controllers\Auth\SignUpController;
use Future\LaraApiAuth\Http\Controllers\Password\ForgotPasswordController;
use Future\LaraApiAuth\Http\Controllers\Password\ResetPasswordController;
use Future\LaraApiAuth\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {
	$routeConfigs = config('lara-api-auth.routes');

	Route::group(['prefix' => 'auth'], function () use ($routeConfigs) {
		if (in_array('laraApiAuth.signUp', $routeConfigs, true)) {
			Route::post('sign-up', [SignUpController::class, 'signUp'])->name('laraApiAuth.signUp');
		}

		if (in_array('laraApiAuth.signIn', $routeConfigs, true)) {
			Route::post('sign-in', [SignInController::class, 'signIn'])->name('laraApiAuth.signIn');
		}

		if (in_array('laraApiAuth.refreshToken', $routeConfigs, true)) {
			Route::post('refresh-token', [RefreshController::class, 'refreshToken'])->name('laraApiAuth.refreshToken');
		}

		if (in_array('laraApiAuth.signOut', $routeConfigs, true)) {
			Route::get('sign-out', [SignOutController::class, 'signOut'])->middleware('auth:api')->name('laraApiAuth.signOut');
		}
	});

	if (in_array('laraApiAuth.me', $routeConfigs, true)) {
		Route::get('/me', [UserController::class, 'user'])->middleware('auth:api')->name('laraApiAuth.me');
	}

	Route::group(['prefix' => 'password'], function () use ($routeConfigs) {
		if (in_array('password.email', $routeConfigs, true)) {
			Route::post('email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
		}

		if (in_array('password.reset', $routeConfigs, true)) {
			Route::post('reset', [ResetPasswordController::class, 'reset'])->name('password.reset');
		}
	});
});
