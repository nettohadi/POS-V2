<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AccessUser;
use Illuminate\Support\Facades\Route;


Route::post('tokens',[TokenController::class,'store'])->name('tokens.store');
Route::middleware(AccessUser::class)->group(function(){
    Route::delete('tokens',[TokenController::class,'destroy'])->name('tokens.destroy');;
    Route::resource('units', UnitController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('users',UserController::class);
    Route::resource('products',ProductController::class);
    Route::resource('outlets',OutletController::class);
    Route::resource('recipes',RecipeController::class);
});


