<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\HistoryData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FasilitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fasilitas = Fasilitas::latest()->get();
        return response()->json($fasilitas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('fasilitas', 'public');
        }

        $fasilitas = Fasilitas::create($validated);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Tambah',
            'keterangan' => 'Menambahkan fasilitas: ' . $fasilitas->judul,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Fasilitas berhasil ditambahkan!',
            'data' => $fasilitas
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);
        return response()->json($fasilitas, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($fasilitas->gambar && Storage::disk('public')->exists($fasilitas->gambar)) {
                Storage::disk('public')->delete($fasilitas->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('fasilitas', 'public');
        }

        // Handle clear gambar
        if ($request->input('clear_gambar')) {
            if ($fasilitas->gambar && Storage::disk('public')->exists($fasilitas->gambar)) {
                Storage::disk('public')->delete($fasilitas->gambar);
            }
            $validated['gambar'] = null;
        }

        $fasilitas->update($validated);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Edit',
            'keterangan' => 'Memperbarui fasilitas: ' . $fasilitas->judul,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Fasilitas berhasil diperbarui!',
            'data' => $fasilitas
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);
        $judulFasilitas = $fasilitas->judul;

        if ($fasilitas->gambar && Storage::disk('public')->exists($fasilitas->gambar)) {
            Storage::disk('public')->delete($fasilitas->gambar);
        }

        $fasilitas->delete();

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Hapus',
            'keterangan' => 'Menghapus fasilitas: ' . $judulFasilitas,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Fasilitas berhasil dihapus!'
        ], 200);
    }
}
