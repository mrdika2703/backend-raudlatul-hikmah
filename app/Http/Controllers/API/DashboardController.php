<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Absensi;
use App\Models\Rapor;
use App\Models\User;
use App\Models\HistoryData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Statistik total data
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalUsers = User::count();
        $totalRapor = Rapor::count();

        // 2. Pembagian gender siswa (L / P)
        $genderCounts = Siswa::select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin')
            ->toArray();

        $genderBreakdown = [
            'laki_laki' => $genderCounts['L'] ?? $genderCounts['Laki-laki'] ?? 0,
            'perempuan' => $genderCounts['P'] ?? $genderCounts['Perempuan'] ?? 0,
        ];

        // 3. Status absensi hari ini (Hadir, Sakit, Izin, Alpa)
        $today = Carbon::today()->toDateString();
        $absensiCounts = Absensi::whereDate('absen_date', $today)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $absensiToday = [
            'hadir' => $absensiCounts['Hadir'] ?? 0,
            'sakit' => $absensiCounts['Sakit'] ?? 0,
            'izin' => $absensiCounts['Izin'] ?? 0,
            'alpa' => $absensiCounts['Alpa'] ?? 0,
        ];

        // 4. Statistik siswa per kelas aktif
        $kelasStats = Kelas::where('status', 'Aktif')
            ->withCount('siswa')
            ->get()
            ->map(function ($kelas) {
                return [
                    'id' => $kelas->id,
                    'kelas' => $kelas->kelas,
                    'semester' => $kelas->semester,
                    'tahun_ajaran' => $kelas->tahun_ajaran,
                    'total_siswa' => $kelas->siswa_count,
                ];
            });

        // 5. 5 Aktivitas log terbaru (History Data)
        $recentHistories = HistoryData::with('user:id,name')
            ->latest('date')
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($history) {
                return [
                    'id' => $history->id,
                    'user_name' => $history->user ? $history->user->name : 'System',
                    'category' => $history->category,
                    'keterangan' => $history->keterangan,
                    'date' => $history->date->toDateTimeString(),
                ];
            });

        return response()->json([
            'stats' => [
                'total_siswa' => $totalSiswa,
                'total_kelas' => $totalKelas,
                'total_users' => $totalUsers,
                'total_rapor' => $totalRapor,
            ],
            'gender_breakdown' => $genderBreakdown,
            'absensi_today' => $absensiToday,
            'kelas_stats' => $kelasStats,
            'recent_histories' => $recentHistories,
        ], 200);
    }
}
