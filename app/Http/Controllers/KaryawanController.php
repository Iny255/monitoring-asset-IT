<?php

namespace App\Http\Controllers;

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
        $perusahaanId = auth()->user()->id_perusahaan;
        $search = $request->search;

        $karyawans = Karyawan::where('id_perusahaan', $perusahaanId);

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
            ],
            [
                'kode_karyawan.unique' => 'Kode karyawan sudah terdaftar.',
            ]
        );

        try {
            $validated['id_perusahaan'] = auth()->user()->id_perusahaan;

            Karyawan::create($validated);

            return redirect()->route('karyawan.index')
                ->with('success', 'Data karyawan berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Error: ' . $e->getMessage()); // 🔥 biar kelihatan error asli
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        // 🔒 pastikan tidak beda perusahaan
        if ($karyawan->id_perusahaan != auth()->user()->id_perusahaan) {
            abort(403);
        }

        $validated = $request->validate([
            'kode_karyawan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('karyawans', 'kode_karyawan')->ignore($karyawan->id),
            ],
            'nama_karyawan' => 'required|string|max:100',
            'jabatan' => 'required|string|max:50',
            'divisi' => 'required|string|max:50',
        ]);

        try {
            $karyawan->update($validated);

            return redirect()->route('karyawan.index')
                ->with('success', 'Data karyawan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Karyawan $karyawan)
    {
        if ($karyawan->id_perusahaan != auth()->user()->id_perusahaan) {
            abort(403);
        }

        try {
            $karyawan->delete();

            return redirect()->route('karyawan.index')
                ->with('success', 'Karyawan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
   
}
