<?php

namespace App\Http\Controllers;

use App\Models\Main;
use App\Models\Post;
use App\Models\User;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class DashboardUserController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $search = $request->input('search');

    // 🔥 ambil data user
    $users = User::query();

    // 🔍 fitur search
    if ($search) {
      $users->where(function ($query) use ($search) {
        $query->where('username', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%');
      });
    }

    // 🔥 pagination
    $users = $users->latest()->paginate(5);

    // 🔥 ambil semua perusahaan (untuk dropdown create & edit)
    $perusahaans = Perusahaan::all();

    return view('content.dashboard.user.index', compact('users', 'perusahaans'));
  }

  public function hapususer($id)
  {
    $result = Main::Hapus('users', ['id' => $id]);
    if ($result == 1) {
      return redirect('/dashboard/user')->with('success', 'Data user berhasil dihapus.');
    } else {
      return redirect('/dashboard/user')->with('error', 'Gagal menghapus data user.');
    }
  }
  /**
   * Show the form for creating a new resource.
   */

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'username' => ['required', 'min:3', 'max:100', 'unique:users'],
      'name' => ['required', 'min:3', 'max:100'],
      'email' => 'required|email|unique:users',
      'password' => 'required|min:5|max:100',
      'role' => 'required|in:petugas,manager,super_admin',

      // 🔥 kalau bukan super_admin wajib perusahaan
      'id_perusahaan' => 'nullable|exists:perusahaans,id',
    ]);

    // 🔥 kalau super_admin → perusahaan null
    if ($request->role === 'super_admin') {
      $validatedData['id_perusahaan'] = null;
    }

    $validatedData['password'] = Hash::make($validatedData['password']);

    User::create($validatedData);

    return redirect('/dashboard/user')->with('success', 'Data user berhasil disimpan.');
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $authUser = auth()->user();

    // 🔥 HANYA SUPER ADMIN BOLEH AKSES
    if ($authUser->role !== 'super_admin') {
      abort(404); // langsung not found
    }

    $user = User::find($id);

    // 🔥 kalau data tidak ada
    if (!$user) {
      abort(404);
    }

    // 🔥 OPTIONAL: super admin tidak boleh lihat dirinya sendiri
    if ($authUser->id == $user->id) {
      abort(404);
    }

    return view('content.dashboard.user.detail', compact('user'));
  }

  /**
   * Show the form for editing the specified resource.
   */

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $request->validate([
      'username' => 'required|string|max:100',
      'name' => 'required|string|max:100',
      'email' => 'required|email|max:100',
      'password' => 'nullable|string|min:8',
      'role' => 'required|in:petugas,manager,super_admin',
      'id_perusahaan' => 'nullable|exists:perusahaans,id',
    ]);

    $user = User::findOrFail($id);

    $user->username = $request->username;
    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->filled('password')) {
      $user->password = bcrypt($request->password);
    }

    $user->role = $request->role;

    // 🔥 kalau super_admin → null
    if ($request->role === 'super_admin') {
      $user->id_perusahaan = null;
    } else {
      $user->id_perusahaan = $request->id_perusahaan;
    }

    $user->save();

    return redirect('/dashboard/user')->with('success', 'Data user berhasil diperbarui.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
