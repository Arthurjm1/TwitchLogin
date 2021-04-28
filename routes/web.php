<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [IndexController::class, 'index']);
Route::get('/auth', [AuthController::class, 'authenticate']);
Route::get('/login', [AuthController::class, 'login']);
Route::get('/home', [AppController::class, 'getHome']);
Route::get('/logout', [AuthController::class, 'logout']);

