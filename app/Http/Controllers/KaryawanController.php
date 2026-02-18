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
        $search = $request->input('search');

        $karyawans = Karyawan::latest();

        if ($search) {
            $karyawans = $karyawans->where(function ($query) use ($search) {
                $query->where('nama_karyawan', 'like', '%' . $search . '%')->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $karyawans = $karyawans->paginate(6);

        return view('content.dashboard.karyawan.index', compact('karyawans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.dashboard.karyawan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(
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
            Karyawan::create($validatedData);

            return redirect('/dashboard/karyawan')
                ->with('success', 'Data karyawan berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Karyawan $karyawan)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Karyawan $karyawan)
    {
        return view('content.dashboard.karyawan.edit', compact('karyawan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        $validatedData = $request->validate(
            [
                'kode_karyawan' => [
                    'required',
                    'string',
                    'max:7',
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
            $karyawan->update($validatedData);

            return redirect('/dashboard/karyawan')
                ->with('success', 'Data karyawan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->back()
            ->with('success', 'Karyawan berhasil dihapus');
    }
}
