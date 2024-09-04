<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductList;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UpdateContentMiddleware;
use App\Http\Controllers\AlterTableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckToken;
use App\Http\Controllers\ProductController;



// Register To Users
Route::post('/register', [UserController::class, 'register']);

Route::post('/login', [UserController::class, 'login']);



Route::middleware(CheckToken::class)->group(function () {   // Token Sahibi Kullanıcıların Kullanabileceği Route'lar

    Route::post('/logout', [UserController::class, 'logout']);  // Log Out

    Route::get('/user/{id}', [AdminController::class, 'UserList'])->middleware([AdminMiddleware::class]); // Kullanıcı listeleme

    Route::put('/alter-user', [AdminController::class, 'AlterUsers'])->middleware([UpdateContentMiddleware::class]); // Kullanıcı listeleme

    // TABLE ADD ROUTES
    //======================================================================================

    Route::put('/add_color', [TableController::class, 'add_color'])->middleware([UpdateContentMiddleware::class]);

    Route::put('/add_size', [TableController::class, 'add_size'])->middleware([UpdateContentMiddleware::class]);

    Route::put('/add_category', [TableController::class, 'add_category'])->middleware([UpdateContentMiddleware::class]);

    Route::put('/add_variant', [TableController::class, 'add_variant'])->middleware([UpdateContentMiddleware::class]);

    Route::put('/add_product', [TableController::class, 'add_product'])->middleware([UpdateContentMiddleware::class]);

    // PRODUCT SEARH ROUTES
    //========================================================================================

    Route::get('/list', [ProductList::class, 'listByFilter'])->middleware([AdminMiddleware::class]); // filterleyerek bulmak

    Route::get('/list/{id}', [ProductList::class, 'listByID'])->middleware([AdminMiddleware::class]); // id ile bulmak

    // ALTER TABLE ROUTES
    //=========================================================================================

    Route::put('/alter_table', [AlterTableController::class, 'AlterTable'])->middleware([UpdateContentMiddleware::class]);


    // RESOURCE EXAMPLE ROUTES
    //==========================================================================================

    Route::resource('products', ProductController::class);
    /*
    GET	        /products	index	products.index
    GET	        /products/create	create	products.create
    POST	    /products	store	products.store
    GET	        /products/{product}	show	products.show
    GET	        /products/{product}/edit	edit	products.edit
    PUT/PATCH	/products/{product}	update	products.update
    DELETE	    /products/{product}	destroy	products.destroy */
});
