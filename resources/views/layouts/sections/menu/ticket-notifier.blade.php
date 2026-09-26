{{-- SCRIPT NOTIFIKASI REAL-TIME E-TICKET IT --}}
<style>
    /* Animasi denyut halus saat ada tiket baru */
    @keyframes pulse-badge {
        0% { transform: scale(1); }
        50% { transform: scale(1.25); }
        100% { transform: scale(1); }
    }
    .badge-pulse {
        animation: pulse-badge 1.5s infinite;
    }
</style>

<script>
(function() {
    'use strict';

    const CHECK_URL = "{{ route('e-ticket.check-notifications') }}";
    const POLL_INTERVAL = 15000; // 15 detik
    const ORIGINAL_TITLE = document.title;

    let lastSeenId = parseInt(sessionStorage.getItem('it_ticket_last_seen_id') || '0', 10);
    let titleBlinkTimer = null;
    let isWindowFocused = document.hasFocus();

    window.addEventListener('focus', function() {
        isWindowFocused = true;
        stopTabTitleBlink();
    });

    window.addEventListener('blur', function() {
        isWindowFocused = false;
    });

    /**
     * Dual-tone synthesizer alert sound via Web Audio API
     * (Zero external MP3 dependency, 100% reliable across browsers)
     */
    function playTicketChime() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;

            const ctx = new AudioContext();
            if (ctx.state === 'suspended') {
                ctx.resume();
            }

            const now = ctx.currentTime;

            // Nada 1: D5 (587.33 Hz)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(587.33, now);
            gain1.gain.setValueAtTime(0.25, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.35);

            // Nada 2: A5 (880 Hz) - Nada notifikasi khas profesional
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880.00, now + 0.12);
            gain2.gain.setValueAtTime(0.28, now + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.12);
            osc2.stop(now + 0.6);
        } catch (e) {
            // Audio blocked by browser policy until first user interaction
        }
    }

    /**
     * Kedipan judul tab browser saat tiket masuk
     */
    function startTabTitleBlink(ticket) {
        if (isWindowFocused) return;
        stopTabTitleBlink();

        let toggle = false;
        titleBlinkTimer = setInterval(function() {
            document.title = toggle 
                ? `🔔 Tiket Baru #${ticket.nomor_tiket}!` 
                : ORIGINAL_TITLE;
            toggle = !toggle;
        }, 1200);
    }

    function stopTabTitleBlink() {
        if (titleBlinkTimer) {
            clearInterval(titleBlinkTimer);
            titleBlinkTimer = null;
        }
        document.title = ORIGINAL_TITLE;
    }

    /**
     * Browser Desktop Notification (Native OS Notification)
     */
    function triggerDesktopNotification(ticket) {
        if (!("Notification" in window)) return;

        if (Notification.permission === "granted") {
            try {
                const notif = new Notification(`Tiket IT Masuk: #${ticket.nomor_tiket}`, {
                    body: `${ticket.judul}\nPelapor: ${ticket.nama_pelapor}`,
                    icon: "{{ asset('assets/img/logo_aset.png') }}",
                    tag: 'ticket-' + ticket.id,
                    requireInteraction: false
                });

                notif.onclick = function() {
                    window.focus();
                    window.location.href = ticket.url;
                };
            } catch (e) {}
        } else if (Notification.permission === "default") {
            Notification.requestPermission();
        }
    }

    /**
     * SweetAlert2 Floating Toast Notification
     */
    function triggerToastNotification(ticket) {
        if (typeof Swal === 'undefined') return;

        const priorityBadges = {
            'urgent': '<span class="badge bg-danger">URGENT</span>',
            'high': '<span class="badge bg-warning text-dark">HIGH</span>',
            'medium': '<span class="badge bg-info text-dark">MEDIUM</span>',
            'low': '<span class="badge bg-secondary">LOW</span>'
        };

        const badgeHtml = priorityBadges[ticket.prioritas] || '<span class="badge bg-primary">BARU</span>';

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: `
                <div class="d-flex align-items-center justify-content-between w-100">
                    <span class="fw-bold fs-6 text-dark"><i class="bx bx-bell bx-tada me-1 text-primary"></i> Tiket IT Baru Masuk!</span>
                    ${badgeHtml}
                </div>
            `,
            html: `
                <div class="text-start mt-2">
                    <div class="fw-bold text-primary mb-1">#${ticket.nomor_tiket} - ${escapeHtml(ticket.judul)}</div>
                    <div class="small text-muted mb-2"><i class="bx bx-user me-1"></i>Pelapor: <strong>${escapeHtml(ticket.nama_pelapor)}</strong></div>
                    <a href="${ticket.url}" class="btn btn-primary btn-sm w-100 py-1 shadow-sm">
                        <i class="bx bx-show me-1"></i> Buka & Tangani Tiket
                    </a>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            timer: 15000,
            timerProgressBar: true,
            customClass: {
                popup: 'shadow-lg border-2 border-primary'
            }
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    /**
     * Update Badge Counter di Sidebar Menu & Navbar
     */
    function updateBadges(openCount, recentTickets) {
        const count = parseInt(openCount || '0', 10);

        // 1. Sidebar Group Badge (Helpdesk E-Ticket)
        const elGroupBadge = document.getElementById('eTicketGroupBadge');
        if (elGroupBadge) {
            elGroupBadge.textContent = count;
            if (count > 0) {
                elGroupBadge.classList.remove('d-none');
                elGroupBadge.classList.add('badge-pulse');
            } else {
                elGroupBadge.classList.add('d-none');
                elGroupBadge.classList.remove('badge-pulse');
            }
        }

        // 2. Sidebar Submenu Badge (Daftar Tiket)
        const elSubmenuBadge = document.getElementById('eTicketSubmenuBadge');
        if (elSubmenuBadge) {
            elSubmenuBadge.textContent = count;
            if (count > 0) {
                elSubmenuBadge.classList.remove('d-none');
                elSubmenuBadge.classList.add('badge-pulse');
            } else {
                elSubmenuBadge.classList.add('d-none');
                elSubmenuBadge.classList.remove('badge-pulse');
            }
        }

        // 3. Navbar Bell Badge
        const elNavBadge = document.getElementById('navTicketBadge');
        if (elNavBadge) {
            elNavBadge.textContent = count;
            if (count > 0) {
                elNavBadge.classList.remove('d-none');
                elNavBadge.classList.add('badge-pulse');
            } else {
                elNavBadge.classList.add('d-none');
                elNavBadge.classList.remove('badge-pulse');
            }
        }

        // 4. Navbar Header Count
        const elNavHeaderCount = document.getElementById('navTicketHeaderCount');
        if (elNavHeaderCount) {
            elNavHeaderCount.textContent = count + ' Open';
        }

        // 5. Render Daftar Tiket di Dropdown Navbar
        const elNavList = document.getElementById('navTicketList');
        const elNavEmpty = document.getElementById('navTicketEmpty');
        if (elNavList && Array.isArray(recentTickets)) {
            if (recentTickets.length === 0) {
                if (elNavEmpty) elNavEmpty.style.display = '';
                // Hapus item dinamis lama
                elNavList.querySelectorAll('.dynamic-ticket-item').forEach(el => el.remove());
            } else {
                if (elNavEmpty) elNavEmpty.style.display = 'none';
                elNavList.querySelectorAll('.dynamic-ticket-item').forEach(el => el.remove());

                recentTickets.forEach(function(t) {
                    const li = document.createElement('li');
                    li.className = 'list-group-item list-group-item-action py-2 px-3 border-bottom dynamic-ticket-item';
                    li.style.cursor = 'pointer';
                    li.innerHTML = `
                        <a href="${t.url}" class="text-decoration-none d-block">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold font-monospace text-primary small">#${t.nomor_tiket}</span>
                                <small class="text-muted" style="font-size: 0.72rem;">${t.time_ago || ''}</small>
                            </div>
                            <div class="text-dark small fw-semibold text-truncate mb-1" style="max-width: 280px;">${escapeHtml(t.judul)}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted" style="font-size: 0.75rem;"><i class="bx bx-user me-1"></i>${escapeHtml(t.nama_pelapor)}</small>
                                <span class="badge bg-label-warning py-0 px-1" style="font-size: 0.65rem;">OPEN</span>
                            </div>
                        </a>
                    `;
                    elNavList.appendChild(li);
                });
            }
        }
    }

    /**
     * Polling Engine Utama
     */
    function checkNotifications() {
        const url = CHECK_URL + '?last_seen_id=' + lastSeenId + '&_t=' + new Date().getTime();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            if (!data || !data.success) return;

            // Inisialisasi awal ID terbesar
            if (lastSeenId === 0 && data.latest_id > 0) {
                lastSeenId = data.latest_id;
                sessionStorage.setItem('it_ticket_last_seen_id', lastSeenId);
            }

            // Update badge dan daftar tiket terbuka
            updateBadges(data.open_count, data.recent);

            // JIKA ADA TIKET BARU MASUK!
            if (data.has_new && data.new_ticket) {
                lastSeenId = data.latest_id;
                sessionStorage.setItem('it_ticket_last_seen_id', lastSeenId);

                // 1. Suara notifikasi
                playTicketChime();

                // 2. Toast Popup SweetAlert2
                triggerToastNotification(data.new_ticket);

                // 3. Desktop Notification
                triggerDesktopNotification(data.new_ticket);

                // 4. Tab title alert
                startTabTitleBlink(data.new_ticket);

                // 5. Jika sedang membuka halaman daftar tiket (index), tampilkan banner info
                const indexContainer = document.getElementById('ticketListContainer');
                if (indexContainer && !document.getElementById('newTicketLiveAlert')) {
                    const alertDiv = document.createElement('div');
                    alertDiv.id = 'newTicketLiveAlert';
                    alertDiv.className = 'alert alert-info alert-dismissible d-flex align-items-center mb-3 shadow-sm';
                    alertDiv.innerHTML = `
                        <i class="bx bx-bell bx-tada fs-4 me-2"></i>
                        <div class="flex-grow-1">
                            <strong>Tiket baru telah masuk (#${data.new_ticket.nomor_tiket})!</strong> 
                            <a href="javascript:void(0);" onclick="window.location.reload();" class="alert-link ms-2 text-decoration-underline">Klik disini untuk memuat ulang daftar</a>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    indexContainer.prepend(alertDiv);
                }
            } else if (data.latest_id > lastSeenId) {
                lastSeenId = data.latest_id;
                sessionStorage.setItem('it_ticket_last_seen_id', lastSeenId);
            }
        })
        .catch(err => {
            // Abaikan kesalahan koneksi sementara
        });
    }

    // Jalankan segera setelah DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            checkNotifications();
            setInterval(checkNotifications, POLL_INTERVAL);
        });
    } else {
        checkNotifications();
        setInterval(checkNotifications, POLL_INTERVAL);
    }

    // Minta izin desktop notification saat user klik tombol lonceng jika belum ditentukan
    document.addEventListener('click', function(e) {
        const bellBtn = e.target.closest('#navTicketNotificationItem');
        if (bellBtn && "Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }
    });

})();
</script>
