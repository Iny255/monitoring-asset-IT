<?php

namespace App\Http\Controllers;

use App\Models\TicketCategory;
use Illuminate\Http\Request;

class TicketCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('nama_kategori', 'asc')->paginate(10);

        return view('content.dashboard.e_ticket.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'sla_jam' => 'required|integer|min:1',
        ]);

        TicketCategory::create([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
            'sla_jam' => $request->sla_jam,
        ]);

        return redirect()->route('ticket-categories.index')->with('success', 'Kategori tiket berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $category = TicketCategory::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'sla_jam' => 'required|integer|min:1',
        ]);

        $category->update([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
            'sla_jam' => $request->sla_jam,
        ]);

        return redirect()->route('ticket-categories.index')->with('success', 'Kategori tiket berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $category = TicketCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('ticket-categories.index')->with('success', 'Kategori tiket berhasil dihapus.');
    }
}
