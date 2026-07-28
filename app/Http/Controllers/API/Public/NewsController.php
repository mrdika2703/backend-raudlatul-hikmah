<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\Controller;
use App\Models\BeritaAcara;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of all news/activities sorted by latest.
     */
    public function index()
    {
        $news = BeritaAcara::latest('tanggal_kegiatan')->latest('created_at')->get();
        return response()->json($news, 200);
    }

    /**
     * Display the specified news/activity.
     */
    public function show(string $id)
    {
        $news = BeritaAcara::findOrFail($id);
        return response()->json($news, 200);
    }
}
