<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/send-email', [App\http\Controllers\MailController::class, 'send_mail'])->name('send_email');

Route::post('/sign-up', [App\http\Controllers\UserController::class, 'registerUser'])->name('register_user');

Route::post('/validate-pin', [App\http\Controllers\UserController::class, 'validatePin'])->name('validate_pin');

Route::post('/user-login', [App\http\Controllers\UserController::class, 'userLogin'])->name('user_login');

Route::post('/update-profile', [App\http\Controllers\UserController::class, 'updateProfile'])->name('update_profile');
