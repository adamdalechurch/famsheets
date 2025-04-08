<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Authenticate;

Route::middleware([Authenticate::class])->group(function () {
    Route::get('/', function () {
        return view('app');
    })->name('app'); // This route is for the main application page

});

Route::get('/auth', function () {
    return view('auth');
})->name('auth'); // This route is for authentication pages