<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryData;
use App\Models\ProgramUnggulan;
use App\Models\VisiMisi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible via query parameter ?type=program_unggulan or ?type=visi_misi.
     * If no type provided, returns both in one object.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');

        if ($type === 'program_unggulan') {
            return response()->json(ProgramUnggulan::latest()->get(), 200);
        }

        if ($type === 'visi_misi') {
            return response()->json(VisiMisi::all(), 200);
        }

        return response()->json([
            'program_unggulan' => ProgramUnggulan::latest()->get(),
            'visi_misi' => VisiMisi::all(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * Requires 'type' in body: 'program_unggulan' or 'visi_misi'.
     */
    public function store(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'program_unggulan') {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'keterangan' => 'required|string',
                'icon' => 'nullable',
            ]);

            if ($request->hasFile('icon')) {
                $validated['icon'] = $request->file('icon')->store('program_unggulan', 'public');
            }

            $item = ProgramUnggulan::create($validated);

            HistoryData::create([
                'user_id' => $request->user()->id,
                'category' => 'Tambah',
                'keterangan' => 'Menambahkan program unggulan: ' . $item->judul,
                'date' => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Program Unggulan berhasil ditambahkan!',
                'data' => $item
            ], 201);
        }

        if ($type === 'visi_misi') {
            $validated = $request->validate([
                'kategori' => 'required|in:Visi,Misi',
                'keterangan' => 'required|string',
            ]);

            $item = VisiMisi::create($validated);

            HistoryData::create([
                'user_id' => $request->user()->id,
                'category' => 'Tambah',
                'keterangan' => 'Menambahkan ' . $validated['kategori'],
                'date' => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Visi/Misi berhasil ditambahkan!',
                'data' => $item
            ], 201);
        }

        return response()->json([
            'message' => 'Tipe informasi tidak valid. Gunakan type=program_unggulan atau type=visi_misi.'
        ], 422);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $type = $request->query('type', $request->input('type'));

        if ($type === 'program_unggulan') {
            $item = ProgramUnggulan::findOrFail($id);
            return response()->json($item, 200);
        }

        if ($type === 'visi_misi') {
            $item = VisiMisi::findOrFail($id);
            return response()->json($item, 200);
        }

        return response()->json([
            'message' => 'Tipe informasi tidak valid. Gunakan type=program_unggulan atau type=visi_misi.'
        ], 422);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $type = $request->input('type', $request->query('type'));

        if ($type === 'program_unggulan') {
            $item = ProgramUnggulan::findOrFail($id);

            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'keterangan' => 'required|string',
                'icon' => 'nullable',
            ]);

            if ($request->hasFile('icon')) {
                if ($item->icon && Storage::disk('public')->exists($item->icon)) {
                    Storage::disk('public')->delete($item->icon);
                }
                $validated['icon'] = $request->file('icon')->store('program_unggulan', 'public');
            }

            $item->update($validated);

            HistoryData::create([
                'user_id' => $request->user()->id,
                'category' => 'Edit',
                'keterangan' => 'Memperbarui program unggulan: ' . $item->judul,
                'date' => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Program Unggulan berhasil diperbarui!',
                'data' => $item
            ], 200);
        }

        if ($type === 'visi_misi') {
            $item = VisiMisi::findOrFail($id);

            $validated = $request->validate([
                'kategori' => 'required|in:Visi,Misi',
                'keterangan' => 'required|string',
            ]);

            $item->update($validated);

            HistoryData::create([
                'user_id' => $request->user()->id,
                'category' => 'Edit',
                'keterangan' => 'Memperbarui ' . $validated['kategori'],
                'date' => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Visi/Misi berhasil diperbarui!',
                'data' => $item
            ], 200);
        }

        return response()->json([
            'message' => 'Tipe informasi tidak valid. Gunakan type=program_unggulan atau type=visi_misi.'
        ], 422);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $type = $request->input('type', $request->query('type'));

        if ($type === 'program_unggulan') {
            $item = ProgramUnggulan::findOrFail($id);

            if ($item->icon && Storage::disk('public')->exists($item->icon)) {
                Storage::disk('public')->delete($item->icon);
            }

            $judulProgram = $item->judul;
            $item->delete();

            HistoryData::create([
                'user_id' => $request->user()->id,
                'category' => 'Hapus',
                'keterangan' => 'Menghapus program unggulan: ' . $judulProgram,
                'date' => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Program Unggulan berhasil dihapus!'
            ], 200);
        }

        if ($type === 'visi_misi') {
            $item = VisiMisi::findOrFail($id);
            $kategoriVisiMisi = $item->kategori;
            $item->delete();

            HistoryData::create([
                'user_id' => $request->user()->id,
                'category' => 'Hapus',
                'keterangan' => 'Menghapus ' . $kategoriVisiMisi,
                'date' => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Visi/Misi berhasil dihapus!'
            ], 200);
        }

        return response()->json([
            'message' => 'Tipe informasi tidak valid. Gunakan type=program_unggulan atau type=visi_misi.'
        ], 422);
    }
}
