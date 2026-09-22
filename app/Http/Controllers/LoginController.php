<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
    // REDIRECT KHUSUS (MISAL DARI SCAN QR)
    // =====================================
    $redirectTarget = $request->input('redirect', $request->query('redirect'));
    if (!empty($redirectTarget) && is_string($redirectTarget)) {
      if (str_starts_with($redirectTarget, '/') && !str_starts_with($redirectTarget, '//')) {
        return redirect($redirectTarget);
      }
    }

    // =====================================
    // REDIRECT BERDASARKAN ROLE
    // =====================================

    $rawRole = strtolower(trim((string) ($user->role ?? '')));
    $slugRole = Str::slug($rawRole, '_');

    // Normalisasi jika role berupa angka ID dari tabel roles
    if (is_numeric($rawRole)) {
      $roleModel = \App\Models\Role::find((int) $rawRole);
      $userRole = $roleModel ? strtolower(trim($roleModel->name)) : $rawRole;
    } else {
      $userRole = $slugRole;
    }

    // 1. Super Admin (slug 'super_admin', ID 1, variasi 'superadmin', 'super admin', dsb.)
    if (
      $userRole === 'super_admin' ||
      $rawRole === '1' ||
      $userRole === '1' ||
      $userRole === 'superadmin' ||
      $slugRole === 'super_admin'
    ) {
      return redirect()->route('dashboard.superadmin');
    }

    // 2. Petugas IT Support
    if ($userRole === 'petugas' || $rawRole === '2' || $slugRole === 'petugas_it_support') {
      return redirect()->route('dashboard.petugas');
    }

    // 3. Manager
    if ($userRole === 'manager') {
      return redirect()->route('dashboard.manager');
    }

    // 4. User / Karyawan
    if (in_array($userRole, ['user', 'karyawan', '3', '4']) || in_array($slugRole, ['user', 'karyawan'])) {
      return redirect()->route('aset-saya.index');
    }

    // 5. Cek role kustom dari tabel roles
    $roleRecord = $user->roleDefinition
      ?: (is_numeric($rawRole) ? \App\Models\Role::find((int) $rawRole)
        : (\App\Models\Role::where('name', $userRole)->orWhere('name', $slugRole)->first()
          ?: \App\Models\Role::whereRaw('LOWER(name) = ?', [strtolower($userRole)])->first()));
    if ($roleRecord) {
      if ($roleRecord->name === 'super_admin' || $roleRecord->can_manage_settings) {
        return redirect()->route('dashboard.superadmin');
      }

      if ($roleRecord->modules()->where('is_active', true)->where('url', 'dashboard/petugas')->exists()) {
        return redirect()->route('dashboard.petugas');
      }

      if ($roleRecord->modules()->where('is_active', true)->where('url', 'dashboard/superadmin')->exists()) {
        return redirect()->route('dashboard.superadmin');
      }

      if ($roleRecord->modules()->where('is_active', true)->where('url', 'dashboard/aset-saya')->exists()) {
        return redirect()->route('aset-saya.index');
      }

      $firstModule = $roleRecord->modules()->where('is_active', true)->whereNotNull('url')->orderBy('order')->first();
      if ($firstModule && $firstModule->url) {
        return redirect('/' . ltrim($firstModule->url, '/'));
      }

      return redirect()->route('aset-saya.index');
    }

    // 6. Fallback aman menggunakan getDashboardUrl() model User
    if (method_exists($user, 'getDashboardUrl')) {
      return redirect($user->getDashboardUrl());
    }

    Auth::logout();

    return redirect('/login')->withErrors([
      'login' => 'Role tidak dikenali',
    ]);
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
