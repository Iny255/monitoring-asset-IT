<?php

namespace App\Http\Controllers;

use App\Models\Main;
use App\Models\Post;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $karyawans = Karyawan::query();

        if ($search) {
            $karyawans->where(function ($query) use ($search) {
                $query->where('nama_karyawan', 'like', "%{$search}%")
                      ->orWhere('kode_karyawan', 'like', "%{$search}%");
            });
        }

        $karyawans = $karyawans->latest()->paginate(5);

        return view('content.dashboard.karyawan.index', compact('karyawans'));
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'kode_karyawan' => 'required|string|max:20|unique:karyawans,kode_karyawan',
                'nama_karyawan' => 'required|string|max:100',
                'jabatan' => 'required|string|max:50',
                'divisi' => 'required|string|max:50',
                'perusahaan' => 'required|string|max:50',
            ],
            [
                'kode_karyawan.unique' => 'Kode karyawan sudah terdaftar.',
            ]
        );

        try {
            Karyawan::create($validated);

            return redirect()->route('karyawan.index')
                ->with('success', 'Data karyawan berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate(
            [
                'kode_karyawan' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('karyawans', 'kode_karyawan')->ignore($karyawan->id),
                ],
                'nama_karyawan' => 'required|string|max:100',
                'jabatan' => 'required|string|max:50',
                'divisi' => 'required|string|max:50',
                'perusahaan' => 'required|string|max:50',
            ],
            [
                'kode_karyawan.unique' => 'Kode karyawan sudah digunakan.',
            ]
        );

        try {
            $karyawan->update($validated);

            return redirect()->route('karyawan.index')
                ->with('success', 'Data karyawan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Karyawan $karyawan)
    {
        try {
            $karyawan->delete();

            return redirect()->route('karyawan.index')
                ->with('success', 'Karyawan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}
