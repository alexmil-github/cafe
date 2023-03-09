<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
//Аутентификация и выход
Route::post('/login', [\App\Http\Controllers\UserController::class, 'login'])->withoutMiddleware(['auth:api']);
Route::get('/logout', [\App\Http\Controllers\UserController::class, 'logout'])->middleware('auth:api');

//Администратор
//Вывод всех пользователей
Route::get('/user', [\App\Http\Controllers\UserController::class, 'index'])->middleware('auth:api');
