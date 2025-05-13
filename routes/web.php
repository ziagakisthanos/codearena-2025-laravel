<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/posts', [PostController::class, 'index'])->name('blog.index');

Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('blog.show');

Route::get('/authors/{user}', [PostController::class, 'index'])->name('author');
