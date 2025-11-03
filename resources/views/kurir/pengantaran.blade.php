@extends('layouts.app')

@section('title', 'Daftar Pengantaran')

@section('content')

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between mb-3">
            <h1 class="text-3xl font-semibold text-gray-800">🚚 Daftar Pengantaran</h1>
            <a href="{{ route('kurir.pengantaran.create') }}" class="btn btn-dark notion-btn">+ Tambah Pengantaran</a>
        </div>

        {{-- Filter Frontend --}}
        <div class="bg-light p-4 rounded mb-3">
            <form id="deliveryClientFilter" class="d-flex gap-2" onsubmit="return false;">
                <input type="text" id="deliverySearch" class="form-control"
                    placeholder="🔍 Cari no. delivery, transaksi, kurir, kecamatan, kelurahan..." style="max-width:400px">
                <select id="deliveryStatus" class="form-select" style="max-width:220px">
                    <option value="">Semua Status</option>
                    <option value="belum">Belum terkirim</option>
                    <option value="selesai">Selesai</option>
                </select>
                <select id="deliverySort" class="form-select" style="max-width:260px">
                    <option value="">Urutkan Waktu</option>
                    <option value="diantar_asc">Tanggal Diantar • Terlama</option>
                    <option value="diantar_desc">Tanggal Diantar • Terbaru</option>
                    <option value="terkirim_asc">Tanggal Terkirim • Terlama</option>
                    <option value="terkirim_desc">Tanggal Terkirim • Terbaru</option>
                </select>
                <button type="button" class="btn btn-secondary" onclick="resetDeliveryFilters()">Reset</button>
            </form>
        </div>

        <div class="bg-white notion-box overflow-auto">
            <table id="deliveriesTable" class="table align-middle mb-0 table-hover text-nowrap w-full">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase small text-center">
                        <th>No. Delivery</th>
                        <th>No. Transaction</th>
                        <th>Kurir</th>
                        <th>Kecamatan</th>
                        <th>Kelurahan</th>
                        <th>Tanggal Diantar</th>
                        <th>Tanggal Terkirim</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $d)
                        @php
                            $diantarTs = $d->tanggal_diantar
                                ? \Carbon\Carbon::parse($d->tanggal_diantar)->timestamp
                                : 0;
                            $terkirimTs = $d->tanggal_terkirim
                                ? \Carbon\Carbon::parse($d->tanggal_terkirim)->timestamp
                                : 0;
                            $isDone = !empty($d->tanggal_terkirim);
                            $statusUi = $isDone ? 'Selesai' : 'Belum terkirim';
                            $statusVal = $isDone ? 'selesai' : 'belum';
                        @endphp
                        <tr class="text-center delivery-row" data-status="{{ $statusVal }}"
                            data-diantar="{{ $diantarTs }}" data-terkirim="{{ $terkirimTs }}"
                            data-search="{{ strtolower($d->no_delivery . ' ' . $d->no_transaction . ' ' . ($d->kurir->username ?? '') . ' ' . ($d->transaction->kecamatan->name ?? '') . ' ' . ($d->transaction->kelurahan->name ?? '')) }}">
                            <td>{{ $d->no_delivery }}</td>
                            <td>{{ $d->no_transaction }}</td>
                            <td>{{ $d->kurir->username ?? '-' }}</td>
                            <td>{{ $d->transaction->kecamatan->name ?? '-' }}</td>
                            <td>{{ $d->transaction->kelurahan->name ?? '-' }}</td>
                            <td>
                                {{ $d->tanggal_diantar ? \Carbon\Carbon::parse($d->tanggal_diantar)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td>
                                @if ($d->tanggal_terkirim)
                                    {{ \Carbon\Carbon::parse($d->tanggal_terkirim)->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Belum terkirim</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $isDone ? 'bg-success' : 'bg-warning' }}">
                                    {{ $statusUi }}
                                </span>
                            </td>
                            <td class="text-end d-flex gap-2 justify-center text-center">
                                <a href="{{ route('kurir.pengantaran.edit', $d->no_delivery) }}"
                                    class="btn btn-outline-dark btn-sm notion-btn">Finish</a>
                                <form action="{{ route('kurir.pengantaran.destroy', $d->no_delivery) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-dark btn-sm notion-btn"
                                        onclick="return confirm('Hapus pengantaran ini?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.querySelector('#deliveriesTable tbody');
            if (!tbody) return;

            const rows = Array.from(tbody.querySelectorAll('tr'));
            const els = {
                search: document.getElementById('deliverySearch'),
                st: document.getElementById('deliveryStatus'),
                so: document.getElementById('deliverySort'),
            };

            function sortVisible(visible, key, dir) {
                const attr = key === 'diantar' ? 'data-diantar' : 'data-terkirim';
                visible.sort((a, b) => {
                    const va = parseInt(a.getAttribute(attr) || '0', 10);
                    const vb = parseInt(b.getAttribute(attr) || '0', 10);
                    return dir === 'asc' ? va - vb : vb - va;
                });
                visible.forEach(tr => tbody.appendChild(tr));
            }

            window.applyDeliveryFilters = function() {
                const searchText = (els.search?.value || '').trim().toLowerCase();
                const st = (els.st?.value || '').trim().toLowerCase(); // '', 'belum', 'selesai'
                const so = els.so?.value || ''; // '', 'diantar_asc', 'terkirim_desc', ...

                rows.forEach(tr => {
                    const status = (tr.dataset.status || '').toLowerCase();
                    const searchData = (tr.dataset.search || '').toLowerCase();

                    const matchSearch = !searchText || searchData.includes(searchText);
                    const matchStatus = !st || status === st;

                    tr.style.display = (matchSearch && matchStatus) ? '' : 'none';
                });

                if (so) {
                    const [key, dir] = so.split('_'); // diantar_asc, terkirim_desc
                    const visible = rows.filter(tr => tr.style.display !== 'none');
                    sortVisible(visible, key, dir);
                }
            };

            window.resetDeliveryFilters = function() {
                if (els.search) els.search.value = '';
                if (els.st) els.st.value = '';
                if (els.so) els.so.value = '';
                rows.forEach(tr => tr.style.display = '');
            };

            ['input', 'change'].forEach(evt => {
                els.search?.addEventListener(evt, applyDeliveryFilters);
                els.st?.addEventListener(evt, applyDeliveryFilters);
                els.so?.addEventListener(evt, applyDeliveryFilters);
            });
        });
    </script>
@endpush
