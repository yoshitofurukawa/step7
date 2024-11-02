<?php

use Illuminate\Support\Facades\Route;
// "Route"というツールを使うために必要な部品を取り込んでいます。
use App\Http\Controllers\ProductController;
// ProductControllerに繋げるために取り込んでいます
use Illuminate\Support\Facades\Auth;
// "Auth"という部品を使うために取り込んでいます。この部品はユーザー認証（ログイン）に関する処理を行います

Auth::routes();

// 商品一覧画面
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// 商品登録フォーム画面
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

// 商品追加画面
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

// 商品詳細画面
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// 商品更新フォーム画面
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');

// 商品更新
Route::patch('/products/{product}', [ProductController::class, 'update'])->name('products.update');

// 商品削除
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

Route::group(['middleware' => 'auth'], function () {
    Route::resource('products', ProductController::class);
});

