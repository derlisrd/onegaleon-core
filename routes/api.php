<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function(){

    Route::prefix('/movimientos')->group(function(){

    });

});
