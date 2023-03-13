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

Route::get('/unauth', function ()
{
    return response()->json([
       'error' => [
           'code' => 403,
           'message' => 'Login failed'
       ]
    ], 403);
})->name('login');

//Администратор
//Вывод всех пользователей
Route::get('/user', [\App\Http\Controllers\UserController::class, 'index'])->middleware(['auth:api', 'admin']);
//Добавление новой карточки
Route::post('/user', [\App\Http\Controllers\UserController::class, 'store'])->middleware(['auth:api', 'admin']);
//Создание смены
Route::post('/work-shift', [\App\Http\Controllers\WorkShiftController::class, 'store'])->middleware(['auth:api', 'admin']);
//Открытие смены
Route::get('/work-shift/{id}/open', [\App\Http\Controllers\WorkShiftController::class, 'open'])->middleware(['auth:api', 'admin']);
//Закрытие смены
Route::get('/work-shift/{id}/close', [\App\Http\Controllers\WorkShiftController::class, 'close'])->middleware(['auth:api', 'admin']);
//Добавление сотрудника на смену
Route::get('/work-shift/{user_id}/user', [\App\Http\Controllers\WorkShiftController::class, 'adduser'])->middleware(['auth:api', 'admin']);;
