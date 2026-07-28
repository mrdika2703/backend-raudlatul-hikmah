<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\ProgramUnggulan;
use App\Models\User;
use App\Models\VisiMisi;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display profile page data.
     */
    public function index()
    {
        $programUnggulan = ProgramUnggulan::all();
        
        $visi = VisiMisi::where('kategori', 'Visi')->get();
        $misi = VisiMisi::where('kategori', 'Misi')->get();
        
        $fasilitas = Fasilitas::all();
        
        $users = User::select('name', 'category', 'photo', 'address', 'description')->get();

        return response()->json([
            'program_unggulan' => $programUnggulan,
            'visi' => $visi,
            'misi' => $misi,
            'fasilitas' => $fasilitas,
            'users' => $users
        ], 200);
    }
}
