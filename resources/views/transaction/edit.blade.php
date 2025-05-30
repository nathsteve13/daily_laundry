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

    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">✏️ Edit Transaction</h1>
            <a href="{{ route('transactions.index') }}" class="btn btn-light notion-btn">Back</a>
        </div>

        <div class="bg-white notion-box overflow-hidden p-6 shadow rounded-3 border-0">
            <form action="{{ route('transactions.update', $transaction->no_transaction) }}" method="POST">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label">No. Transaction</label>
                    <input type="text" class="form-control mb-3" value="{{ $transaction->no_transaction }}" disabled>
                </div>
                <input type="hidden" name="users_id" value="{{ auth()->id() }}">

                <div class="mb-3">
                    <label class="form-label">Customers</label>
                    <select name="customers_id[]" class="form-control mb-3" multiple>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ in_array($c->id, old('customers_id', $transaction->customers->pluck('id')->toArray())) ? 'selected':'' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Subtotal</label>
                        <input type="number" step="0.01" name="subtotal" class="form-control" value="{{ old('subtotal', $transaction->subtotal) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Discount</label>
                        <input type="number" step="0.01" name="discount" class="form-control" value="{{ old('discount', $transaction->discount) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total</label>
                        <input type="number" step="0.01" name="total" class="form-control" value="{{ old('total', $transaction->total) }}" required>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Details</h5>
                <div id="details">
                    @foreach(old('details', $transaction->details->toArray()) as $i => $d)
                    <div class="detail-row row mb-2">
                        <div class="col">
                            <label class="form-label">Service Type</label>
                            <select name="details[{{ $i }}][service_type_id]" class="form-control">
                                @foreach($services as $s)
                                    <option value="{{ $s->id }}" {{ ($d['service_type_id'] ?? '') == $s->id ? 'selected':'' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Pickup</label><br>
                            <input type="checkbox" name="details[{{ $i }}][pickup]" value="1" {{ !empty($d['pickup']) ? 'checked':'' }}>
                        </div>
                        <div class="col">
                            <label class="form-label">Value per unit</label>
                            <input type="number" step="0.01" name="details[{{ $i }}][value_per_unit]" class="form-control" value="{{ $d['value_per_unit'] }}">
                        </div>
                        <div class="col-auto align-self-end">
                            <button type="button" class="btn btn-danger remove-detail">×</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" id="add-detail" class="btn btn-outline-secondary mb-4 notion-btn">Add Detail</button>

                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <select name="status[status]" class="form-control">
                        @foreach(['pending','proccessed','ready','done'] as $st)
                            <option value="{{ $st }}" {{ old('status.status', $transaction->status->status) == $st ? 'selected' : '' }}>
                                {{ ucfirst($st) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary notion-btn">Cancel</a>
                    <button type="submit" class="btn btn-dark notion-btn">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        let detailIndex = {{ $transaction->details->count() }};

        document.getElementById('add-detail').addEventListener('click', () => {
            const template = document.querySelector('.detail-row').cloneNode(true);
            template.querySelectorAll('select, input').forEach(el => {
                el.name = el.name.replace(/\d+/, detailIndex);
                if (el.type !== 'checkbox') el.value = '';
                else el.checked = false;
            });
            document.getElementById('details').append(template);
            detailIndex++;
        });

        document.getElementById('details').addEventListener('click', e => {
            if (e.target.classList.contains('remove-detail') && document.querySelectorAll('.detail-row').length > 1) {
                e.target.closest('.detail-row').remove();
            }
        });
    });
    </script>

@endsection
