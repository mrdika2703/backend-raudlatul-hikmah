<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->get();
        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'position' => 'required|in:Kepala Sekolah,Operator,Guru',
            'category' => 'nullable|string|max:255',
            'photo' => 'nullable',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Tambah',
            'keterangan' => 'Menambahkan user: ' . $user->name . ' (' . $user->position . ')',
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'User berhasil ditambahkan!',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'password' => 'nullable|string|min:6',
            'position' => 'required|in:Kepala Sekolah,Operator,Guru',
            'category' => 'nullable|string|max:255',
            'photo' => 'nullable',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Edit',
            'keterangan' => 'Memperbarui user: ' . $user->name . ' (' . $user->position . ')',
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'User berhasil diperbarui!',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $namaUser = $user->name;

        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        HistoryData::create([
            'user_id' => $request->user()->id,
            'category' => 'Hapus',
            'keterangan' => 'Menghapus user: ' . $namaUser,
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'User berhasil dihapus!'
        ], 200);
    }
}
