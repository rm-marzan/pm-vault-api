<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\FoldersController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\ItemsController;

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

Route::middleware('auth:sanctum')->group(function(){
    // User APIs
    Route::get('/user/{id}', [UsersController::class, 'show']);
    Route::put('user/{id}', [UsersController::class, 'update']);
    Route::delete('/user/{id}', [UsersController::class, 'destroy']);
    Route::post('/logout', [UsersController::class, 'logout']);
    
    // Folder APIs
    Route::get('/folders/{userId}', [FoldersController::class, 'index']);
    Route::post('/folder', [FoldersController::class, 'store']);
    Route::put('/folder/{id}', [FoldersController::class, 'update']);
    Route::delete('/folder/{id}', [FoldersController::class, 'destroy']);

    // Organization APIs
    Route::get('/organizations/{userId}', [OrganizationsController::class, 'index']);
    Route::post('/organization', [OrganizationsController::class, 'store']);
    Route::put('/organization/{id}', [OrganizationsController::class, 'update']);
    Route::delete('/organization/{id}', [OrganizationsController::class, 'destroy']);
    
    // Item APIs
    Route::get('/items/{userId}', [ItemsController::class, 'index']);
    Route::get('/item/{id}', [ItemsController::class, 'show']);
    Route::post('/item', [ItemsController::class, 'store']);
    Route::put('/item/{id}', [ItemsController::class, 'update']);
    // Deletes
    Route::delete('/item/{id}', [ItemsController::class, 'destroy']);
    Route::get('/itemRestore/{id}', [ItemsController::class, 'itemRestore']);
    Route::get('/itemsDeleted/{userId}', [ItemsController::class, 'trashedItems']);
    Route::post('/items/{userId}', [ItemsController::class, 'destroyItems']);
    // Export-Import
    Route::get('/itemsExport/{userId}', [ItemsController::class, 'export']);
    Route::post('/itemsImport/{userId}', [ItemsController::class, 'import']);
    // Move to Folder
    Route::post('/moveToFolder/{folderId}', [ItemsController::class, 'moveItemsToFolder']);
    // Move to Organization
    Route::post('/moveToOrg/{orgId}', [ItemsController::class, 'moveItemsToOrg']);
});

// Login-Register
Route::post('/register',[UsersController::class, 'store']);
Route::post('/login',[UsersController::class, 'login']);

