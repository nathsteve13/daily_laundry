@extends('layouts.app')

@section('title', 'Daftar Pesanan')

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
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">📦 Daftar Pesanan</h1>
        </div>

        <div class="bg-white notion-box overflow-hidden">
            <table class="table align-middle mb-0 table-hover text-nowrap w-full">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase small">
                        <th>No. Order</th>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Layanan</th>
                        <th>Estimasi</th>
                        <th>Status</th>
                        <th>Pengantaran</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->no_order }}</td>
                            <td>{{ $order->name }}</td>
                            <td>{{ $order->phone_number }}</td>
                            <td>{{ $order->address }}</td>
                            <td>{{ $order->serviceType->name ?? '-' }}</td>
                            <td>Rp {{ number_format($order->estimated_value, 0) }}</td>
                            <td>
                                <span
                                    class="badge
                                        @if ($order->status === 'selesai') bg-success
                                        @elseif($order->status === 'ditolak') bg-danger
                                        @else bg-warning @endif">
                                    {{ ucfirst($order->status) }}
                                </span>

                            </td>
                            <td>{{ ucfirst($order->delivery_type) }}</td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button data-order='@json($order->append('details'))' onclick="showTerimaModal(this)"
                                        class="btn btn-sm btn-dark">
                                        Terima
                                    </button>

                                    <form action="{{ route('order.tolak') }}" method="POST"
                                        onsubmit="return confirm('Tolak pesanan ini?')">
                                        @csrf
                                        <input type="hidden" name="no_order" value="{{ $order->no_order }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Belum ada pesanan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('order.modal-order-terima')


@endsection
@php
    // Siapkan data customer sederhana agar @json tidak error
    $customerJson = $customers
        ->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'phone' => $c->phone_number,
            ];
        })
        ->values();
@endphp

@push('scripts')
    <script>
        // Expose data customer ke JS
        window.CUSTOMERS = @json($customerJson);

        function normalizePhone(s) {
            if (!s) return ''
            return String(s).replace(/\D+/g, '')
        }

        function buildSubstrings(phoneDigits, len) {
            const out = new Set()
            if (!phoneDigits || phoneDigits.length < len) return out
            for (let i = 0; i <= phoneDigits.length - len; i++) {
                out.add(phoneDigits.slice(i, i + len))
            }
            return out
        }

        function scoreCustomerMatch(orderPhoneDigits, customerPhoneDigits) {
            const subs5 = buildSubstrings(orderPhoneDigits, 5)
            const subs4 = buildSubstrings(orderPhoneDigits, 4)

            let score = 0
            let bestLen = 0

            subs5.forEach(sub => {
                if (customerPhoneDigits.includes(sub)) {
                    score += 10
                    bestLen = Math.max(bestLen, 5)
                }
            })

            subs4.forEach(sub => {
                if (customerPhoneDigits.includes(sub)) {
                    score += 3
                    bestLen = Math.max(bestLen, 4)
                }
            })

            return {
                score,
                bestLen
            }
        }

        function renderCustomerSuggestions(candidates) {
            const modal = document.getElementById('terimaModal');
            const wrap = modal?.querySelector('#customer-suggestions');
            if (!wrap) return;

            if (!candidates.length) {
                wrap.innerHTML = '<small class="text-muted">Tidak ada kandidat cocok</small>';
                return;
            }

            const top = candidates.slice(0, 3);
            const btns = top.map(c => `
                <button type="button" class="btn btn-sm btn-outline-secondary me-2 mb-2"
                        onclick="selectCustomer(${c.id})">
                ${c.name} • ${c.phone} • match ${c.bestLen} digits
                </button>
            `).join('');

            wrap.innerHTML = `
                <div class="mt-1">
                <small class="text-muted d-block mb-1">Saran cocok</small>
                ${btns}
                </div>
            `;
        }

        function selectCustomer(id) {
            const modal = document.getElementById('terimaModal');
            const select = modal?.querySelector('select[name="customers_id"]');
            if (!select) return;
            select.value = String(id);
            select.dispatchEvent(new Event('change'));
        }

        function autoSuggestCustomer(orderPhoneRaw) {
            const orderDigits = normalizePhone(orderPhoneRaw);
            const modal = document.getElementById('terimaModal');
            const select = modal?.querySelector('select[name="customers_id"]');

            if (!orderDigits || !Array.isArray(window.CUSTOMERS)) {
                renderCustomerSuggestions([]);
                return;
            }

            const scored = window.CUSTOMERS.map(c => {
                const cDigits = normalizePhone(c.phone);
                const {
                    score,
                    bestLen
                } = scoreCustomerMatch(orderDigits, cDigits);
                return {
                    ...c,
                    score,
                    bestLen
                };
            }).filter(x => x.score > 0);

            scored.sort((a, b) => {
                if (b.score !== a.score) return b.score - a.score;
                if (b.bestLen !== a.bestLen) return b.bestLen - a.bestLen;
                return a.name.localeCompare(b.name);
            });

            // reset select ke daftar awal (kalau sebelumnya terfilter)
            if (select && select.options.length > 0 && select.options[0].value === '') {
                // biarkan isi asal dari Blade; tidak perlu rebuild
            }

            if (scored.length) selectCustomer(scored[0].id);
            renderCustomerSuggestions(scored);
        }

        // Filter select saat user mengetik di input cari
        function customerSearchInputInit() {
            const modal = document.getElementById('terimaModal');
            const input = modal?.querySelector('input[name="customer_search"]');
            const select = modal?.querySelector('select[name="customers_id"]');
            if (!input || !select) return;

            const originalOptions = Array.from(select.options).map(o => ({
                value: o.value,
                text: o.text
            }));

            input.addEventListener('input', () => {
                const q = input.value.toLowerCase().trim();
                // reset isi ke opsi asli dari Blade agar id selalu ada
                select.innerHTML = '';
                originalOptions.forEach(o => {
                    if (o.value === '' || o.text.toLowerCase().includes(q)) {
                        const opt = document.createElement('option');
                        opt.value = o.value;
                        opt.textContent = o.text;
                        select.appendChild(opt);
                    }
                });
            });
        }
        document.addEventListener('DOMContentLoaded', customerSearchInputInit);

        // Integrasi ke alur modal Anda
        function showTerimaModal(button) {
            const order = JSON.parse(button.getAttribute('data-order'));
            document.getElementById('modal_no_order').value = order.no_order;

            const modal = document.getElementById('terimaModal');
            const layananContainer = modal.querySelector('#layanan-list');
            const pickupInput = modal.querySelector('#pickup');
            const dataDiv = modal.querySelector('#orderData');

            layananContainer.innerHTML = (order.details || []).map((d, i) => `
                <div class="col-md-6">
                <label class="form-label">Jenis Layanan</label>
                <input type="text" class="form-control" readonly value="${d.service_type.name}">
                <input type="hidden" name="details[${i}][service_type_id]" value="${d.service_type_id}" data-price="${d.service_type.price}">
                </div>
                <div class="col-md-6">
                <label class="form-label">Estimasi</label>
                <input type="text" class="form-control" readonly value="${d.estimated_value}">
                <input type="hidden" name="details[${i}][estimated_value]" value="${d.estimated_value}">
                </div>
            `).join('');

            pickupInput.value = order.delivery_type?.toLowerCase().includes('ambil') ? 1 : 0;

            dataDiv.innerHTML = `
                <div><strong>No Order:</strong> ${order.no_order}</div>
                <div><strong>Nama:</strong> ${order.name}</div>
                <div><strong>Telepon:</strong> ${order.phone_number}</div>
                <div><strong>Alamat:</strong> ${order.address}</div>
                <div><strong>Pengantaran:</strong> ${order.delivery_type}</div>
            `;

            autoSuggestCustomer(order.phone_number);

            const disc = modal.querySelector('#discount');
            disc.removeEventListener('input', calculateTotalFromModal);
            disc.addEventListener('input', calculateTotalFromModal);
            calculateTotalFromModal();

            new bootstrap.Modal(modal).show();
        }
        // Fungsi hitung total Anda tetap dipakai
        function calculateTotalFromModal() {
            const discountInput = document.getElementById('discount')
            const subtotalField = document.getElementById('subtotal')
            const subtotalDisplay = document.getElementById('subtotal_display')
            const totalField = document.getElementById('total')
            const totalDisplay = document.getElementById('total_display')

            let subtotal = 0
            const serviceInputs = document.querySelectorAll('input[name^="details"][name$="[service_type_id]"]')

            serviceInputs.forEach(input => {
                const name = input.name
                const index = name.match(/^details\[(\d+)]/)[1]
                const priceInput = document.querySelector(`input[name="details[${index}][service_type_id]"]`)
                const price = parseFloat(priceInput?.getAttribute('data-price') || 0)
                const estInput = document.querySelector(`input[name="details[${index}][estimated_value]"]`)
                const estimated = parseFloat(estInput?.value || 0)
                if (!priceInput || !estInput || isNaN(price) || isNaN(estimated)) return
                subtotal += price * estimated
            })

            const discount = parseFloat(discountInput.value) || 0
            const total = Math.max(0, subtotal - discount)

            subtotalField.value = subtotal.toFixed(2)
            subtotalDisplay.value = subtotal.toFixed(2)
            totalField.value = total.toFixed(2)
            totalDisplay.value = total.toFixed(2)
        }



        function calculateTotalFromModal() {
            const discountInput = document.getElementById('discount');
            const subtotalField = document.getElementById('subtotal');
            const subtotalDisplay = document.getElementById('subtotal_display');
            const totalField = document.getElementById('total');
            const totalDisplay = document.getElementById('total_display');

            let subtotal = 0;

            const serviceInputs = document.querySelectorAll('input[name^="details"][name$="[service_type_id]"]');

            serviceInputs.forEach(input => {
                const name = input.name; // details[0][service_type_id]
                const index = name.match(/^details\[(\d+)]/)[1];
                const priceInput = document.querySelector(`input[name="details[${index}][service_type_id]"]`);
                const price = parseFloat(priceInput?.getAttribute('data-price') || 0);
                const estInput = document.querySelector(`input[name="details[${index}][estimated_value]"]`);
                const estimated = parseFloat(estInput?.value || 0);

                if (!priceInput || !estInput || isNaN(price) || isNaN(estimated)) {
                    console.warn(`Skipping index ${index} because price or estimate invalid.`);
                    return;
                }


                subtotal += price * estimated;
            });

            const discount = parseFloat(discountInput.value) || 0;
            const total = Math.max(0, subtotal - discount);

            console.log('subtotalDisplay', subtotalDisplay);
            console.log('subtotal:', subtotal);

            subtotalField.value = subtotal.toFixed(2);
            subtotalDisplay.value = subtotal.toFixed(2);
            totalField.value = total.toFixed(2);
            totalDisplay.value = total.toFixed(2);
        }



        function calculateTotal() {
            const serviceSelect = document.querySelector('[name="service_type_id"]');
            const selectedOption = serviceSelect?.options[serviceSelect.selectedIndex];
            const quantityInput = document.querySelector('[name="value_per_unit"]');
            const discountInput = document.querySelector('[name="discount"]');

            if (!selectedOption || !quantityInput || !discountInput) return;

            const priceText = selectedOption.textContent.match(/([\d,]+(?:\.\d+)?)/);
            const price = priceText ? parseFloat(priceText[1].replace(/,/g, '')) : 0;
            const quantity = parseFloat(quantityInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;

            const total = Math.max(0, (price * quantity) - discount);

            const totalDisplay = document.getElementById('total_display');
            const totalHidden = document.getElementById('total');

            if (totalDisplay && totalHidden) {
                totalDisplay.value = !isNaN(total) ? total.toFixed(2) : 0;
                totalHidden.value = !isNaN(total) ? total : 0;
            }
        }
    </script>
@endpush
