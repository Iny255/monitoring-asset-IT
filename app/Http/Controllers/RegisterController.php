<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        return view('content.authentications.auth-register-basic', [
            'title' => 'Register-Basic'
        ]);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'username' => ['required', 'min:3', 'max:100', 'unique:users'],
            'email' => 'required|email:dns|unique:users',
            'password' => 'required|min:5|max:100',
            'role' => 'required|in:petugas,manager',
        ]);

        // Hash password
        $validateData['password'] = Hash::make($validateData['password']);

        // Create user
        User::create($validateData);

        // Redirect + flash message
        return redirect('/login')->with('success', 'Registration successful! Please login.');
    }
}
