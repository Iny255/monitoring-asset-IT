<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

  public function index()
  {
    return view('content.authentications.auth-login-basic', [
      'title' => 'Login-Basic'
    ]);
  }
  public function authenticate(Request $request)
{
    $credentials = $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();

        switch ($user->role) {
            case 'manager':
                return redirect('/dashboard/manager');
            case 'petugas':
                return redirect('/dashboard/petugas');
            default:
                Auth::logout();
                return redirect('/login')->with('loginError', 'Role tidak dikenali.');
        }
    }

    return back()->with('loginError', 'Email atau password salah.');
}
  public function logout(Request $request)
  {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
  }
  protected function redirectTo()
  {
    return '/dashboard';
  }
}
