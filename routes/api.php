<?php

use Future\LaraApiAuth\Http\Controllers\Auth\SignOutController;
use Future\LaraApiAuth\Http\Controllers\Auth\RefreshController;
use Future\LaraApiAuth\Http\Controllers\Auth\SignInController;
use Future\LaraApiAuth\Http\Controllers\Auth\SignUpController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {

	Route::group(['prefix' => 'auth'], function () {
		Route::post('sign-up', [SignUpController::class, 'signUp'])->name('laraApiAuth.signUp');

		Route::post('sign-in', [SignInController::class, 'signIn'])->name('laraApiAuth.signIn');
		Route::post('refresh-token', [RefreshController::class, 'refreshToken'])->name('laraApiAuth.refreshToken');
		Route::get('sign-out', [SignOutController::class, 'signOut'])->middleware('auth:api')->name('laraApiAuth.signOut');
	});
});
