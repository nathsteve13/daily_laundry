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
                                <span class="badge bg-{{ $order->status === 'selesai' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($order->delivery_type) }}</td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>
                                <button data-order='@json($order->append('details'))' onclick="showTerimaModal(this)"
                                    class="btn btn-sm btn-dark">Terima</button>

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

    <!-- Modal Terima Pesanan -->
    <div id="terimaModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('order.terima') }}" method="POST">
                    @csrf
                    <input type="hidden" name="no_order" id="modal_no_order">
                    <div class="modal-header">
                        <h5 class="modal-title">Terima Pesanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body space-y-4">
                        <div>
                            <label class="form-label">Data Pesanan</label>
                            <div id="orderData" class="border p-3 rounded bg-light text-sm">
                                <!-- diisi via JS -->
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Pilih Customer</label>
                            <div class="d-flex gap-2 mb-2">
                                <input type="text" name="customer_search" class="form-control"
                                    placeholder="Cari customer...">
                                <a href="{{ route('customers.create') }}" class="btn btn-outline-primary">+ Customer
                                    Baru</a>
                            </div>
                            <select name="customers_id" class="form-select" required>
                                <option value="">-- Pilih Customer --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->phone_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Detail Transaksi</label>
                            <div id="layanan-list" class="row g-3">
                                <!-- detail layanan akan diisi via JS -->
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <label class="form-label">Subtotal</label>
                                    <input type="number" name="subtotal_display" id="subtotal_display"
                                        class="form-control bg-light" readonly>

                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Diskon (Rp)</label>
                                    <input type="number" name="discount" id="discount" class="form-control" step="0.01"
                                        value="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Total</label>
                                    <input type="number" name="total_display" id="total_display"
                                        class="form-control bg-light" readonly>
                                </div>
                            </div>

                            <!-- nilai asli -->
                            <input type="hidden" name="subtotal" id="subtotal">
                            <input type="hidden" name="total" id="total">

                        </div>
                    </div>
                    <input type="hidden" name="pickup" id="pickup">

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">✔ Terima</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function showTerimaModal(button) {
            const order = JSON.parse(button.getAttribute('data-order'));
            document.getElementById('modal_no_order').value = order.no_order;

            const layananContainer = document.getElementById('layanan-list');
            layananContainer.innerHTML = '';
            document.getElementById('pickup').value = order.delivery_type.toLowerCase().includes('ambil') ? 1 : 0;

            let totalEstimasi = 0;
            const layananHTML = (order.details || []).map((d, i) => {
                return `
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
                `;
            }).join('');


            layananContainer.innerHTML = layananHTML;

            const dataDiv = document.getElementById('orderData');
            dataDiv.innerHTML = `
                <div><strong>No Order:</strong> ${order.no_order}</div>
                <div><strong>Nama:</strong> ${order.name}</div>
                <div><strong>Telepon:</strong> ${order.phone_number}</div>
                <div><strong>Alamat:</strong> ${order.address}</div>
                <div><strong>Estimasi:</strong> Rp ${totalEstimasi.toLocaleString()}</div>
                <div><strong>Pengantaran:</strong> ${order.delivery_type}</div>
            `;

            document.getElementById('discount').addEventListener('input', calculateTotalFromModal);
            calculateTotalFromModal();



            new bootstrap.Modal(document.getElementById('terimaModal')).show();
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
