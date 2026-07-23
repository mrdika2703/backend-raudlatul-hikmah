<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryData;
use App\Models\Kegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kegiatan = Kegiatan::latest()->get();
        return response()->json($kegiatan, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'gambar_1' => 'nullable',
            'gambar_2' => 'nullable',
            'gambar_3' => 'nullable',
        ]);

        foreach (['gambar_1', 'gambar_2', 'gambar_3'] as $gambarKey) {
            if ($request->hasFile($gambarKey)) {
                $validated[$gambarKey] = $request->file($gambarKey)->store('kegiatan', 'public');
            }
        }

        $kegiatan = Kegiatan::create($validated);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Tambah',
            'keterangan' => 'Menambahkan kegiatan: ' . $kegiatan->judul,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Kegiatan berhasil ditambahkan!',
            'data' => $kegiatan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        return response()->json($kegiatan, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'gambar_1' => 'nullable',
            'gambar_2' => 'nullable',
            'gambar_3' => 'nullable',
        ]);

        foreach (['gambar_1', 'gambar_2', 'gambar_3'] as $gambarKey) {
            if ($request->hasFile($gambarKey)) {
                if ($kegiatan->$gambarKey && Storage::disk('public')->exists($kegiatan->$gambarKey)) {
                    Storage::disk('public')->delete($kegiatan->$gambarKey);
                }
                $validated[$gambarKey] = $request->file($gambarKey)->store('kegiatan', 'public');
            }
        }

        $kegiatan->update($validated);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Edit',
            'keterangan' => 'Memperbarui kegiatan: ' . $kegiatan->judul,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Kegiatan berhasil diperbarui!',
            'data' => $kegiatan
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $judulKegiatan = $kegiatan->judul;

        foreach (['gambar_1', 'gambar_2', 'gambar_3'] as $gambarKey) {
            if ($kegiatan->$gambarKey && Storage::disk('public')->exists($kegiatan->$gambarKey)) {
                Storage::disk('public')->delete($kegiatan->$gambarKey);
            }
        }

        $kegiatan->delete();

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Hapus',
            'keterangan' => 'Menghapus kegiatan: ' . $judulKegiatan,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Kegiatan berhasil dihapus!'
        ], 200);
    }
}
