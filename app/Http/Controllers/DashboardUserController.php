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

    // 🔥 ambil data user dengan relasi perusahaan
    $users = User::with('perusahaan.parent');

    // 🔍 fitur search
    if ($search) {
      $users->where(function ($query) use ($search) {
        $query->where('username', 'like', '%' . $search . '%')
          ->orWhere('name', 'like', '%' . $search . '%')
          ->orWhere('email', 'like', '%' . $search . '%');
      });
    }

    // 🔥 pagination
    $users = $users->latest()->paginate(10)->appends($request->query());

    // 🔥 ambil semua perusahaan & cabangs & karyawans
    $parentPerusahaans = Perusahaan::with('cabangs')->whereNull('parent_id')->orderBy('nama_perusahaan')->get();
    $perusahaans = Perusahaan::with('parent')->orderBy('nama_perusahaan')->get();
    $karyawans = \App\Models\Karyawan::orderBy('nama_karyawan')->get();

    return view('content.dashboard.user.index', compact('users', 'perusahaans', 'parentPerusahaans', 'karyawans'));
  }

  public function hapususer(int $id)
  {
    $result = Main::Hapus('users', ['id' => $id]);
    if ($result == 1) {
      return redirect('/dashboard/user')->with('success', 'Data user berhasil dihapus.');
    } else {
      return redirect('/dashboard/user')->with('error', 'Gagal menghapus data user.');
    }
  }

  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'username' => ['required', 'min:3', 'max:100', 'unique:users'],
      'name' => ['required', 'min:3', 'max:100'],
      'email' => 'required|email|unique:users',
      'password' => 'required|min:5|max:100',
      'role' => 'required|in:user,karyawan,petugas,super_admin',

      'id_perusahaan' => 'nullable|exists:perusahaans,id',
      'karyawan_id' => 'nullable|exists:karyawans,id',
    ]);

    $validatedData['name'] = strtoupper($validatedData['name']);
    $validatedData['email'] = strtolower($validatedData['email']);

    if ($request->role === 'super_admin') {
      $validatedData['id_perusahaan'] = null;
    }

    $validatedData['password'] = Hash::make($validatedData['password']);

    User::create($validatedData);

    return redirect('/dashboard/user')->with('success', 'Data user berhasil disimpan.');
  }

  public function show(string $id)
  {
    $authUser = auth()->user();

    if ($authUser->role !== 'super_admin') {
      abort(404);
    }

    $user = User::find($id);

    if (!$user) {
      abort(404);
    }

    if ($authUser->id == $user->id) {
      abort(404);
    }

    return view('content.dashboard.user.detail', compact('user'));
  }

  public function update(Request $request, string $id)
  {
    $request->validate([
      'username' => 'required|string|max:100',
      'name' => 'required|string|max:100',
      'email' => 'required|email|max:100',
      'password' => 'nullable|string|min:8',
      'role' => 'required|in:user,karyawan,petugas,super_admin',
      'id_perusahaan' => 'nullable|exists:perusahaans,id',
      'karyawan_id' => 'nullable|exists:karyawans,id',
    ]);

    $user = User::findOrFail($id);

    $user->username = $request->username;
    $user->name = strtoupper($request->name);
    $user->email = strtolower($request->email);

    if ($request->filled('password')) {
      $user->password = bcrypt($request->password);
    }

    $user->role = $request->role;
    $user->karyawan_id = $request->karyawan_id;

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
