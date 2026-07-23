<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryData;
use App\Models\Siswa;
use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    // 1. READ: Ambil semua data siswa (diurutkan dari yang terbaru)
    public function index()
    {
        $siswas = Siswa::with('kelas')->latest()->get();

        return response()->json($siswas, 200);
    }

    // 2. CREATE: Simpan siswa baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|unique:siswas,nisn',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'kelas' => 'required|string',
        ], [
            'nisn.unique' => 'NISN ini sudah terdaftar di sistem.'
        ]);

        // Map string kelas (e.g. "Kelas A") to kelas_id
        if (is_numeric($validated['kelas'])) {
            $kelasObj = Kelas::find($validated['kelas']);
        } else {
            $kelasName = str_replace('Kelas ', '', $validated['kelas']);
            $kelasObj = Kelas::where('kelas', $kelasName)->first();
        }

        if (!$kelasObj) {
            $kelasName = str_replace('Kelas ', '', $validated['kelas']);
            $kelasObj = Kelas::firstOrCreate([
                'kelas' => $kelasName,
                'semester' => 'Ganjil',
                'tahun_ajaran' => '2023/2024'
            ]);
        }

        $validated['kelas_id'] = $kelasObj->id;
        unset($validated['kelas']);

        $siswa = Siswa::create($validated);
        $siswa->load('kelas');

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Tambah',
            'keterangan' => 'Menambahkan siswa: ' . $siswa->nama_lengkap . ' (NISN: ' . $siswa->nisn . ')',
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Data siswa berhasil ditambahkan!',
            'data' => $siswa
        ], 201);
    }

    // 3. SHOW: Ambil detail 1 siswa (opsional)
    public function show($id)
    {
        $siswa = Siswa::with('kelas')->findOrFail($id);
        return response()->json($siswa, 200);
    }

    // 4. UPDATE: Perbarui data siswa
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nisn' => 'required|string|unique:siswas,nisn,' . $id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'kelas' => 'required|string',
        ]);

        // Map string kelas (e.g. "Kelas A") to kelas_id
        if (is_numeric($validated['kelas'])) {
            $kelasObj = Kelas::find($validated['kelas']);
        } else {
            $kelasName = str_replace('Kelas ', '', $validated['kelas']);
            $kelasObj = Kelas::where('kelas', $kelasName)->first();
        }

        if (!$kelasObj) {
            $kelasName = str_replace('Kelas ', '', $validated['kelas']);
            $kelasObj = Kelas::firstOrCreate([
                'kelas' => $kelasName,
                'semester' => 'Ganjil',
                'tahun_ajaran' => '2023/2024'
            ]);
        }

        $validated['kelas_id'] = $kelasObj->id;
        unset($validated['kelas']);

        $siswa->update($validated);
        $siswa->load('kelas');

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Edit',
            'keterangan' => 'Memperbarui data siswa: ' . $siswa->nama_lengkap . ' (NISN: ' . $siswa->nisn . ')',
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui!',
            'data' => $siswa
        ], 200);
    }

    // 5. DELETE: Hapus siswa
    public function destroy(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $namaSiswa = $siswa->nama_lengkap;
        $nisnSiswa = $siswa->nisn;
        $siswa->delete();

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Hapus',
            'keterangan' => 'Menghapus siswa: ' . $namaSiswa . ' (NISN: ' . $nisnSiswa . ')',
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Data siswa berhasil dihapus!'
        ], 200);
    }
}
