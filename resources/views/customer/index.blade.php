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
            <form id="customerClientFilter" class="d-flex gap-2" onsubmit="return false;">
                <input id="customerSearch" type="text" class="form-control"
                    placeholder="Cari nama, alamat, atau telepon…">
                <button type="button" class="btn btn-primary" onclick="applyCustomerFilter()">Cari</button>
                <button type="button" class="btn btn-secondary" onclick="resetCustomerFilter()">Reset</button>
            </form>
        </div>

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">🧍 Customer List</h1>
            <a href="{{ route('customers.create') }}" class="btn btn-dark notion-btn">+ New Customer</a>
        </div>

        <div class="bg-white notion-box overflow-hidden">
            <table id="customersTable" class="table align-middle mb-0 table-hover text-nowrap w-full">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase small text-center">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr class="text-center" data-name="{{ strtolower($customer->name) }}"
                            data-address="{{ strtolower($customer->address) }}"
                            data-phone="{{ preg_replace('/\D+/', '', $customer->phone_number) }}">

                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->address }}</td>
                            <td>{{ $customer->phone_number }}</td>
                            <td class="text-end d-flex gap-2 justify-center text-center">
                                <a href="{{ route('customers.edit', $customer->id) }}"
                                    class="btn btn-outline-dark btn-sm notion-btn">Edit</a>
                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-dark btn-sm notion-btn"
                                        onclick="return confirm('Delete this customer?')">Delete</button>
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
            const tbody = document.querySelector('#customersTable tbody');
            if (!tbody) return;

            const rows = Array.from(tbody.querySelectorAll('tr'));
            const input = document.getElementById('customerSearch');

            window.applyCustomerFilter = function() {
                const q = (input?.value || '').trim().toLowerCase();
                const qDigits = q.replace(/\D+/g, ''); // untuk cocokkan nomor telepon

                rows.forEach(tr => {
                    const name = tr.dataset.name || '';
                    const address = tr.dataset.address || '';
                    const phone = tr.dataset.phone || '';

                    const match = !q ||
                        name.includes(q) ||
                        address.includes(q) ||
                        (qDigits && phone.includes(qDigits));

                    tr.style.display = match ? '' : 'none';
                });
            };

            window.resetCustomerFilter = function() {
                if (input) input.value = '';
                rows.forEach(tr => tr.style.display = '');
            };

            input?.addEventListener('input', applyCustomerFilter);
        });
    </script>
@endpush
