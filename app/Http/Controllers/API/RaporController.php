<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryData;
use App\Models\Rapor;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RaporController extends Controller
{
    /**
     * Display a listing of the resource.
     * Supports filtering by tanggal (date) and kelas_id.
     */
    public function index(Request $request)
    {
        $query = Rapor::with('siswa.kelas');

        if ($request->has('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $query->whereHas('siswa.kelas', function ($q) use ($request) {
            $q->where('id', $request->kelas_id)
                ->where('status', 'Aktif');
        });


        $rapors = $query->latest('tanggal')->get();
        return response()->json($rapors, 200);
    }

    /**
     * Store report card (rapor) records.
     * Can bulk create based on class (kelas_id) and target (all students or only present students).
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kegiatan' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'target' => 'required|in:semua,hadir',
        ]);

        $tanggal = $request->tanggal;
        $kegiatan = $request->kegiatan;
        $kelasId = $request->kelas_id;
        $target = $request->target;

        // Query students in the selected class
        $siswaQuery = Siswa::where('kelas_id', $kelasId);

        if ($target === 'hadir') {
            // Filter only students who attended (absensis status 'Hadir') on that date
            $siswaQuery->whereHas('absensis', function ($q) use ($tanggal) {
                $q->whereDate('absen_date', $tanggal)
                    ->where('status', 'Hadir');
            });
        }

        $siswas = $siswaQuery->get();

        if ($siswas->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada siswa yang sesuai dengan filter kelas dan kehadiran.'
            ], 404);
        }

        $createdCount = 0;
        $updatedCount = 0;
        $savedRapors = [];

        foreach ($siswas as $siswa) {
            $exists = Rapor::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $tanggal)
                ->exists();

            $rapor = Rapor::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'tanggal' => $tanggal,
                ],
                [
                    'kegiatan' => $kegiatan,
                ]
            );

            if ($exists) {
                $updatedCount++;
            } else {
                $createdCount++;
            }

            $savedRapors[] = $rapor->load('siswa');
        }

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Tambah',
            'keterangan' => "Menyimpan rapor: $createdCount baru, $updatedCount diperbarui (kegiatan: $kegiatan)",
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => "Rapor berhasil disimpan! $createdCount baru dibuat, $updatedCount diperbarui.",
            'data' => $savedRapors
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rapor = Rapor::with('siswa.kelas')->findOrFail($id);
        return response()->json($rapor, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rapor = Rapor::findOrFail($id);

        $validated = $request->validate([
            'kegiatan' => 'required|string',
            'tanggal' => 'required|date',
            'siswa_id' => 'required|exists:siswas,id',
        ]);

        $rapor->update($validated);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Edit',
            'keterangan' => 'Memperbarui rapor siswa ID: ' . $validated['siswa_id'],
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Data rapor berhasil diperbarui!',
            'data' => $rapor->load('siswa.kelas')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $rapor = Rapor::with('siswa')->findOrFail($id);
        $namaSiswa = $rapor->siswa ? $rapor->siswa->nama_lengkap : 'Unknown';
        $rapor->delete();

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Hapus',
            'keterangan' => 'Menghapus rapor siswa: ' . $namaSiswa,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Data rapor berhasil dihapus!'
        ], 200);
    }
}
