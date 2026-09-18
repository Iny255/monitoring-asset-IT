<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class ChecklistItemController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $perusahaanId = $request->get('id_perusahaan');

        $query = ChecklistItem::with('perusahaan')->orderBy('urutan')->orderBy('id');

        if ($user->role === 'super_admin') {
            if ($perusahaanId === 'global') {
                $query->whereNull('id_perusahaan');
            } elseif (!empty($perusahaanId)) {
                $query->where('id_perusahaan', $perusahaanId);
            }
        }

        $items = $query->get();
        $perusahaans = $user->role === 'super_admin' ? Perusahaan::orderBy('nama_perusahaan')->get() : collect();

        return view('content.dashboard.checklist.item.index', compact('items', 'perusahaans', 'perusahaanId'));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'nama_item' => 'required|string|max:255',
            'kategori' => 'required|string|max:50',
            'keterangan' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ];

        if (auth()->user()->role === 'super_admin') {
            $validationRules['id_perusahaan'] = 'nullable|exists:perusahaans,id';
        }

        $request->validate($validationRules);

        $idPerusahaan = auth()->user()->role === 'super_admin'
            ? $request->id_perusahaan
            : auth()->user()->id_perusahaan;

        ChecklistItem::create([
            'id_perusahaan' => $idPerusahaan,
            'nama_item' => $request->nama_item,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'urutan' => $request->urutan ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('checklist.item.index')
            ->with('success', 'Item pemeriksaan berhasil ditambahkan.');
    }

    public function update(Request $request, ChecklistItem $item)
    {
        $validationRules = [
            'nama_item' => 'required|string|max:255',
            'kategori' => 'required|string|max:50',
            'keterangan' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];

        if (auth()->user()->role === 'super_admin') {
            $validationRules['id_perusahaan'] = 'nullable|exists:perusahaans,id';
        }

        $request->validate($validationRules);

        $data = [
            'nama_item' => $request->nama_item,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'urutan' => $request->urutan ?? $item->urutan,
            'is_active' => $request->has('is_active'),
        ];

        if (auth()->user()->role === 'super_admin') {
            $data['id_perusahaan'] = $request->id_perusahaan;
        }

        $item->update($data);

        return redirect()->route('checklist.item.index')
            ->with('success', 'Item pemeriksaan berhasil diperbarui.');
    }

    public function destroy(ChecklistItem $item)
    {
        $item->delete();

        return redirect()->route('checklist.item.index')
            ->with('success', 'Item pemeriksaan berhasil dihapus.');
    }
}
