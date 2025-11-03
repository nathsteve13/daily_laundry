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
        @php
            $statusOptions = $payments->pluck('status')->filter()->unique()->values();
        @endphp

        <div class="bg-light p-4 rounded mb-3">
            <form id="paymentClientFilter" class="d-flex gap-2" onsubmit="return false;">
                <input id="paySearch" type="text" class="form-control" placeholder="Cari no payment atau no transaksi…">
                <select id="payStatus" class="form-select" style="max-width:220px">
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $s)
                        <option value="{{ strtolower($s) }}">{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <select id="paySort" class="form-select" style="max-width:260px">
                    <option value="">Urutkan</option>
                    <option value="total_asc">Total • Kecil ke Besar</option>
                    <option value="total_desc">Total • Besar ke Kecil</option>
                    <option value="date_asc">Tanggal • Terlama</option>
                    <option value="date_desc">Tanggal • Terbaru</option>
                </select>
                <button type="button" class="btn btn-secondary" onclick="resetPaymentFilters()">Reset</button>
            </form>
        </div>

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">💰 Payment List</h1>
            <a href="{{ route('payments.create') }}" class="btn btn-dark notion-btn">+ New Payment</a>
        </div>

        <div class="bg-white notion-box overflow-hidden">
            <table id="paymentsTable" class="table align-middle mb-0 table-hover text-nowrap w-full">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase small text-center">
                        <th>No. Payment</th>
                        <th>No. Transaction</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $p)
                        <tr data-payment="{{ strtolower($p->no_payment) }}"
                            data-transaction="{{ strtolower($p->no_transaction) }}"
                            data-status="{{ strtolower($p->status) }}" data-total="{{ $p->total }}"
                            data-created="{{ strtotime($p->created_at) }}" class="text-center">
                            <td>{{ $p->no_payment }}</td>
                            <td>{{ $p->no_transaction }}</td>
                            <td>Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $p->status === 'success' ? 'success' : ($p->status === 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td class="text-end d-flex gap-2 justify-center">
                                <a href="{{ route('payments.edit', $p->no_payment) }}"
                                    class="btn btn-outline-dark btn-sm notion-btn">Edit</a>
                                <form action="{{ route('payments.destroy', $p->no_payment) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-dark btn-sm notion-btn"
                                        onclick="return confirm('Delete this payment?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.querySelector('#paymentsTable tbody');
            if (!tbody) return;

            const rows = Array.from(tbody.querySelectorAll('tr'));
            const els = {
                q: document.getElementById('paySearch'),
                st: document.getElementById('payStatus'),
                so: document.getElementById('paySort'),
            };

            function sortVisible(visible, key, dir) {
                const attr = key === 'total' ? 'data-total' : 'data-created';
                visible.sort((a, b) => {
                    const va = parseInt(a.getAttribute(attr) || '0', 10);
                    const vb = parseInt(b.getAttribute(attr) || '0', 10);
                    return dir === 'asc' ? va - vb : vb - va;
                });
                visible.forEach(tr => tbody.appendChild(tr));
            }

            window.applyPaymentFilters = function() {
                const q = (els.q?.value || '').trim().toLowerCase();
                const st = (els.st?.value || '').trim().toLowerCase();
                const so = els.so?.value || '';

                rows.forEach(tr => {
                    const pay = tr.dataset.payment || '';
                    const trx = tr.dataset.transaction || '';
                    const status = tr.dataset.status || '';
                    const matchText = !q || pay.includes(q) || trx.includes(q);
                    const matchStatus = !st || status === st;
                    tr.style.display = (matchText && matchStatus) ? '' : 'none';
                });

                if (so) {
                    const [key, dir] = so.split('_'); // total_asc, date_desc
                    const visible = rows.filter(tr => tr.style.display !== 'none');
                    sortVisible(visible, key === 'date' ? 'created' : key, dir);
                }
            };

            window.resetPaymentFilters = function() {
                if (els.q) els.q.value = '';
                if (els.st) els.st.value = '';
                if (els.so) els.so.value = '';
                rows.forEach(tr => tr.style.display = '');
            };

            ['input', 'change'].forEach(evt => {
                els.q?.addEventListener(evt, applyPaymentFilters);
                els.st?.addEventListener(evt, applyPaymentFilters);
                els.so?.addEventListener(evt, applyPaymentFilters);
            });
        });
    </script>
@endpush
