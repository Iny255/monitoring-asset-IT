<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketReply;
use App\Models\Inventaris;
use App\Models\Karyawan;
use App\Models\Lokasi;
use App\Models\Perusahaan;
use App\Models\User;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::with(['user', 'karyawan', 'perusahaan', 'lokasi', 'inventaris.dataAset', 'category', 'assignee']);

        // Scope company access & role filtering
        if ($user->role === 'super_admin') {
            if ($request->filled('perusahaan_id')) {
                $query->where('id_perusahaan', $request->perusahaan_id);
            }
        } elseif (in_array($user->role, ['user', 'karyawan'])) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->karyawan_id) {
                    $q->orWhere('karyawan_id', $user->karyawan_id);
                }
            });
        } else {
            $accessibleCompanyIds = $user->getAccessibleCompanyIds();
            if ($accessibleCompanyIds) {
                if ($request->filled('perusahaan_id') && $accessibleCompanyIds->contains($request->perusahaan_id)) {
                    $query->where('id_perusahaan', $request->perusahaan_id);
                } else {
                    $query->whereIn('id_perusahaan', $accessibleCompanyIds);
                }
            } elseif ($user->id_perusahaan) {
                $query->where('id_perusahaan', $user->id_perusahaan);
            }
        }

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_tiket', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter priority
        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }

        // Filter category
        if ($request->filled('category_id')) {
            $query->where('ticket_category_id', $request->category_id);
        }

        // Stats calculation (scoped to user's company access and role)
        $scopedBaseQuery = Ticket::query();
        if ($user->role === 'super_admin') {
            if ($request->filled('perusahaan_id')) {
                $scopedBaseQuery->where('id_perusahaan', $request->perusahaan_id);
            }
        } elseif (in_array($user->role, ['user', 'karyawan'])) {
            $scopedBaseQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->karyawan_id) {
                    $q->orWhere('karyawan_id', $user->karyawan_id);
                }
            });
        } else {
            $accessibleCompanyIds = $user->getAccessibleCompanyIds();
            if ($accessibleCompanyIds) {
                $scopedBaseQuery->whereIn('id_perusahaan', $accessibleCompanyIds);
            } elseif ($user->id_perusahaan) {
                $scopedBaseQuery->where('id_perusahaan', $user->id_perusahaan);
            }
        }

        $stats = [
            'total' => (clone $scopedBaseQuery)->count(),
            'open' => (clone $scopedBaseQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $scopedBaseQuery)->where('status', 'in_progress')->count(),
            'pending' => (clone $scopedBaseQuery)->where('status', 'pending')->count(),
            'resolved' => (clone $scopedBaseQuery)->where('status', 'resolved')->count(),
            'closed' => (clone $scopedBaseQuery)->where('status', 'closed')->count(),
        ];

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());
        $categories = TicketCategory::all();
        $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();

        // Inventaris list for Modal Create Form
        $inventarisQuery = Inventaris::with(['dataAset.kategori', 'perusahaan']);
        if ($user->role !== 'super_admin') {
            $accessibleCompanyIds = $user->getAccessibleCompanyIds();
            if ($accessibleCompanyIds) {
                $inventarisQuery->whereIn('perusahaan_id', $accessibleCompanyIds);
            } elseif ($user->id_perusahaan) {
                $inventarisQuery->where('perusahaan_id', $user->id_perusahaan);
            }
        }
        $inventarisList = $inventarisQuery->get();

        return view('content.dashboard.e_ticket.index', compact(
            'tickets', 'stats', 'categories', 'perusahaans', 'lokasis', 'karyawans', 'inventarisList'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $categories = TicketCategory::all();
        $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();

        // Inventaris list
        $inventarisQuery = Inventaris::with(['dataAset.kategori', 'perusahaan']);
        if ($user->role !== 'super_admin') {
            $accessibleCompanyIds = $user->getAccessibleCompanyIds();
            if ($accessibleCompanyIds) {
                $inventarisQuery->whereIn('perusahaan_id', $accessibleCompanyIds);
            } elseif ($user->id_perusahaan) {
                $inventarisQuery->where('perusahaan_id', $user->id_perusahaan);
            }
        }
        $inventarisList = $inventarisQuery->get();

        return view('content.dashboard.e_ticket.create', compact('categories', 'perusahaans', 'lokasis', 'karyawans', 'inventarisList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'prioritas' => 'required|in:low,medium,high,urgent',
            'inventaris_id' => 'nullable|exists:inventaris,id',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'id_perusahaan' => 'nullable|exists:perusahaans,id',
            'lokasi_id' => 'nullable|exists:lokasis,id',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx,zip|max:5120',
        ]);

        $user = Auth::user();
        $lampiranPath = null;

        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('tickets', 'public');
        }

        $nomorTiket = Ticket::generateNomorTiket();

        $idPerusahaan = ($user->role === 'super_admin' && $request->filled('id_perusahaan'))
            ? $request->id_perusahaan
            : ($user->id_perusahaan ?? $request->id_perusahaan);

        $ticket = Ticket::create([
            'nomor_tiket' => $nomorTiket,
            'user_id' => $user->id,
            'karyawan_id' => $request->karyawan_id,
            'id_perusahaan' => $idPerusahaan,
            'lokasi_id' => $request->lokasi_id,
            'inventaris_id' => $request->inventaris_id,
            'ticket_category_id' => $request->ticket_category_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'prioritas' => $request->prioritas,
            'status' => 'open',
            'lampiran' => $lampiranPath,
        ]);

        return redirect()->route('e-ticket.show', $ticket->id)->with('success', "Tiket {$nomorTiket} berhasil dibuat.");
    }

    public function show($id)
    {
        $ticket = Ticket::with([
            'user',
            'karyawan',
            'perusahaan',
            'lokasi',
            'inventaris.dataAset.kategori',
            'category',
            'assignee',
            'maintenance',
            'replies.user'
        ])->findOrFail($id);

        $petugasList = User::whereIn('role', ['petugas', 'super_admin'])->orderBy('name')->get();

        return view('content.dashboard.e_ticket.show', compact('ticket', 'petugasList'));
    }

    public function storeReply(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'pesan' => 'required|string',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx,zip|max:5120',
            'is_internal_note' => 'nullable|boolean',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('ticket_replies', 'public');
        }

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'pesan' => $request->pesan,
            'lampiran' => $lampiranPath,
            'is_internal_note' => $request->boolean('is_internal_note'),
        ]);

        // Auto change open ticket to in_progress upon response
        if ($ticket->status === 'open' && Auth::user()->role !== 'user') {
            $ticket->update([
                'status' => 'in_progress',
                'responded_at' => Carbon::now(),
            ]);
        }

        return redirect()->route('e-ticket.show', $ticket->id)->with('success', 'Balasan berhasil dikirim.');
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'status' => 'required|in:open,in_progress,pending,resolved,closed,rejected',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $updateData = [
            'status' => $request->status,
            'assigned_to' => $request->assigned_to,
        ];

        if ($request->status === 'in_progress' && !$ticket->responded_at) {
            $updateData['responded_at'] = Carbon::now();
        }

        if ($request->status === 'resolved' && !$ticket->resolved_at) {
            $updateData['resolved_at'] = Carbon::now();
        }

        if ($request->status === 'closed' && !$ticket->closed_at) {
            $updateData['closed_at'] = Carbon::now();
        }

        $ticket->update($updateData);

        return redirect()->route('e-ticket.show', $ticket->id)->with('success', 'Status tiket berhasil diperbarui.');
    }

    public function convertToMaintenance(Request $request, $id)
    {
        $ticket = Ticket::with('inventaris')->findOrFail($id);

        if (!$ticket->inventaris_id) {
            return back()->with('error', 'Tiket ini tidak memiliki aset/inventaris terkait yang dapat diservis.');
        }

        if ($ticket->maintenance_id) {
            return back()->with('error', 'Tiket ini sudah pernah dikonversi ke Service & Maintenance.');
        }

        // Generate Maintenance Code
        $today = Carbon::now()->format('Ymd');
        $prefix = "SERV-{$today}-";
        $lastMaintenance = Maintenance::where('kode_service', 'like', "{$prefix}%")->latest('id')->first();

        if ($lastMaintenance) {
            $lastNum = (int) substr($lastMaintenance->kode_service, -4);
            $kodeService = $prefix . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $kodeService = $prefix . '0001';
        }

        $maintenance = Maintenance::create([
            'kode_service' => $kodeService,
            'inventaris_id' => $ticket->inventaris_id,
            'tanggal' => Carbon::now()->toDateString(),
            'jenis' => 'Service',
            'kategori' => 'Hardware',
            'status' => 'Pengajuan',
            'keluhan' => "Dikonversi dari Tiket Helpdesk #{$ticket->nomor_tiket}: {$ticket->judul}. {$ticket->deskripsi}",
            'created_by' => Auth::id(),
            'asal' => 'Manual',
        ]);

        $ticket->update([
            'maintenance_id' => $maintenance->id,
            'status' => 'in_progress',
        ]);

        return redirect()->route('e-ticket.show', $ticket->id)->with('success', "Berhasil mengkonversi ke Service & Maintenance (#{$kodeService}).");
    }
}
