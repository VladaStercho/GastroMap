<?php

use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// ==========================================
// ПУБЛІЧНІ МАРШРУТИ
// ==========================================
Route::get('/', [EstablishmentController::class, 'index'])->name('home');
Route::get('/establishment/{id}', [EstablishmentController::class, 'show'])->name('establishments.show');

// ==========================================
// АВТОРИЗАЦІЯ
// ==========================================
Route::get('/auth', [AuthController::class, 'showAuthForm'])->name('auth');
Route::get('/login', [AuthController::class, 'showAuthForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');

// ==========================================
// ЗАХИЩЕНІ МАРШРУТИ (для всіх авторизованих)
// ==========================================
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Відгуки
    Route::post('/establishment/{id}/review', [ReviewController::class, 'store'])->name('review.store');

    // ==========================================
    // МАРШРУТИ ДЛЯ ВЛАСНИКА ЗАКЛАДУ (Owner)
    // ==========================================
    Route::get('/owner/establishment/create', [EstablishmentController::class, 'create'])->name('owner.establishment.create');
    Route::post('/owner/establishment', [EstablishmentController::class, 'store'])->name('owner.establishment.store');
    Route::get('/owner/establishment/{id}/edit', [EstablishmentController::class, 'edit'])->name('owner.establishment.edit');
    Route::put('/owner/establishment/{id}', [EstablishmentController::class, 'update'])->name('owner.establishment.update');

    // ==========================================
    // МАРШРУТИ ДЛЯ АДМІНІСТРАТОРА
    // ==========================================
    Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::post('/admin/approve/{id}', [AuthController::class, 'approveEstablishment'])->name('admin.approve');
    Route::post('/admin/user/{id}/role', [AuthController::class, 'updateUserRole'])->name('admin.user.role');
    Route::delete('/admin/user/{id}', [AuthController::class, 'deleteUser'])->name('admin.user.delete');
    Route::delete('/admin/establishment/{id}', [AuthController::class, 'deleteEstablishment'])->name('admin.establishment.delete');
    Route::delete('/admin/review/{id}', [AuthController::class, 'deleteReview'])->name('admin.review.delete');

    Route::put('/admin/establishments/{id}/update-menu', [EstablishmentController::class, 'updateMenu'])->name('admin.establishment.updateMenu');
});
