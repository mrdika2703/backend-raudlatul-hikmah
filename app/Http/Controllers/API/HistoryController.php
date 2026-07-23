<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryData;

class HistoryController extends Controller
{
    /**
     * Display a listing of the history logs.
     */
    public function index()
    {
        $histories = HistoryData::with('user')->latest('date')->latest('created_at')->get();
        return response()->json($histories, 200);
    }
}
