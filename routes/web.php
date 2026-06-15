<?php

use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// Головна сторінка та перегляд закладу
Route::get('/', [EstablishmentController::class, 'index'])->name('home');
Route::get('/establishment/{id}', [EstablishmentController::class, 'show'])->name('establishments.show');

// Авторизація та реєстрація
Route::get('/auth', [AuthController::class, 'showAuthForm'])->name('auth');
Route::get('/login', [AuthController::class, 'showAuthForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Захищені маршрути (вимагають авторизації)
Route::middleware('auth')->group(function () {

    // Особистий кабінет та вихід
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Залишення відгуків
    Route::post('/establishment/{id}/review', [ReviewController::class, 'store'])->name('review.store');

    // Оновлення PDF-меню закладу (Доступно для Admin та Owner)
    // ВИПРАВЛЕНО: Змінено метод з PUT на POST та узгоджено name з шаблоном show.blade.php
    Route::post('/establishments/{id}/update-menu', [EstablishmentController::class, 'updateMenu'])->name('establishment.menu.update');

    /*
    |--------------------------------------------------------------------------
    | МАРШРУТИ ДЛЯ ВЛАСНИКІВ (Власник редагує своє, Admin — все)
    |--------------------------------------------------------------------------
    */
    Route::get('/owner/establishment/create', [EstablishmentController::class, 'create'])->name('owner.establishment.create');
    Route::post('/owner/establishment', [EstablishmentController::class, 'store'])->name('owner.establishment.store');
    Route::get('/owner/establishment/{id}/edit', [EstablishmentController::class, 'edit'])->name('owner.establishment.edit');
    Route::put('/owner/establishment/{id}', [EstablishmentController::class, 'update'])->name('owner.establishment.update');


    Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');

    // ДУБЛЮЮЧІ МАРШРУТИ РЕДАГУВАННЯ ДЛЯ АДМІНА (Щоб уникнути конфліктів із URL-префіксами)
    Route::get('/admin/establishment/{id}/edit', [EstablishmentController::class, 'edit'])->name('admin.establishment.edit');
    Route::put('/admin/establishment/{id}', [EstablishmentController::class, 'update'])->name('admin.establishment.update');

    // Модерація та керування контентом
    Route::post('/admin/approve/{id}', [AuthController::class, 'approveEstablishment'])->name('admin.approve');
    Route::post('/admin/user/{id}/role', [AuthController::class, 'updateUserRole'])->name('admin.user.role');
    Route::delete('/admin/user/{id}', [AuthController::class, 'deleteUser'])->name('admin.user.delete');
    Route::delete('/admin/establishment/{id}', [AuthController::class, 'deleteEstablishment'])->name('admin.establishment.delete');
    Route::delete('/admin/review/{id}', [AuthController::class, 'deleteReview'])->name('admin.review.delete');
});
