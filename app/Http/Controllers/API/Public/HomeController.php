<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\Controller;
use App\Models\BeritaAcara;
use App\Models\Fasilitas;
use App\Models\Kegiatan;
use App\Models\ProgramUnggulan;
use App\Models\VisiMisi;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource for the public home page.
     */
    public function index()
    {
        $programUnggulan = ProgramUnggulan::take(3)->get();
        
        $visi = VisiMisi::where('kategori', 'Visi')->get();
        $misi = VisiMisi::where('kategori', 'Misi')->get();
        
        $fasilitas = Fasilitas::all();
        
        $ekstrakurikuler = Kegiatan::where('kategori', 'Ekstrakurikuler')->get();
        
        $berita = BeritaAcara::latest('tanggal_kegiatan')
            ->latest('created_at')
            ->take(4)
            ->get();

        return response()->json([
            'program_unggulan' => $programUnggulan,
            'visi' => $visi,
            'misi' => $misi,
            'fasilitas' => $fasilitas,
            'ekstrakurikuler' => $ekstrakurikuler,
            'berita' => $berita
        ], 200);
    }
}
