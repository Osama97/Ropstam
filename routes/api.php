<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;

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

Route::post('/signup',[AccountController::class, 'Signup']);
Route::post('/login',[AccountController::class, 'Login']);


/*
	* I'm Using Passport library for authentication to access private routes of application
*/

Route::group(['middleware' => 'auth:api'], function(){
	
	Route::get('/home-data', [AccountController::class, 'HomeData']);
	Route::post('/product-details', [AccountController::class, 'ProductDetails']);

});