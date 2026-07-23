<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BeritaAcara;
use App\Models\HistoryData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = BeritaAcara::latest('tanggal_kegiatan')->latest('created_at')->get();
        return response()->json($news, 200);
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
            'tanggal_kegiatan' => 'required|date',
            'gambar_1' => 'nullable',
            'gambar_2' => 'nullable',
            'gambar_3' => 'nullable',
        ]);

        foreach (['gambar_1', 'gambar_2', 'gambar_3'] as $gambarKey) {
            if ($request->hasFile($gambarKey)) {
                $validated[$gambarKey] = $request->file($gambarKey)->store('news', 'public');
            }
        }

        $news = BeritaAcara::create($validated);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Tambah',
            'keterangan' => 'Menambahkan berita: ' . $news->judul,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Berita berhasil ditambahkan!',
            'data' => $news
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $news = BeritaAcara::findOrFail($id);
        return response()->json($news, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $news = BeritaAcara::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'tanggal_kegiatan' => 'required|date',
            'gambar_1' => 'nullable',
            'gambar_2' => 'nullable',
            'gambar_3' => 'nullable',
        ]);

        foreach (['gambar_1', 'gambar_2', 'gambar_3'] as $gambarKey) {
            if ($request->hasFile($gambarKey)) {
                if ($news->$gambarKey && Storage::disk('public')->exists($news->$gambarKey)) {
                    Storage::disk('public')->delete($news->$gambarKey);
                }
                $validated[$gambarKey] = $request->file($gambarKey)->store('news', 'public');
            }
        }

        // Handle clear photo requests (gambar_2 & gambar_3 only)
        foreach (['gambar_2', 'gambar_3'] as $gambarKey) {
            if ($request->input('clear_' . $gambarKey)) {
                if ($news->$gambarKey && Storage::disk('public')->exists($news->$gambarKey)) {
                    Storage::disk('public')->delete($news->$gambarKey);
                }
                $validated[$gambarKey] = null;
            }
        }

        $news->update($validated);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Edit',
            'keterangan' => 'Memperbarui berita: ' . $news->judul,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Berita berhasil diperbarui!',
            'data' => $news
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $news = BeritaAcara::findOrFail($id);
        $judulNews = $news->judul;

        foreach (['gambar_1', 'gambar_2', 'gambar_3'] as $gambarKey) {
            if ($news->$gambarKey && Storage::disk('public')->exists($news->$gambarKey)) {
                Storage::disk('public')->delete($news->$gambarKey);
            }
        }

        $news->delete();

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Hapus',
            'keterangan' => 'Menghapus berita: ' . $judulNews,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Berita berhasil dihapus!'
        ], 200);
    }
}
