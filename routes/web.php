<?php

use App\Http\Controllers\BookAuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
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
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::middleware('auth')->group(function(){
    Route::prefix('books')->group(function(){
        Route::get('',[BookController::class,'index'])->name('book');
        Route::get('{id}',[BookController::class,'get']);
        Route::post('{id}',[BookController::class,'update']);
        Route::delete('{id}',[BookController::class,'destroy']);
        Route::post('',[BookController::class,'store']);
    });

    Route::prefix('authors')->group(function(){
        Route::get('',[BookAuthorController::class,'index'])->name('author');
        Route::get('select2',[BookAuthorController::class,'select2']);
        Route::get('{id}',[BookAuthorController::class,'get']);
        Route::patch('{id}',[BookAuthorController::class,'update']);
        Route::delete('{id}',[BookAuthorController::class,'destroy']);
        Route::post('',[BookAuthorController::class,'store']);
    });

    Route::prefix('users')->middleware('auth.user')->group(function(){
        Route::get('',[UserController::class,'index'])->name('user');
        Route::get('{id}',[UserController::class,'get']);
        Route::patch('{id}',[UserController::class,'update']);
        Route::delete('{id}',[UserController::class,'destroy']);
        Route::post('',[UserController::class,'store']);
    });
});
