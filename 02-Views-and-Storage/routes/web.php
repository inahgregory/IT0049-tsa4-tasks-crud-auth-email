<?php
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// These four pages are deliberately public.
Route::get('/', [TaskController::class, 'welcome'])->name('welcome');
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
Route::view('/about', 'about')->name('about');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [TaskController::class, 'welcome'])->name('dashboard');
    Route::resource('tasks', TaskController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::patch('/account', [AccountController::class, 'update'])->name('account.update');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
});
require __DIR__.'/auth.php';
