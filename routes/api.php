<?php

use App\Http\Controllers\API\AbsensiController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\HistoryController;
use App\Http\Controllers\API\HomeInformationController;
use App\Http\Controllers\API\KelasController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\RaporController;
use App\Http\Controllers\API\SiswaController;
use App\Http\Controllers\API\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rute Publik (Login)
Route::post('/login', [AuthController::class, 'login']);

// Rute Terproteksi (Harus bawa Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('dashboard', [DashboardController::class, 'index']);
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
    Route::apiResource('activity', ActivityController::class);
    Route::apiResource('users', UsersController::class);
    Route::get('history', [HistoryController::class, 'index']);
});
