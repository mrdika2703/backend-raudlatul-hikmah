<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryData;
use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Kelas::latest()->get();
        return response()->json($kelas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:255',
            'status' => 'nullable|string|in:Aktif,Lulus',
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = 'Aktif';
        }

        $kelas = Kelas::create($validated);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Tambah',
            'keterangan' => 'Menambahkan kelas: ' . $kelas->kelas . ' (' . $kelas->semester . ' - ' . $kelas->tahun_ajaran . ')',
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Kelas berhasil ditambahkan!',
            'data' => $kelas
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kelas = Kelas::findOrFail($id);
        return response()->json($kelas, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kelas = Kelas::findOrFail($id);

        $validated = $request->validate([
            'kelas' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:255',
            'status' => 'nullable|string|in:Aktif,Lulus',
        ]);

        $kelas->update($validated);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Edit',
            'keterangan' => 'Memperbarui kelas: ' . $kelas->kelas . ' (' . $kelas->semester . ' - ' . $kelas->tahun_ajaran . ')',
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Kelas berhasil diperbarui!',
            'data' => $kelas
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $kelas = Kelas::findOrFail($id);
        $namaKelas = $kelas->kelas;
        $kelas->delete();

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Hapus',
            'keterangan' => 'Menghapus kelas: ' . $namaKelas,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Kelas berhasil dihapus!'
        ], 200);
    }

    /**
     * Update the status of the specified class.
     */
    public function updateStatus(Request $request, string $id)
    {
        $kelas = Kelas::findOrFail($id);

        if ($request->has('status')) {
            $validated = $request->validate([
                'status' => 'required|string|in:Aktif,Lulus',
            ]);
            $kelas->status = $validated['status'];
        } else {
            $kelas->status = $kelas->status === 'Aktif' ? 'Lulus' : 'Aktif';
        }

        $kelas->save();

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Edit',
            'keterangan' => 'Mengubah status kelas ' . $kelas->kelas . ' menjadi ' . $kelas->status,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Status kelas berhasil diperbarui!',
            'data' => $kelas
        ], 200);
    }

    /**
     * Advance the class to the next semester or academic year.
     */
    public function naikKelas(Request $request, string $id)
    {
        $kelas = Kelas::findOrFail($id);

        $currentSemester = $kelas->semester;
        $currentTahun = $kelas->tahun_ajaran;

        if (strcasecmp($currentSemester, 'Ganjil') === 0) {
             $newSemester = 'Genap';
             $newTahun = $currentTahun;
        } else {
             $newSemester = 'Ganjil';
             $years = explode('/', $currentTahun);
             if (count($years) === 2) {
                 $year1 = (int)$years[0] + 1;
                 $year2 = (int)$years[1] + 1;
                 $newTahun = "$year1/$year2";
             } else {
                 $newTahun = $currentTahun;
             }
        }

        $validated = $request->validate([
            'semester' => 'nullable|string|max:255',
            'tahun_ajaran' => 'nullable|string|max:255',
        ]);

        $kelas->semester = $validated['semester'] ?? $newSemester;
        $kelas->tahun_ajaran = $validated['tahun_ajaran'] ?? $newTahun;
        $kelas->save();

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Edit',
            'keterangan' => 'Naik kelas: ' . $kelas->kelas . ' ke ' . $kelas->semester . ' - ' . $kelas->tahun_ajaran,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Kelas berhasil naik kelas!',
            'data' => $kelas
        ], 200);
    }
}
