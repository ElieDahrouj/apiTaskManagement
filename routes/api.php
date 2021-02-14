<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasksController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [TasksController::class, 'login']);
Route::post('/register', [TasksController::class, 'register']);
Route::get('/task', [TasksController::class, 'index'])->middleware("auth:sanctum");
Route::post('/task', [TasksController::class, 'store'])->middleware("auth:sanctum");
Route::get('/task/{id}', [TasksController::class, 'show'])->middleware("auth:sanctum");
Route::put('/task/{id}', [TasksController::class, 'update'])->middleware("auth:sanctum");
Route::delete('/task/{id}', [TasksController::class, 'destroy'])->middleware("auth:sanctum");
