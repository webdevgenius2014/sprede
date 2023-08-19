<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactInfoController;
use App\Http\Controllers\Api\UserController; //Demographic_info_controller

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group([
    'middleware' => ['api', 'user.auth']
], function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    // Route::get('/login-view', [AuthController::class, 'login_view'])->name('login');
    
    //Contact Info Routes
    Route::post('/contact-info', [ContactInfoController::class, 'contact_info']);
   
    // Demographic info
    // Route::post('/update-demographic-info','UserController@updateDemographic');
    Route::post('/demographic-info', [UserController::class, 'updateDemographic']);
    Route::post('/get-username', [ContactInfoController::class, 'get_username']);
    Route::post('/store-username', [ContactInfoController::class, 'store_username']);

});

// Forgot Password Module api
Route::namespace("App\Http\Controllers\Api")->middleware('api')->group(function(){
    Route::post('/send-otp','ForgetpasswordController@sendOtp');
    Route::post('/verify-otp','ForgetpasswordController@verifyOtp');
//User Module api
    // Route::post('/update-demographic-info','UserController@updateDemographic');
});
