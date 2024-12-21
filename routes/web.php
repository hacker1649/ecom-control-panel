<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;

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

// Website route
Route::get('/', [WebsiteController::class, 'index'])->name('website');

Route::controller(UserController::class)->group(function () {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(AdminController::class)->group(function () {
    Route::get('admin/a_login', 'a_login')->name('a_login');
    Route::post('admin/authenticate', 'a_authenticate')->name('a_authenticate');

    Route::middleware('admin')->group(function () {
        Route::get('admin/a_dashboard', 'a_dashboard')->name('a_dashboard');
        Route::get('admin/manage_user', 'fetch_users')->name('manage_user');
        Route::get('admin/add_user', 'add_user')->name('add_user');
        Route::post('storeUser', 'storeUser')->name('storeUser');
        Route::get('/admin/edit_user/{encrypted_id}', 'edit_userData')->name('edit_user');
        Route::post('updateUser/{id}', 'updateUser')->name('updateUser');
        Route::get('admin/delete_user/{id}', 'deactivate')->name('delete_user');
        Route::post('admin/upload', 'upload')->name('upload');
        Route::get('admin/download{id}', 'download')->name('download');
        Route::get('admin/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('admin/report', [ReportController::class, 'index'])->name('report.index');
        Route::get('admingenerate-report-pdf', [ReportController::class, 'generatePdf'])->name('generate.report.pdf');
    });
});

Route::resource('admin/product', ProductController::class)->middleware('admin');

Route::post('admin/product/upload_images', [ProductController::class, 'uploadImages'])->name('product.uploadImages');

Route::get('/product/{productId}/details', [WebsiteController::class, 'details'])->name('details');

Route::controller(CartController::class)->middleware('cart')->group(function () {
    Route::get('/add_to_cart', 'index')->name('cart.index');
    Route::post('/add_to_cart/add/{product_id}', 'add')->name('cart.add');
    Route::patch('/add_to_cart/update/{cartItemId}', 'update')->name('cart.update');
    Route::delete('/add_to_cart/remove', 'remove')->name('cart.remove');
    Route::post('/add_to_cart/update-cart-status', 'updateCartStatus')->name('update.cart.status');
    Route::post('add_to_cart/update-user-profile', 'updateUserProfile')->name('update.user.profile');
    Route::get('/add_to_cart/payment', 'showPaymentPage')->name('payment.page');
    Route::get('/add_to_cart/success', 'showSuccessPage')->name('order.success');
    Route::post('/add_to_cart/success/cash', 'successOnCash')->name('order.success.cash');
    // Route to create PayPal order
    Route::get('add_to_cart/paypal/order/create', 'createPaypalOrder')->name('paypal.order.create');
    // Route to handle successful payment
    Route::post('/add_to_cart/success/paypal', 'successOnPaypal')->name('order.success.paypal');
});
