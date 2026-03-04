<?php

use App\Http\Controllers\BlogPostSearchController;
use App\Http\Controllers\ProductManualSearchController;
use App\Http\Controllers\SupportFaqSearchController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'embeddings.home')->name('home');
Route::get('/search/blog-posts', BlogPostSearchController::class)->name('search.blog-posts');
Route::get('/search/product-manuals', ProductManualSearchController::class)->name('search.product-manuals');
Route::get('/search/support-faqs', SupportFaqSearchController::class)->name('search.support-faqs');
