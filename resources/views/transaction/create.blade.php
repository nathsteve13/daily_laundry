@extends('layouts.app')

@section('content')
    {{-- Alerts untuk validasi dan session error --}}
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
        {{-- Header + Button Back --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-semibold text-gray-800">+ Create Transaction</h1>
            <a href="{{ route('transactions.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Back</a>
        </div>

        {{-- Wrapper utama --}}
        <div class="bg-white notion-box p-6 shadow rounded-lg border border-gray-200">
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf

                {{-- users_id --}}
                <input type="hidden" name="users_id" value="{{ auth()->id() }}">

                {{-- Row 1: Customers + Tombol Add Customer --}}
                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">Customers</label>
                    <div class="flex gap-2">
                        <select name="customers_id[]" id="customers_id_select"
                            class="form-control flex-1 p-2 border border-gray-300 rounded" multiple required>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}"
                                    {{ collect(old('customers_id'))->contains($c->id) ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#createCustomerModal"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            + Add Customer
                        </button>
                    </div>
                    @error('customers_id')
                        <div class="text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                    @error('customers_id.*')
                        <div class="text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Row 2: Subtotal, Discount, Total --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    {{-- Subtotal --}}
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Subtotal</label>
                        <input type="number" step="0.01" id="subtotal" name="subtotal"
                            class="w-full p-2 border border-gray-300 rounded bg-gray-100" value="{{ old('subtotal', 0) }}"
                            readonly>
                    </div>

                    {{-- Discount --}}
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Discount</label>
                        <input type="number" step="0.01" id="discount" name="discount"
                            class="w-full p-2 border border-gray-300 rounded" value="{{ old('discount', 0) }}">
                        @error('discount')
                            <div class="text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Total --}}
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Total</label>
                        <input type="number" step="0.01" id="total" name="total"
                            class="w-full p-2 border border-gray-300 rounded bg-gray-100" value="{{ old('total', 0) }}"
                            readonly>
                    </div>
                </div>

                <hr class="my-6">

                {{-- Details Header --}}
                <h5 class="text-xl font-semibold text-gray-800 mb-4">Details</h5>

                {{-- Container untuk baris detail --}}
                <div id="details" class="space-y-4">
                    {{-- Baris detail pertama (index 0) --}}
                    <div class="detail-row grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        {{-- Service Type --}}
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Service Type</label>
                            <select name="details[0][service_type_id]"
                                class="stype-select w-full p-2 border border-gray-300 rounded">
                                @foreach ($services as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Value per unit --}}
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Value per unit</label>
                            <input type="number" step="0.01" name="details[0][value_per_unit]"
                                class="vpu-input w-full p-2 border border-gray-300 rounded"
                                value="{{ old('details.0.value_per_unit', 0) }}">
                        </div>

                        {{-- Line Total --}}
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Line Total</label>
                            <input type="number" step="0.01"
                                class="line-total w-full p-2 border border-gray-300 rounded bg-gray-100" value="0"
                                readonly>
                        </div>

                        {{-- Tombol Hapus Baris --}}
                        <div class="text-right">
                            <button type="button"
                                class="remove-detail px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                ×
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Tombol Add Detail --}}
                <div class="mb-6">
                    <button type="button" id="add-detail"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        + Add Detail
                    </button>
                </div>

                <hr class="my-6">

                {{-- Status --}}
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Status</label>
                    <select name="status[status]" class="w-1/2 p-2 border border-gray-300 rounded">
                        @foreach (['pending', 'proccessed', 'ready', 'done'] as $st)
                            <option value="{{ $st }}" {{ old('status.status') == $st ? 'selected' : '' }}>
                                {{ ucfirst($st) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status.status')
                        <div class="text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end gap-4">
                    <a href="{{ route('transactions.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Create Customer --}}
    <div class="modal fade z-50" id="createCustomerModal" tabindex="-1" aria-labelledby="createCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow rounded-3 border-0">
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf
                    <div class="modal-header border-0">
                        <h5 class="modal-title font-semibold text-gray-800" id="createCustomerModalLabel">+ Add New
                            Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="customerName" class="form-label">Name</label>
                            <input type="text" id="customerName" name="name" class="form-control"
                                placeholder="Customer Name" required>
                            @error('name')
                                <div class="text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Address --}}
                        <div class="mb-3">
                            <label for="customerAddress" class="form-label">Address</label>
                            <input type="text" id="customerAddress" name="address" class="form-control"
                                placeholder="Address" required>
                            @error('address')
                                <div class="text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Phone --}}
                        <div class="mb-3">
                            <label for="customerPhone" class="form-label">Phone Number</label>
                            <input type="text" id="customerPhone" name="phone_number" class="form-control"
                                placeholder="Phone Number" required>
                            @error('phone_number')
                                <div class="text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script untuk perhitungan dan interaksi --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1) Mapping service_type_id → price
            const servicePrices = {
                @foreach ($services as $s)
                    {{ $s->id }}: {{ $s->price }},
                @endforeach
            };

            // Fungsi hitung ulang subtotal, total, line totals
            function recalcAll() {
                let subtotal = 0;

                document.querySelectorAll('.detail-row').forEach(row => {
                    const stSelect = row.querySelector('.stype-select');
                    const vpuInput = row.querySelector('.vpu-input');
                    const lineTotalIn = row.querySelector('.line-total');

                    const stId = parseInt(stSelect.value) || 0;
                    const vpu = parseFloat(vpuInput.value) || 0;
                    const pricePerU = servicePrices[stId] || 0;

                    const lineTotal = vpu * pricePerU;
                    lineTotalIn.value = lineTotal.toFixed(2);

                    subtotal += lineTotal;
                });

                // Update subtotal
                document.getElementById('subtotal').value = subtotal.toFixed(2);

                // Update total (subtotal – discount)
                const discount = parseFloat(document.getElementById('discount').value) || 0;
                const total = subtotal - discount;
                document.getElementById('total').value = total.toFixed(2);
            }

            // Attach event listener ke satu .detail-row
            function attachRowListeners(row) {
                row.querySelector('.stype-select').addEventListener('change', recalcAll);
                row.querySelector('.vpu-input').addEventListener('input', recalcAll);
                row.querySelector('.remove-detail').addEventListener('click', e => {
                    if (document.querySelectorAll('.detail-row').length > 1) {
                        e.currentTarget.closest('.detail-row').remove();
                        recalcAll();
                    }
                });
            }

            // Set listener pada baris pertama (index 0)
            attachRowListeners(document.querySelector('.detail-row'));

            // Ketika discount berubah → recalcAll
            document.getElementById('discount').addEventListener('input', recalcAll);

            // Add Detail
            let detailIndex = 1;
            document.getElementById('add-detail').addEventListener('click', () => {
                const container = document.getElementById('details');
                const firstRow = document.querySelector('.detail-row');
                const cloneRow = firstRow.cloneNode(true);

                // Reset tiap input/select & perbarui name index
                cloneRow.querySelectorAll('select, input').forEach(el => {
                    el.name = el.name.replace(/\d+/, detailIndex);
                    if (el.classList.contains('vpu-input')) el.value = 0;
                    if (el.classList.contains('line-total')) el.value = 0;
                    if (el.tagName.toLowerCase() === 'select') el.selectedIndex = 0;
                    if (el.type === 'checkbox') el.checked = false;
                });

                container.appendChild(cloneRow);
                attachRowListeners(cloneRow);

                detailIndex++;
                recalcAll();
            });

            // Hitung sekali pada load
            recalcAll();
        });
    </script>
@endsection
