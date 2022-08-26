<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\SubscriberController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('posts', PostController::class);
Route::apiResource('websites', WebsiteController::class);
Route::apiResource('subscribers', SubscriberController::class);
Route::post('subscribers/{id}/website', [SubscriberController::class, 'addWebsite']);
Route::post('subscribers/{id}/websites', [SubscriberController::class, 'addWebsites']);
