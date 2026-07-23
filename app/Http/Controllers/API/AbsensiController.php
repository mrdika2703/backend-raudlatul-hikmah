<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\HistoryData;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    // 1. READ: Ambil daftar absen (Bisa difilter tanggal, kelas, atau search nama)
    public function index(Request $request)
    {
        $query = Absensi::with('siswa.kelas');

        // Filter berdasarkan tanggal (default: hari ini)
        $date = $request->query('date', Carbon::today()->toDateString());
        $query->whereDate('absen_date', $date);
        $query->whereHas('siswa.kelas', function ($q) {
            $q->where('status', 'Aktif');
        });

        // Filter berdasarkan kelas jika dipilih
        if ($request->has('kelas') && $request->kelas !== 'Semua Kelas') {
            $query->whereHas('siswa.kelas', function ($q) use ($request) {
                $q->where('id', $request->kelas);
            });
        }

        $absensis = $query->latest('absen_date')->get();

        return response()->json($absensis, 200);
    }

    // 2. SCAN QR CODE: Logika otomatis saat kamera mendeteksi QR
    public function scan(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
        ]);

        // Cari siswa berdasarkan NISN dengan kelas loaded
        $siswa = Siswa::with('kelas')->where('nisn', $request->nisn)->first();

        if (!$siswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak dikenali! Data siswa tidak ditemukan.'
            ], 404);
        }

        // Cek apakah siswa sudah memiliki rekam absensi HARI INI
        $hariIni = Carbon::today();
        $absenExist = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('absen_date', $hariIni)
            ->first();

        if ($absenExist) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Siswa sudah tercatat hari ini dengan status: ' . $absenExist->status,
                'data' => [
                    'nama_lengkap' => $siswa->nama_lengkap,
                    'kelas' => $siswa->kelas ? 'Kelas ' . $siswa->kelas->kelas : null,
                    'status' => $absenExist->status,
                    'waktu' => Carbon::parse($absenExist->absen_date)->format('H:i')
                ]
            ], 200);
        }

        // Jika belum ada, catat sebagai HADIR dengan waktu sekarang
        $absenBaru = Absensi::create([
            'siswa_id' => $siswa->id,
            'absen_date' => Carbon::now(),
            'status' => 'Hadir',
            'keterangan' => 'Absen otomatis via QR Code',
        ]);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Absen',
            'keterangan' => 'Scan QR absensi: ' . $siswa->nama_lengkap . ' (NISN: ' . $siswa->nisn . ') - Hadir',
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil absen hadir!',
            'data' => [
                'nama_lengkap' => $siswa->nama_lengkap,
                'kelas' => $siswa->kelas ? 'Kelas ' . $siswa->kelas->kelas : null,
                'status' => 'Hadir',
                'waktu' => Carbon::parse($absenBaru->absen_date)->format('H:i')
            ]
        ], 201);
    }

    // 3. CREATE MANUAL: Input absen manual oleh guru (misal untuk Izin/Sakit)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'absen_date' => 'required|date',
            'status' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'keterangan' => 'nullable|string',
        ]);

        $absen = Absensi::create($validated);
        $absen->load('siswa.kelas');

        $namaSiswa = $absen->siswa ? $absen->siswa->nama_lengkap : 'Unknown';
        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Tambah',
            'keterangan' => 'Menambahkan absensi manual: ' . $namaSiswa . ' - ' . $validated['status'],
            'date' => Carbon::now(),
        ]);

        if ($absen->siswa) {
            $absen->siswa->kelas = $absen->siswa->kelas ? 'Kelas ' . $absen->siswa->kelas->kelas : null;
        }

        return response()->json(['message' => 'Absensi berhasil disimpan', 'data' => $absen], 201);
    }

    // 4. UPDATE MANUAL: Ubah status absen
    public function update(Request $request, $id)
    {
        $absen = Absensi::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'keterangan' => 'nullable|string',
        ]);

        $absen->update($validated);
        $absen->load('siswa.kelas');

        $namaSiswa = $absen->siswa ? $absen->siswa->nama_lengkap : 'Unknown';
        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Edit',
            'keterangan' => 'Memperbarui absensi: ' . $namaSiswa . ' - ' . $validated['status'],
            'date' => Carbon::now(),
        ]);

        if ($absen->siswa) {
            $absen->siswa->kelas = $absen->siswa->kelas ? 'Kelas ' . $absen->siswa->kelas->kelas : null;
        }

        return response()->json(['message' => 'Absensi berhasil diperbarui', 'data' => $absen], 200);
    }

    // 5. DELETE: Hapus riwayat absen
    public function destroy(Request $request, $id)
    {
        $absen = Absensi::with('siswa')->findOrFail($id);
        $namaSiswa = $absen->siswa ? $absen->siswa->nama_lengkap : 'Unknown';
        $absen->delete();

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Hapus',
            'keterangan' => 'Menghapus data absensi: ' . $namaSiswa,
            'date' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Data absensi berhasil dihapus'], 200);
    }
}
