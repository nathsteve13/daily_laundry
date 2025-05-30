@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Payment</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('payments.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>No. Payment</label>
            <input type="text" name="no_payment" class="form-control" value="{{ old('no_payment') }}">
            @error('no_payment') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label>No. Transaction</label>
            <select name="no_transaction" class="form-control">
                @foreach($transactions as $t)
                    <option value="{{ $t->no_transaction }}" {{ old('no_transaction')==$t->no_transaction?'selected':'' }}>
                        {{ $t->no_transaction }}
                    </option>
                @endforeach
            </select>
            @error('no_transaction') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label>Total</label>
            <input type="number" step="0.01" name="total" class="form-control" value="{{ old('total') }}">
            @error('total') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                @foreach(['pending','failed','success'] as $st)
                    <option value="{{ $st }}" {{ old('status')==$st?'selected':'' }}>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            @error('status') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <button class="btn btn-success">Save</button>
        <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
