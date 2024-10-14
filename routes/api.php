<?php

use App\Http\Controllers\OneGaleonApp\AuthController;
use App\Http\Controllers\OneGaleonApp\CategoryController;
use App\Http\Controllers\OneGaleonApp\MovimientoController;
use Illuminate\Support\Facades\Route;


Route::prefix('/auth')->group(function(){
    Route::post('/login',[AuthController::class,'login'])->name('api_login');
    Route::post('/register',[AuthController::class,'register'])->name('api_register');
});


Route::middleware('auth:api')->group(function(){

    Route::prefix('/movimientos')->group(function(){
        Route::get('/',[MovimientoController::class,'index']);
        Route::get('/{id}',[MovimientoController::class,'show']);
        Route::post('/',[MovimientoController::class,'index']);
        Route::put('/{id}',[MovimientoController::class,'index']);
        Route::delete('/{id}',[MovimientoController::class,'index']);
    });

    Route::prefix('/categorias')->group(function(){
        Route::get('/',[CategoryController::class,'index']);
        Route::get('/{id}',[CategoryController::class,'show']);
        Route::post('/',[CategoryController::class,'index']);
        Route::put('/{id}',[CategoryController::class,'index']);
        Route::delete('/{id}',[CategoryController::class,'index']);
    });

});
