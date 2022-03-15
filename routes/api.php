<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\CompanyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy office your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

//Auth
Route::prefix('auth')->group(function () {

    //Route::post('/usersfree', [UsersController::class, 'store']);

    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/glogin/{email}', [AuthController::class, 'glogin']);
    Route::post('/plogin', [AuthController::class, 'plogin']);
    Route::post('/pforgetpsw', [AuthController::class, 'pForgetPsw']);
    Route::post('/pchangepsw', [AuthController::class, 'pchangepsw']);
    Route::middleware('auth:api')->group(function () {
        Route::get('/user', [AuthController::class, 'getUserByToken']);
        Route::get('/logout', function () {
        });
    });
    
});

Route::middleware(['auth:api'/*,'scopes:manage-dashboard'*/])->group(function () {
    
    //Users
    Route::post('/profile', [UsersController::class, 'update_profile']);
    //Route::get('/users/{id}/offices', [UsersController::class, 'show_user_offices']);

    Route::post('/users', [UsersController::class, 'store']);
    Route::post('/users/import', [UsersController::class, 'import_store']);
    Route::get('/users', [UsersController::class, 'index']); //For super admin
    Route::get('/users/{id}', [UsersController::class, 'show']);
    Route::put('/users/{id}', [UsersController::class, 'update']);
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);

    Route::get('/users/all/roles/{id}', [UsersController::class, 'show_all_roles']);
    Route::get('/users/all/permissions/{id}', [UsersController::class, 'index_all_permissions']);

    Route::post('/users/{id}/roles', [UsersController::class, 'give_role']);
    Route::put('/users/{id}/roles', [UsersController::class, 'sync_roles']);
    Route::delete('/users/{id}/role/{roleId}', [UsersController::class, 'revoke_role']);
    Route::get('/users/{id}/roles', [UsersController::class, 'show_user_roles']);
    Route::get('/users/{id}/permissions', [UsersController::class, 'show_user_permissions']);

    Route::get('/users/{id}/logs', [UsersController::class, 'index_user_logs']);
   
    //Roles
    Route::post('/roles', [RolesController::class, 'store']);
    Route::put('/roles/{id}', [RolesController::class, 'update']);
    Route::delete('/roles/{id}', [RolesController::class, 'destroy']);

    //companys
    Route::get('/company/{id}/users', [CompanyController::class, 'show_all_users']);
    Route::get('/company/{id}/offices', [CompanyController::class, 'show_all_offices']);

    Route::get('/companys', [CompanyController::class, 'index']);
    Route::post('/companys', [CompanyController::class, 'store']);
    Route::post('/companys/{id}', [CompanyController::class, 'update']);
    Route::delete('/companys/{id}', [CompanyController::class, 'destroy']);
    Route::get('/companys/{id}', [CompanyController::class, 'show']);

    //offices
    Route::get('/offices', [OfficeController::class, 'index']); //Super admin
    Route::post('/offices', [OfficeController::class, 'store']);
    Route::put('/offices/{id}', [OfficeController::class, 'update']);
    Route::delete('/offices/{id}', [OfficeController::class, 'destroy']);
   
});


