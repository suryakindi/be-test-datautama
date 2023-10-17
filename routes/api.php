<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
                        AuthController,
                        DataController,
};

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




Route::group([
    'controller'        => AuthController::class,
    'prefix'            => 'auth'
    ], function () {
        Route::post('/login', [AuthController::class, 'storelogin']);
        Route::post('/register', [AuthController::class, 'register']);
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::group([
        'controller'        => DataController::class,
        'prefix'            => 'data'
    ], function () {
        Route::get('/product', [DataController::class, 'listproduct']);
        Route::post('/product/create', [DataController::class, 'createproduct']);
        Route::get('/product/edit/{id}', [DataController::class, 'getproduct']);
        Route::post('/product/edit/{id}', [DataController::class, 'editproduct']);
        Route::get('/product/delete/{id}', [DataController::class, 'deleteproduct']);
        Route::get('/list-transactions', [DataController::class, 'listtransactions']);
        Route::post('/transactions', [DataController::class, 'transactionsproduct']);
    });
    Route::group([
        'controller' => AuthController::class,
        'prefix' => 'session',
    ], function(){
        Route::get('/logout', [AuthController::class, 'logout']);
       
    });
});

Route::get('/login', function(){
return response('Silahkan Login', 401);
})->name('login');

