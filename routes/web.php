<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;


Route::get('/', [CategoryController::class, 'home'])->name('home');

Route::get('/caterory/{id}', [CategoryController::class, 'singleCategory'])->name('single.category');

Route::post('categories/store', [CategoryController::class, 'store'])->name('categories.store');

Route::get('/categories/index', [CategoryController::class, 'index'])->name('categories.index');


Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');

Route::delete('/categories/destroy/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
