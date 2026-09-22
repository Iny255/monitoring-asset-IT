<tr>
    <td class="text-center text-muted fw-semibold">
        {{ $index }}
    </td>
    <td>
        <span class="badge bg-label-primary font-monospace px-2 py-1">
            📅 {{ ucfirst($jadwal->hari) }}
        </span>
    </td>
    <td>
        <div class="d-flex align-items-center">
            <div class="avatar avatar-sm bg-label-info me-2">
                <i class="bx bx-door-open fs-5"></i>
            </div>
            <div>
                <span class="fw-bold text-dark d-block">
                    {{ $jadwal->lokasi->nama_lokasi ?? '-' }}
                </span>
                @if ($jadwal->catatan)
                    <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                        <i class="bx bx-note me-1"></i>{{ $jadwal->catatan }}
                    </small>
                @endif
            </div>
        </div>
    </td>
    @if (auth()->user()->role === 'super_admin')
        <td>
            <x-company-badge :perusahaan="$jadwal->perusahaan" />
        </td>
    @endif
    <td>
        @if ($jadwal->assignedTo)
            <div class="d-flex align-items-center">
                <div class="avatar avatar-xs bg-label-success me-2">
                    <span class="avatar-initial rounded-circle">{{ substr($jadwal->assignedTo->name, 0, 1) }}</span>
                </div>
                <span class="small fw-semibold">{{ $jadwal->assignedTo->name }}</span>
            </div>
        @else
            <span class="badge bg-label-secondary">Belum Ditugaskan</span>
        @endif
    </td>
    <td>
        <small class="text-muted font-monospace">
            <i class="bx bx-time-five me-1"></i>
            {{ $jadwal->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '08:00' }} - {{ $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '17:00' }}
        </small>
    </td>
    <td class="text-center">
        <form action="{{ route('checklist.jadwal.toggle', $jadwal->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm border-0 bg-transparent p-0" title="Klik untuk ubah status">
                @if ($jadwal->is_active)
                    <span class="badge bg-label-success">
                        <i class="bx bx-check-circle me-1"></i> Aktif
                    </span>
                @else
                    <span class="badge bg-label-secondary">
                        <i class="bx bx-pause-circle me-1"></i> Nonaktif
                    </span>
                @endif
            </button>
        </form>
    </td>
    <td class="text-center">
        <div class="d-flex justify-content-center gap-1">
            <a href="{{ route('checklist.jadwal.edit', $jadwal->id) }}" 
               class="btn btn-sm btn-icon btn-outline-secondary" 
               title="Edit Jadwal Rutin">
                <i class="bx bx-edit"></i>
            </a>
            <form action="{{ route('checklist.jadwal.destroy', $jadwal->id) }}" 
                  method="POST" 
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal rutin {{ $jadwal->lokasi->nama_lokasi ?? '' }} di hari {{ ucfirst($jadwal->hari) }}?\n\n(Catatan: Riwayat pelaksanaan checklist yang sudah ada tidak akan ikut terhapus)');"
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus Jadwal Rutin">
                    <i class="bx bx-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
