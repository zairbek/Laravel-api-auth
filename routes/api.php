<?php

use Future\LaraApiAuth\Http\Controllers\Auth\SignInController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {

	Route::group(['prefix' => 'auth'], function () {
		Route::post('sign-in', [SignInController::class, 'signIn'])->name('laraApiAuth.signIn');
	});
});
