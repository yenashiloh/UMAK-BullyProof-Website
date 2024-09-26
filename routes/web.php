<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\PreventBackHistory;

Route::get('/', [AdminAuthController::class, 'showLoginForm'])->name('login');

Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

Route::middleware([PreventBackHistory::class, 'discipline'])->group(function () {
Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('admin.dashboard');
Route::get('/profile', [ProfileController::class, 'showDisciplineProfile'])->name('admin.profile');
Route::post('/admin/update-profile', [ProfileController::class, 'updateProfile'])->name('admin.updateProfile');
Route::post('/admin-logout', [AdminAuthController::class, 'logoutAdmin'])->name('admin.logout');
Route::get('/users', [UserController::class, 'showUsers'])->name('admin.users.users');
});