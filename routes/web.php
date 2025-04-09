<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\Authenticate;

// Serve the auth Vue app for authentication routes
Route::get('/auth/{any}', [ AuthController::class, 'index'])->name('auth');

// Serve the main Vue app after login, protect it with auth middleware
Route::middleware([Authenticate::class])->group(function () {
    Route::get('/', function () {
        return view('app');
    })->where('any', '.*')->name('home');
    Route::get('/app/{any?}', function () {
        return view('app');
    })->where('any', '.*')->name('app');
});
