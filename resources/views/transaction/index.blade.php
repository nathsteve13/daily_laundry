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
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">🔄 Transaction List</h1>
            <a href="{{ route('transactions.create') }}" class="btn btn-dark notion-btn">+ New Transaction</a>
        </div>

        <div class="bg-white notion-box overflow-hidden">
            <table class="table align-middle mb-0 table-hover text-nowrap w-full">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase small">
                        <th>No. Transaction</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>User ID</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                    <tr>
                        <td>{{ $t->no_transaction }}</td>
                        <td>{{ $t->subtotal }}</td>
                        <td>{{ $t->discount }}</td>
                        <td>{{ $t->total }}</td>
                        <td>{{ $t->users_id }}</td>
                        <td>
                            @php
                                $latestStatus = $t->transactionStatus->sortByDesc('created_at')->first();
                            @endphp
                            <span id="status-{{ $t->no_transaction }}" class="badge bg-{{ match($latestStatus?->status) {
                                'pending' => 'secondary',
                                'pickup' => 'warning',
                                'proccessed' => 'info',
                                'ready' => 'primary',
                                'delivered' => 'dark',
                                'done' => 'success',
                                default => 'light'
                            } }}">
                                <a href="#" onclick="openStatusModal('{{ $t->no_transaction }}')" class="text-white text-decoration-none">
                                    {{ ucfirst($latestStatus?->status ?? 'unknown') }}
                                </a>
                            </span>
                        </td>
                        <td class="text-end d-flex gap-2 justify-end">
                            <a href="{{ route('transactions.edit', $t->no_transaction) }}" class="btn btn-outline-dark btn-sm notion-btn">Edit</a>
                            <form action="{{ route('transactions.destroy', $t->no_transaction) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-dark btn-sm notion-btn" onclick="return confirm('Delete this transaction?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

            <!-- Modal Ubah Status -->
            <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="statusForm">
                        @csrf
                        <input type="hidden" name="no_transaction" id="modal_no_transaction">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ubah Status Transaksi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body space-y-4">
                                <div class="mb-3">
                                    <label class="form-label">Status Baru</label>
                                    <select name="status" class="form-select" required>
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
@push('scripts')
<script>
    function openStatusModal(no_transaction) {
        document.getElementById('modal_no_transaction').value = no_transaction;
        const modal = new bootstrap.Modal(document.getElementById('statusModal'));
        modal.show();
    }
</script>
@endpush

@push('scripts')
<script>
    function openStatusModal(no_transaction) {
        document.getElementById('modal_no_transaction').value = no_transaction;
        const modal = new bootstrap.Modal(document.getElementById('statusModal'));
        modal.show();
    }

    document.getElementById('statusForm').addEventListener('submit', function (e) {
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
                badge.innerHTML = `<a href="#" onclick="openStatusModal('${no_transaction}')" class="text-white text-decoration-none">${res.status}</a>`;
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
