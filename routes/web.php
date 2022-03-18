<?php

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

Route::get('/', function () {
    return view('home');
});

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/save-user', [App\Http\Controllers\HomeController::class, 'saveUser'])->name('saveUser');
Route::get('/manage/xero', [App\Http\Controllers\XeroController::class, 'redirectUserToXero'])->name('redirectUserToXero');
Route::get('/xero/auth/callback', [App\Http\Controllers\XeroController::class, 'handleCallbackFromXero'])->name('handleCallbackFromXero');


