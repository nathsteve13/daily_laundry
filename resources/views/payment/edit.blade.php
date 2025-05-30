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

    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">✏️ Edit Payment</h1>
            <a href="{{ route('payments.index') }}" class="btn btn-light notion-btn">Back</a>
        </div>

        <div class="bg-white notion-box overflow-hidden p-6 shadow rounded-3 border-0">
            <form action="{{ route('payments.update', $payment->no_payment) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">No. Payment</label>
                    <input type="text" class="form-control mb-3" value="{{ $payment->no_payment }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">No. Transaction</label>
                    <select name="no_transaction" class="form-control mb-3" required>
                        @foreach($transactions as $t)
                            <option value="{{ $t->no_transaction }}" {{ old('no_transaction', $payment->no_transaction)==$t->no_transaction?'selected':'' }}>
                                {{ $t->no_transaction }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Total</label>
                    <input type="number" step="0.01" name="total" class="form-control mb-3" value="{{ old('total', $payment->total) }}" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        @foreach(['pending','failed','success'] as $st)
                            <option value="{{ $st }}" {{ old('status', $payment->status)==$st?'selected':'' }}>
                                {{ ucfirst($st) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary notion-btn">Cancel</a>
                    <button type="submit" class="btn btn-dark notion-btn">Update</button>
                </div>
            </form>
        </div>
    </div>

@endsection
