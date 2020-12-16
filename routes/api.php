<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OutletsController;
use App\Http\Controllers\UnitsController;
use Illuminate\Support\Facades\Route;


Route::resource('units', UnitsController::class);
Route::resource('categories', CategoriesController::class);
Route::resource('products',ProductsController::class);
Route::resource('outlets',OutletsController::class);
