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
        <div class="bg-light p-4 rounded mb-3">
            <form id="serviceClientFilter" class="d-flex gap-2" onsubmit="return false;">
                <input id="svcSearch" type="text" class="form-control" placeholder="Cari nama, unit, atau harga…">
                <select id="svcSort" class="form-select" style="max-width:220px">
                    <option value="">Urutkan</option>
                    <option value="name_asc">Nama • A-Z</option>
                    <option value="name_desc">Nama • Z-A</option>
                    <option value="price_asc">Harga • Murah ke Mahal</option>
                    <option value="price_desc">Harga • Mahal ke Murah</option>
                    <option value="duration_asc">Durasi • Terpendek</option>
                    <option value="duration_desc">Durasi • Terpanjang</option>
                </select>
                <button type="button" class="btn btn-primary" onclick="applyServiceFilter()">Cari</button>
                <button type="button" class="btn btn-secondary" onclick="resetServiceFilter()">Reset</button>
            </form>
        </div>

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">🛎️ Service Types</h1>
            <a href="{{ route('service-types.create') }}" class="btn btn-dark notion-btn">+ New Service</a>
        </div>

        <div class="bg-white notion-box overflow-hidden">
            <table id="servicesTable" class="table align-middle mb-0 table-hover text-nowrap w-full">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase small">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Unit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                        <tr data-name="{{ strtolower($service->name) }}" data-unit="{{ strtolower($service->unit) }}"
                            data-price="{{ (int) preg_replace('/\D+/', '', $service->price) }}"
                            data-duration="{{ (int) $service->duration }}">
                            <td>{{ $service->id }}</td>
                            <td>{{ $service->name }}</td>
                            <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                            <td>{{ $service->duration }}</td>
                            <td>{{ $service->unit }}</td>
                            <td class="text-end d-flex gap-2 justify-end">
                                <a href="{{ route('service-types.edit', $service->id) }}"
                                    class="btn btn-outline-dark btn-sm notion-btn">Edit</a>
                                <form action="{{ route('service-types.destroy', $service->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-dark btn-sm notion-btn"
                                        onclick="return confirm('Delete this service?')">Delete</button>
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
            const tbody = document.querySelector('#servicesTable tbody');
            if (!tbody) return;

            const rows = Array.from(tbody.querySelectorAll('tr'));
            const els = {
                q: document.getElementById('svcSearch'),
                so: document.getElementById('svcSort'),
            };

            function sortVisible(visible, key, dir) {
                const attr = key === 'name' ? 'data-name' :
                    key === 'price' ? 'data-price' :
                    key === 'duration' ? 'data-duration' :
                    'data-name';
                visible.sort((a, b) => {
                    let va = a.getAttribute(attr) || '';
                    let vb = b.getAttribute(attr) || '';
                    if (attr !== 'data-name') {
                        va = parseInt(va || '0', 10);
                        vb = parseInt(vb || '0', 10);
                    }
                    if (attr === 'data-name') {
                        return dir === 'asc' ? va.localeCompare(vb) : vb.localeCompare(va);
                    }
                    return dir === 'asc' ? va - vb : vb - va;
                });
                visible.forEach(tr => tbody.appendChild(tr));
            }

            window.applyServiceFilter = function() {
                const q = (els.q?.value || '').trim().toLowerCase();
                const qDigits = q.replace(/\D+/g, ''); // cocokkan angka harga bila user ketik angka
                const so = els.so?.value || '';

                rows.forEach(tr => {
                    const name = tr.dataset.name || '';
                    const unit = tr.dataset.unit || '';
                    const price = tr.dataset.price || '';
                    const match = !q ||
                        name.includes(q) ||
                        unit.includes(q) ||
                        (qDigits && price.includes(qDigits));
                    tr.style.display = match ? '' : 'none';
                });

                if (so) {
                    const [key, dir] = so.split('_'); // name_asc, price_desc, duration_asc
                    const visible = rows.filter(tr => tr.style.display !== 'none');
                    sortVisible(visible, key, dir);
                }
            };

            window.resetServiceFilter = function() {
                if (els.q) els.q.value = '';
                if (els.so) els.so.value = '';
                rows.forEach(tr => tr.style.display = '');
            };

            ['input', 'change'].forEach(evt => {
                els.q?.addEventListener(evt, applyServiceFilter);
                els.so?.addEventListener(evt, applyServiceFilter);
            });
        });
    </script>
@endpush
