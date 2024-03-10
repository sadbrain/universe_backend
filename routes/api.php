<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/v1/categories', [CategoryController::class, 'getAll']);
Route::get('/v1/categories/{id}', [CategoryController::class, 'get']);
Route::delete('/v1/categories/{id}', [CategoryController::class, 'delete']);
Route::put('/v1/categories/{id}', [CategoryController::class, 'update']);
Route::post('/v1/categories', [CategoryController::class, 'create']);
