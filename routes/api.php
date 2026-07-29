<?php

use App\Http\Controllers\API\Admin\AbsensiController;
use App\Http\Controllers\API\Admin\ActivityController;
use App\Http\Controllers\API\Admin\AuthController;
use App\Http\Controllers\API\Admin\DashboardController;
use App\Http\Controllers\API\Admin\HistoryController;
use App\Http\Controllers\API\Admin\HomeInformationController;
use App\Http\Controllers\API\Admin\KelasController;
use App\Http\Controllers\API\Admin\NewsController;
use App\Http\Controllers\API\Admin\RaporController;
use App\Http\Controllers\API\Admin\SiswaController;
use App\Http\Controllers\API\Admin\UsersController;
use App\Http\Controllers\API\Admin\FasilitasController;
use App\Http\Controllers\API\Public\HomeController;
use App\Http\Controllers\API\Public\ProfileController;
use App\Http\Controllers\API\Public\NewsController as PublicNewsController;
use App\Http\Controllers\API\Public\RaporController as PublicRaporController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rute Publik (Login)
Route::post('/login', [AuthController::class, 'login']);
Route::get('/home', [HomeController::class, 'index']);
Route::get('/profile', [ProfileController::class, 'index']);
Route::get('/public/news', [PublicNewsController::class, 'index']);
Route::get('/public/news/{id}', [PublicNewsController::class, 'show']);
Route::get('/public/rapor', [PublicRaporController::class, 'index']);

// Rute Terproteksi (Harus bawa Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::post('siswas/import', [SiswaController::class, 'import']);
    Route::apiResource('siswas', SiswaController::class);
    Route::patch('kelas/{id}/status', [KelasController::class, 'updateStatus']);
    Route::post('kelas/{id}/naik-kelas', [KelasController::class, 'naikKelas']);
    Route::apiResource('kelas', KelasController::class);
    // Rute Scan QR Code
    Route::post('absensis/scan', [AbsensiController::class, 'scan']);
    // Rute CRUD Absensi
    Route::apiResource('absensis', AbsensiController::class);
    Route::apiResource('rapor', RaporController::class);
    Route::apiResource('news', NewsController::class);
    Route::apiResource('home-information', HomeInformationController::class);
    Route::apiResource('fasilitas', FasilitasController::class);
    Route::apiResource('activity', ActivityController::class);
    Route::apiResource('users', UsersController::class);
    Route::get('history', [HistoryController::class, 'index']);
});
