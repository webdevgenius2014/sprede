<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ContactInfoController;
use App\Http\Controllers\Api\Auth\UserController; //Demographic_info_controller
use App\Http\Controllers\Api\Auth\EmploymentController;
use App\Http\Controllers\Api\Auth\InterestController;
use App\Http\Controllers\Api\Auth\EductionInfoController;
use App\Http\Controllers\Api\Target\TargetController;

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
    Route::post('/contact-info', [ContactInfoController::class, 'contactInfo']);
   
    // Demographic info
    Route::post('/demographic-info', [UserController::class, 'updateDemographic']);
    //Contact Info and username
    // Route::post('/update-demographic-info','UserController@updateDemographic');
    Route::post('/get-username', [ContactInfoController::class, 'getUsername']);
    Route::post('/store-username', [ContactInfoController::class, 'storeUsername']);

    //Employment info
    Route::post('/store-employment-info', [EmploymentController::class, 'storeEmploymentInfo']);

    //Interests Setting
    Route::get('/get-interests', [InterestController::class, 'getDefaultInterest']);   
    Route::post('/add-sub-interests', [InterestController::class, 'storeSubDefaultInterestByUser']);
    Route::post('/store-interests', [InterestController::class, 'storeUserInterest']);
    

    // Eductaion Info
    Route::post('store-education-info', [EductionInfoController::class, 'storeEduction']);

    //Target 
    Route::get('all-targets', [TargetController::class, 'all']);
    Route::get('get-default-interests', [TargetController::class, 'interests']);
    Route::post('get-default-sub-interests', [TargetController::class, 'subInterests']);
    Route::post('/store-target', [TargetController::class, 'store']); 
    Route::get('/edit-target/{id}', [TargetController::class, 'edit']); 
    Route::get('/delete-target/{id}', [TargetController::class, 'destroy']); 
    Route::post('/update-target/{id}', [TargetController::class, 'update']); 

});

// Forgot Password Module api
Route::namespace("App\Http\Controllers\Api")->middleware('api')->group(function(){
    Route::post('/send-otp','ForgetpasswordController@sendOtp');
    Route::post('/verify-otp','ForgetpasswordController@verifyOtp');
//User Module api
    // Route::post('/update-demographic-info','UserController@updateDemographic');
});
