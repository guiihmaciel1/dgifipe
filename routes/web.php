<?php

use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluatorController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ManualUploadController;
use App\Http\Controllers\MarketRadarController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\SuperAdmin\CompanyController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'single-login', 'company-active'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/evaluator', [EvaluatorController::class, 'index'])->name('evaluator');
    Route::post('/evaluator/calculate', [EvaluatorController::class, 'calculate'])
        ->middleware('throttle:30,1')
        ->name('evaluator.calculate');

    Route::get('/market-radar', [MarketRadarController::class, 'index'])->name('market-radar');

    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/{session}', [HistoryController::class, 'show'])->name('history.show');

    Route::get('/manual-upload', [ManualUploadController::class, 'index'])->name('manual-upload');
    Route::post('/manual-upload', [ManualUploadController::class, 'store'])->middleware('throttle:10,1');

    Route::get('/compare', [CompareController::class, 'index'])->name('compare');

    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::put('/alerts/{alert}/dismiss', [AlertController::class, 'dismiss'])->name('alerts.dismiss');
    Route::get('/alerts/count', [AlertController::class, 'count'])->name('alerts.count');

    Route::middleware('role:admin,superadmin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('team', UserController::class)->except(['show']);
        Route::put('team/{user}/toggle', [UserController::class, 'toggle'])->name('team.toggle');
        Route::get('settings', [SettingsController::class, 'edit'])->name('settings');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    });

    Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::resource('companies', CompanyController::class)->except(['destroy']);
        Route::put('companies/{company}/toggle', [CompanyController::class, 'toggle'])->name('companies.toggle');
        Route::get('companies/{company}/users/create', [CompanyController::class, 'createUser'])->name('companies.users.create');
        Route::post('companies/{company}/users', [CompanyController::class, 'storeUser'])->name('companies.users.store');
        Route::put('users/{user}/toggle', [CompanyController::class, 'toggleUser'])->name('users.toggle');
    });
});
