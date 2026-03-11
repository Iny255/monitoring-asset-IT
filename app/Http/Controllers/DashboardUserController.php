<?php

namespace App\Http\Controllers;

use App\Models\Main;
use App\Models\Post;
use App\Models\User;
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

    $users = User::latest();

    if ($search) {
      $users = $users->where(function ($query) use ($search) {
        $query->where('username', 'like', '%' . $search . '%')
          ->orWhere('email', 'like', '%' . $search . '%');
      });
    }

    // Pagination
    $users = $users->paginate(5);

    return view('content.dashboard.user.index', compact('users'));
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
      'name'=> ['required','min:3', 'max:100', 'unique:users'],
      'email' => 'required|email|unique:users',
      'password' => 'required|min:5|max:100',
      'role' => 'required|string|max:20'
    ]);

    // Hash the password before storing it
    $validatedData['password'] = Hash::make($validatedData['password']);

    try {
      // Save the data using Eloquent
      $user = User::create($validatedData);

      if ($user) {
        // Redirect with success message
        return redirect('/dashboard/user')->with('success', 'Data user berhasil disimpan.');
      } else {
        // Redirect with error message if failed to save
        return redirect('/dashboard/user')->with('error', 'Gagal menyimpan data user.');
      }
    } catch (\Exception $e) {
      // Log error and redirect with error message
      Log::error($e->getMessage());
      return redirect('/dashboard/user/create')->with('error', 'Data user tidak berhasil disimpan. Kesalahan: ' . $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $user = User::find($id);

    if (!$user) {
      return redirect('/dashboard/user')->with('error', 'Data user tidak ditemukan.');
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
      'name'=>'required|string|max:100',
      'email' => 'required|email|max:100',
      'password' => 'nullable|string|min:8|confirmed', // Tambahkan 'confirmed' untuk validasi password konfirmasi
      'role' => 'required|string|in:petugas,manager',
    ]);

    $user = User::findOrFail($id);
    $user->username = $request->input('username');
    $user->name     = $request->input ('name'); 
    $user->email = $request->input('email');
    // Update password hanya jika diisi
    if ($request->filled('password')) {
      $user->password = bcrypt($request->input('password'));
    }

    $user->role = $request->input('role');
    $user->save();

    return redirect('/dashboard/user')->with('success', 'Data user berhasil disimpan.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}