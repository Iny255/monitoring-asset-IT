<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
  public function index()
  {
    // =====================================
    // ANGKA KE TEXT
    // =====================================

    $teks = [
      0 => 'NOL',
      1 => 'SATU',
      2 => 'DUA',
      3 => 'TIGA',
      4 => 'EMPAT',
      5 => 'LIMA',
      6 => 'ENAM',
      7 => 'TUJUH',
      8 => 'DELAPAN',
      9 => 'SEMBILAN',
      10 => 'SEPULUH',
      11 => 'SEBELAS',
      12 => 'DUA BELAS',
      13 => 'TIGA BELAS',
      14 => 'EMPAT BELAS',
      15 => 'LIMA BELAS',
      16 => 'ENAM BELAS',
      17 => 'TUJUH BELAS',
      18 => 'DELAPAN BELAS',
      19 => 'SEMBILAN BELAS',
      20 => 'DUA PULUH',
    ];

    // =====================================
    // RANDOM OPERATOR
    // =====================================

    $operators = ['+', '-', '×'];

    $operator = $operators[array_rand($operators)];

    // =====================================
    // DEFAULT VARIABLE
    // =====================================

    $angka1 = 0;

    $angka2 = 0;

    $hasil = 0;

    // =====================================
    // GENERATE CAPTCHA
    // =====================================

    switch ($operator) {
      // ================= TAMBAH
      case '+':
        $angka1 = rand(1, 20);

        $angka2 = rand(1, 20);

        $hasil = $angka1 + $angka2;

        break;

      // ================= KURANG
      case '-':
        $angka1 = rand(5, 20);

        $angka2 = rand(1, 5);

        // agar tidak negatif
        if ($angka1 < $angka2) {
          [$angka1, $angka2] = [$angka2, $angka1];
        }

        $hasil = $angka1 - $angka2;

        break;

      // ================= KALI
      case '×':
        $angka1 = rand(1, 10);

        $angka2 = rand(1, 10);

        $hasil = $angka1 * $angka2;

        break;
    }

    // =====================================
    // RANDOM FORMAT
    // =====================================

    $captcha1 = rand(0, 1) ? $teks[$angka1] ?? $angka1 : $angka1;

    $captcha2 = rand(0, 1) ? $teks[$angka2] ?? $angka2 : $angka2;

    // =====================================
    // SIMPAN CAPTCHA
    // =====================================

    session([
      'captcha' => $hasil,
    ]);

    // =====================================
    // RETURN VIEW
    // =====================================

    return view('content.authentications.auth-login-basic', [
      'title' => 'Login-Basic',

      'captcha1' => $captcha1,

      'captcha2' => $captcha2,

      'operator' => $operator,
    ]);
  }

  // =====================================================
  // PROSES LOGIN
  // =====================================================

  public function authenticate(Request $request)
  {
    // =====================================
    // VALIDASI INPUT
    // =====================================

    $request->validate(
      [
        'username' => 'required',
        'password' => 'required',
        'captcha' => 'required',
      ],
      [
        'username.required' => 'Username wajib diisi',
        'password.required' => 'Password wajib diisi',
        'captcha.required' => 'Captcha wajib diisi',
      ]
    );

    // =====================================
    // VALIDASI CAPTCHA
    // =====================================

    if ((int) $request->captcha !== (int) session('captcha')) {
      return back()
        ->withInput()
        ->withErrors([
          'login' => 'Captcha salah',
        ]);
    }

    // =====================================
    // CEK USERNAME
    // =====================================

    $user = User::where('username', $request->username)->first();

    if (!$user) {
      return back()
        ->withInput()
        ->withErrors([
          'login' => 'Username tidak terdaftar',
        ]);
    }

    // =====================================
    // CEK PASSWORD
    // =====================================

    if (!Hash::check($request->password, $user->password)) {
      return back()
        ->withInput()
        ->withErrors([
          'login' => 'Password salah',
        ]);
    }

    // =====================================
    // LOGIN
    // =====================================

    Auth::login($user);

    $request->session()->regenerate();

    // =====================================
    // REDIRECT BERDASARKAN ROLE
    // =====================================

    switch ($user->role) {
      case 'super_admin':
        return redirect()->route('dashboard.superadmin');

      case 'manager':
        return redirect()->route('dashboard.manager');

      case 'petugas':
        return redirect()->route('dashboard.petugas');

      default:
        Auth::logout();

        return redirect('/login')->withErrors([
          'login' => 'Role tidak dikenali',
        ]);
    }
  }

  // =====================================================
  // LOGOUT
  // =====================================================

  public function logout(Request $request)
  {
    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
  }

  // =====================================================
  // REDIRECT DEFAULT
  // =====================================================

  protected function redirectTo()
  {
    return '/dashboard';
  }
}
