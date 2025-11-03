@extends('layouts.app')

@section('title', 'Data Pengambilan')

@section('content')
    <div class="p-6 space-y-6">
        <div class="bg-light p-4 rounded mb-3">
            <form id="pickupClientFilter" class="d-flex gap-2" onsubmit="return false;">
                <input type="text" id="pickupSearch" class="form-control"
                    placeholder="🔍 Cari no. pickup, transaksi, kurir, kecamatan, kelurahan..." style="max-width:400px">
                <select id="pickupStatus" class="form-select" style="max-width:220px">
                    <option value="">Semua Status</option>
                    <option value="belum">Belum diantar</option>
                    <option value="selesai">Selesai</option>
                </select>
                <select id="pickupSort" class="form-select" style="max-width:220px">
                    <option value="">Urutkan Waktu</option>
                    <option value="ambil_asc">Tanggal Diambil • Terlama</option>
                    <option value="ambil_desc">Tanggal Diambil • Terbaru</option>
                    <option value="sampai_asc">Tanggal Sampai • Terlama</option>
                    <option value="sampai_desc">Tanggal Sampai • Terbaru</option>
                </select>
                <button type="button" class="btn btn-secondary" onclick="resetPickupFilters()">Reset</button>
            </form>
        </div>
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">📦 Data Pengambilan</h1>
            <a href="{{ route('kurir.pengambilan.create') }}" class="btn btn-dark notion-btn">+ Tambah Pengambilan</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="bg-white notion-box overflow-hidden">



            <table id="pickupTable" class="table align-middle mb-0 table-hover text-nowrap w-full">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase small text-center">
                        <th>No. Pickup</th>
                        <th>No. Transaksi</th>
                        <th>Kurir</th>
                        <th>Kecamatan</th>
                        <th>Kelurahan</th>
                        <th>Tanggal Diambil</th>
                        <th>Tanggal Sampai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($data as $row)
                        @php
                            $isDone = !empty($row->tanggal_diambil);
                            $statusUi = $isDone ? 'Selesai' : 'Belum diantar';
                            $statusVal = $isDone ? 'selesai' : 'belum';
                            $ambilTs = $row->tanggal_pengambilan ? strtotime($row->tanggal_pengambilan) : 0;
                            $sampaiTs = $row->tanggal_diambil ? strtotime($row->tanggal_diambil) : 0;
                        @endphp
                        <tr class="text-center pickup-row" data-status="{{ $statusVal }}"
                            data-ambil="{{ $ambilTs }}" data-sampai="{{ $sampaiTs }}"
                            data-search="{{ strtolower($row->no_pickup . ' ' . ($row->transaction->no_transaction ?? '') . ' ' . ($row->kurir->username ?? '') . ' ' . ($row->transaction->kecamatan->name ?? '') . ' ' . ($row->transaction->kelurahan->name ?? '')) }}">
                            <td>{{ $row->no_pickup }}</td>
                            <td>{{ $row->transaction->no_transaction ?? '-' }}</td>
                            <td>{{ $row->kurir->username ?? '-' }}</td>
                            <td>{{ $row->transaction->kecamatan->name ?? '-' }}</td>
                            <td>{{ $row->transaction->kelurahan->name ?? '-' }}</td>
                            <td>{{ $row->tanggal_pengambilan ? date('d/m/Y H:i', strtotime($row->tanggal_pengambilan)) : '-' }}
                            </td>
                            <td>
                                @if ($row->tanggal_diambil)
                                    {{ date('d/m/Y H:i', strtotime($row->tanggal_diambil)) }}
                                @else
                                    <span class="text-muted">Belum diantar</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $isDone ? 'bg-success' : 'bg-warning' }}">{{ $statusUi }}</span>
                            </td>
                            <td>
                                <a href="{{ route('kurir.pengambilan.edit', $row->no_pickup) }}"
                                    class="btn btn-outline-dark btn-sm notion-btn">Finish</a>
                                <form action="{{ route('kurir.pengambilan.destroy', $row->no_pickup) }}" method="POST"
                                    style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-dark btn-sm notion-btn"
                                        onclick="return confirm('Hapus data ini?')">Hapus</button>
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
            const tbody = document.querySelector('#pickupTable tbody');
            if (!tbody) return;

            const rows = Array.from(tbody.querySelectorAll('tr'));
            const els = {
                search: document.getElementById('pickupSearch'),
                st: document.getElementById('pickupStatus'),
                so: document.getElementById('pickupSort'),
            };

            function sortVisible(visible, key, dir) {
                const keyAttr = key === 'ambil' ? 'data-ambil' : 'data-sampai';
                visible.sort((a, b) => {
                    const va = parseInt(a.getAttribute(keyAttr) || '0', 10);
                    const vb = parseInt(b.getAttribute(keyAttr) || '0', 10);
                    return dir === 'asc' ? va - vb : vb - va;
                });
                visible.forEach(tr => tbody.appendChild(tr));
            }

            window.applyPickupFilters = function() {
                const searchText = (els.search?.value || '').trim().toLowerCase();
                const st = (els.st?.value || '').trim().toLowerCase();
                const so = els.so?.value || '';

                rows.forEach(tr => {
                    const status = (tr.dataset.status || '').toLowerCase();
                    const searchData = (tr.dataset.search || '').toLowerCase();

                    const matchSearch = !searchText || searchData.includes(searchText);
                    const matchStatus = !st || status === st;

                    tr.style.display = (matchSearch && matchStatus) ? '' : 'none';
                });

                if (so) {
                    const [key, dir] = so.split('_'); // ambil_asc, sampai_desc
                    const visible = rows.filter(tr => tr.style.display !== 'none');
                    sortVisible(visible, key, dir);
                }
            };

            window.resetPickupFilters = function() {
                if (els.search) els.search.value = '';
                if (els.st) els.st.value = '';
                if (els.so) els.so.value = '';
                rows.forEach(tr => tr.style.display = '');
            };

            ['input', 'change'].forEach(evt => {
                els.search?.addEventListener(evt, applyPickupFilters);
                els.st?.addEventListener(evt, applyPickupFilters);
                els.so?.addEventListener(evt, applyPickupFilters);
            });
        });
    </script>
@endpush
