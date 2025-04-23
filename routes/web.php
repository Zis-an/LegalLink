<?php

use App\Http\Controllers\BidController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\LawyerController;
use App\Http\Controllers\LawyerVerificationController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CaseController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Combine profile routes inside the authenticated group
Route::middleware(['auth'])->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Resource routes for roles, users, and products
    Route::resource('roles', RoleController::class);
    Route::resource('permissions',PermissionController::class);
    Route::resource('cases', CaseController::class);
    Route::resource('bids', BidController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('consultations', ConsultationController::class);
    Route::resource('lawyers', LawyerController::class);
    Route::resource('lawyer-verifications', LawyerVerificationController::class);
});

// 👇 Restrict user management to only admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
