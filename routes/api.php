<?php

use App\Http\Controllers\GroupAssignApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\ShopApiController;
use App\Http\Controllers\TaskAssignApiController;
use App\Http\Controllers\ShopGroupApiController;
use App\Http\Controllers\DateRangeAssignController;
use App\Http\Controllers\DashboardController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// dashboar 
Route::get('/get-dashboard-data', [DashboardController::class, 'getDashboardData']);


//  user info
Route::get('/users/{id?}', [UserApiController::class, 'showUser']);
Route::post('/add-user', [UserApiController::class, 'addUser']);
Route::put('/update-users/{id}', [UserApiController::class, 'updateUser']);
Route::put('/status-user/{id}', [UserApiController::class, 'statusUser']);
Route::delete('/delete-user/{id}', [UserApiController::class, 'deleteUser']);

Route::post('/user-login', [UserApiController::class, 'userLogin']);
Route::post('/admin-login', [UserApiController::class, 'adminLogin']);
Route::put('/user-location/{id}', [UserApiController::class, 'userLocation']);

// shop info
Route::get('/shop/{id?}', [ShopApiController::class, 'showShop']);
Route::post('/add-shop', [ShopApiController::class, 'addShop']);
Route::put('/update-shop/{id}', [ShopApiController::class, 'updateShop']);
Route::put('/status-shop/{id}', [ShopApiController::class, 'statusShop']);
Route::delete('/delete-shop/{id}', [ShopApiController::class, 'deleteShop']);


// task assign
Route::get('/task/{id?}', [TaskAssignApiController::class, 'showTask']);
Route::get('/find-task/{id}/{date}', [TaskAssignApiController::class, 'findTask']);
Route::post('/task-assign', [TaskAssignApiController::class, 'taskAssign']);
Route::delete('/task-delete/{id}', [TaskAssignApiController::class, 'taskDelete']);

// shop group
Route::get('/shop-group/{id?}', [ShopGroupApiController::class, 'showShopGroup']);
Route::post('/shop-assign-by-group', [ShopGroupApiController::class, 'shopAssignByGroup']);
Route::delete('/shop-group-delete/{id}', [ShopGroupApiController::class, 'shopGroupDelete']);

// group assign
Route::get('/user-group/{id?}', [GroupAssignApiController::class, 'showUserGroup']);
Route::post('/group-assign-by-user', [GroupAssignApiController::class, 'groupAssignByUser']);
Route::delete('/user-group-delete/{id}', [GroupAssignApiController::class, 'userGroupDelete']);

// date range assign
Route::get('/user-date-range/{id}', [DateRangeAssignController::class, 'dateRangeUser']);
Route::post('/assign-date-range', [DateRangeAssignController::class, 'dateRangeAssign']);


