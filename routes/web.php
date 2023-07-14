<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicWithTagsController;
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

Route::get('/', MainController::class)->name('main.page');
Route::get('login', AuthController::class)->name('login')->middleware('authorized');
Route::get('verify-login/{token}', [AuthController::class, 'verifyLogin'])->name('verify-login');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/profile', ProfileController::class)->name('profile');
});

Route::get('/{slug}', PublicController::class)->name('public');
Route::get('/{slug}/{tags?}', PublicWithTagsController::class)->where('tags', '.*')->name('public.tags');
