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

	Route::group(['prefix' => 'auth'], function () {
		Route::post('sign-up', [SignUpController::class, 'signUp'])->name('laraApiAuth.signUp');

		Route::post('sign-in', [SignInController::class, 'signIn'])->name('laraApiAuth.signIn');
		Route::post('refresh-token', [RefreshController::class, 'refreshToken'])->name('laraApiAuth.refreshToken');
		Route::get('sign-out', [SignOutController::class, 'signOut'])->middleware('auth:api')->name('laraApiAuth.signOut');

		// Route::get('verify-email', [VerifyController::class, 'verify'])->name('laraApiAuth.verify');
	});

	Route::get('/me', [UserController::class, 'user'])->middleware('auth:api')->name('laraApiAuth.me');

	Route::group(['prefix' => 'password'], function () {
		Route::post('email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
		Route::post('reset', [ResetPasswordController::class, 'reset'])->name('password.reset');
	});
});
