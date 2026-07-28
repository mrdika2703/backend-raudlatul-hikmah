<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Rapor;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RaporController extends Controller
{
    /**
     * Display public report card (rapor) data.
     * Groups reports by class and lists students with their attendance and reports.
     */
    public function index(Request $request)
    {
        $tanggalInput = $request->input('tanggal');
        $tanggal = $tanggalInput ? Carbon::parse($tanggalInput)->toDateString() : Carbon::today()->toDateString();

        $search = $request->input('search');
        $kelasId = $request->input('kelas_id'); // e.g. class ID (numeric) or class name

        // 1. Get Kelas data with today's status
        $classes = Kelas::where('status', 'Aktif')->get();
        $kelasData = [];

        foreach ($classes as $kls) {


            $reports = Rapor::whereDate('tanggal', $tanggal)
                ->whereHas('siswa', function ($q) use ($kls) {
                    $q->where('kelas_id', $kls->id);
                })->get();


            // Group unique kegiatan text
            $activities = $reports->pluck('kegiatan')
                ->filter()
                ->unique()
                ->implode(', ');

            if (empty($activities)) {
                $activities = '-';
            }

            $kelasData[] = [
                'id' => $kls->id,
                'kelas_nama' => $kls->kelas,
                'kegiatan' => $activities,
                'semester' => $kls->semester,
                'tahun_ajaran' => $kls->tahun_ajaran
            ];
        }

        // 2. Get Siswa details (can be searched and filtered)
        $siswaQuery = Siswa::with(['kelas', 'rapors' => function ($q) use ($tanggal) {
            $q->whereDate('tanggal', $tanggal);
        }, 'absensis' => function ($q) use ($tanggal) {
            $q->whereDate('absen_date', $tanggal);
        }]);

        // Filter by class if provided
        if ($kelasId && $kelasId !== 'Semua') {
            $siswaQuery->where('kelas_id', $kelasId);
        }

        // Search by name if provided
        if ($search) {
            $siswaQuery->where('nama_lengkap', 'like', '%' . $search . '%');
        }

        $students = $siswaQuery->get();
        $siswaData = [];

        foreach ($students as $student) {
            $todayRapor = $student->rapors->first();
            $todayAbsen = $student->absensis->first();

            $siswaData[] = [
                'id' => $student->id,
                'nama' => $student->nama_lengkap,
                'nisn' => $student->nisn,
                'jenis_kelamin' => $student->jenis_kelamin,
                'kelas_id' => $student->kelas_id,
                'kelas' => $student->kelas ? $student->kelas->kelas : '-',
                'absen' => $todayAbsen ? $todayAbsen->status : 'Belum Absen',
                'kegiatan' => $todayRapor ? $todayRapor->kegiatan : '-',
            ];
        }

        return response()->json([
            'tanggal' => $tanggal,
            'kelas_data' => $kelasData,
            'siswa_data' => $siswaData
        ], 200);
    }
}
