@extends('layouts.app')

@section('content')

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="p-6 space-y-6">
        <div class="bg-light p-4 rounded">
            <form method="GET" class="d-flex gap-2 mb-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nama customer..."
                    value="{{ request('search') }}">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach (['pending', 'pickup', 'proccessed', 'ready', 'delivered', 'done'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
                <select name="sort" class="form-select">
                    <option value="">Urutkan</option>
                    <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>Terlama</option>
                    <option value="desc" {{ request('sort') === 'desc' ? 'selected' : '' }}>Terbaru</option>
                </select>
                <button class="btn btn-primary">Terapkan</button>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Reset</a>
            </form>
        </div>
        <div class="flex items-center justify-between mb-4">

            <h1 class="text-3xl font-semibold text-gray-800">🔄 Transaction List</h1>
            <a href="{{ route('transactions.create') }}" class="btn btn-dark notion-btn">+ New Transaction</a>

        </div>


        <div class="bg-white notion-box overflow-hidden">
            <table class="table align-middle mb-0 table-hover text-nowrap w-full">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase small text-center">
                        <th>No. Transaction</th>
                        <th>Customer</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>User ID</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $t)
                        <tr class="text-center">
                            <td>{{ $t->no_transaction }}</td>
                            <td>
                                @if ($t->customers && $t->customers->isNotEmpty())
                                    {{ $t->customers->first()->name }}
                                @else
                                    <span class="text-muted">Unknown Customer</span>
                                @endif
                            </td>
                            <td>{{ $t->subtotal }}</td>
                            <td>{{ $t->discount }}</td>
                            <td>{{ $t->total }}</td>
                            <td>{{ $t->users_id }}</td>
                            <td>
                                @php
                                    $latestStatus = $t->transactionStatus->sortByDesc('created_at')->first();
                                @endphp
                                <span id="status-{{ $t->no_transaction }}"
                                    class="badge bg-{{ match ($latestStatus?->status) {
                                        'pending' => 'secondary',
                                        'pickup' => 'warning',
                                        'proccessed' => 'info',
                                        'ready' => 'primary',
                                        'delivered' => 'dark',
                                        'done' => 'success',
                                        default => 'light',
                                    } }}">
                                    <a href="#" onclick="openStatusModal('{{ $t->no_transaction }}')"
                                        class="text-white text-decoration-none">
                                        {{ ucfirst($latestStatus?->status ?? 'unknown') }}
                                    </a>
                                </span>
                            </td>
                            <td class="text-end d-flex gap-2 justify-center">
                                <a href="{{ route('transactions.edit', $t->no_transaction) }}"
                                    class="btn btn-outline-dark btn-sm notion-btn">Edit</a>
                                <form action="{{ route('transactions.destroy', $t->no_transaction) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-dark btn-sm notion-btn"
                                        onclick="return confirm('Delete this transaction?')">Delete</button>
                                </form>
                                <a href="#" class="btn btn-outline-primary btn-sm"
                                    onclick="openAssignModal('{{ $t->no_transaction }}')">Assign Kurir</a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

            <!-- Modal Assign Kurir -->
            <div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="assignForm" method="POST">
                        @csrf
                        <input type="hidden" name="no_transaction" id="assign_no_transaction">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Assign Kurir</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body space-y-3">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Tugas</label>
                                    <select name="jenis" id="jenis" class="form-select" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="ambil">Pengambilan</option>
                                        <option value="terima">Pengantaran</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Pilih Kurir</label>
                                    <select name="kurir_id" class="form-select" required>
                                        @foreach ($kurirs as $k)
                                            <option value="{{ $k->id }}">{{ $k->username }}
                                                ({{ $k->pickups_count }} pickups, {{ $k->deliveries_count }} deliveries)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3" id="tanggal_ambil_group" style="display: none;">
                                    <label class="form-label">Tanggal Pengambilan</label>
                                    <input type="datetime-local" name="tanggal_pengambilan" class="form-control" min="{{ date('Y-m-d\TH:i') }}">
                                </div>
                                <div class="mb-3" id="tanggal_kirim_group" style="display: none;">
                                    <label class="form-label">Tanggal Pengiriman</label>
                                    <input type="datetime-local" name="tanggal_pengiriman" class="form-control" min="{{ date('Y-m-d\TH:i') }}">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-success" type="submit">✔ Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Ubah Status -->
            <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="statusForm">
                        @csrf
                        <input type="hidden" name="no_transaction" id="modal_no_transaction">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ubah Status Transaksi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body space-y-4">
                                <div class="mb-3">
                                    <label class="form-label">Status Baru</label>
                                    <select id="status_select" name="status" class="form-select" required>
                                        <option value="pending">Pending</option>
                                        <option value="pickup">Pickup</option>
                                        <option value="proccessed">Proccessed</option>
                                        <option value="ready">Ready</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="done">Done</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-success" type="submit">✔ Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
@push('styles')
    <style>
        #statusModal select option:disabled {
            color: #9aa0a6;
        }
    </style>
@endpush
@push('scripts')
    <script>
        const STATUS_ORDER = ['pending', 'pickup', 'proccessed', 'ready', 'delivered', 'done'];

        // panggil ini seperti openStatusModal(noTrx) atau openStatusModal(noTrx, currentStatus)
        function openStatusModal(no_transaction, currentStatus = null) {
            document.getElementById('modal_no_transaction').value = no_transaction;

            // dapatkan status saat ini
            let cur = currentStatus;
            if (!cur) {
                // fallback ambil dari badge di tabel
                cur = document.querySelector('#status-' + no_transaction + ' a')?.textContent?.trim().toLowerCase() ||
                    'pending';
            }

            const select = document.querySelector('#statusModal #status_select');
            const curIdx = STATUS_ORDER.indexOf(cur);
            const lastIdx = STATUS_ORDER.length - 1;

            // reset
            select.disabled = false;
            Array.from(select.options).forEach(o => o.disabled = false);

            if (curIdx >= 0) {
                const allowedIdx = (curIdx === lastIdx) ? [curIdx] : [curIdx, curIdx + 1];

                // kunci opsi
                Array.from(select.options).forEach(o => {
                    const idx = STATUS_ORDER.indexOf(o.value);
                    o.disabled = !allowedIdx.includes(idx);
                });

                // set default ke next kalau ada, jika tidak tetap
                const nextIdx = Math.min(curIdx + 1, lastIdx);
                select.value = STATUS_ORDER[nextIdx];
                if (select.options[select.selectedIndex]?.disabled) {
                    select.value = STATUS_ORDER[curIdx];
                }

                // jika sudah status terakhir, kunci seluruh select
                if (curIdx === lastIdx) {
                    select.disabled = true;
                }
            } else {
                // fallback: hanya pending
                Array.from(select.options).forEach(o => o.disabled = (o.value !== 'pending'));
                select.value = 'pending';
            }

            new bootstrap.Modal(document.getElementById('statusModal')).show();
        }

        // guard tambahan. Cegah user paksa pilih opsi yang disabled via keyboard atau scroll.
        (function bindStatusGuard() {
            const sel = document.querySelector('#statusModal #status_select');
            if (!sel) return;
            sel.addEventListener('change', function() {
                if (this.options[this.selectedIndex]?.disabled) {
                    // kembalikan ke opsi pertama yang tidak disabled
                    const firstEnabled = Array.from(this.options).find(o => !o.disabled);
                    if (firstEnabled) this.value = firstEnabled.value;
                }
            }, {
                passive: true
            });
            sel.addEventListener('wheel', e => e.preventDefault(), {
                passive: false
            });
        })();
    </script>
@endpush

@push('scripts')
    <script>
        function openAssignModal(no_transaction) {
            document.getElementById('assign_no_transaction').value = no_transaction;
            document.getElementById('jenis').value = '';
            document.querySelector('select[name="kurir_id"]').selectedIndex = 0;
            document.querySelector('input[name="tanggal_pengambilan"]').value = '';
            document.querySelector('input[name="tanggal_pengiriman"]').value = '';
            document.getElementById('tanggal_ambil_group').style.display = 'none';
            document.getElementById('tanggal_kirim_group').style.display = 'none';

            new bootstrap.Modal(document.getElementById('assignModal')).show();
        }

        document.getElementById('jenis').addEventListener('change', function() {
            const val = this.value;
            document.getElementById('tanggal_ambil_group').style.display = (val === 'ambil') ? 'block' : 'none';
            document.getElementById('tanggal_kirim_group').style.display = (val === 'terima') ? 'block' : 'none';
        });

        document.getElementById('assignForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);

            fetch("{{ route('transactions.assignKurir') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                    },
                    body: data
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        bootstrap.Modal.getInstance(document.getElementById('assignModal')).hide();
                        alert('Kurir berhasil di-assign!');
                        location.reload(); // opsional
                    } else {
                        alert(res.message || 'Gagal assign kurir.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan.');
                });
        });

        document.getElementById('statusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);
            const no_transaction = data.get('no_transaction');
            const status = data.get('status');

            fetch("{{ route('transactions.status.update') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: data
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        const badge = document.getElementById('status-' + no_transaction);
                        badge.className = 'badge bg-' + res.badge;
                        badge.innerHTML =
                            `<a href="#" onclick="openStatusModal('${no_transaction}')" class="text-white text-decoration-none">${res.status}</a>`;
                        bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
                    } else {
                        alert('Gagal mengupdate status.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan.');
                });
        });
    </script>
@endpush
