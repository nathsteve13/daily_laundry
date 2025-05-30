@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Payments</h1>
    <a href="{{ route('payments.create') }}" class="btn btn-primary mb-3">Add Payment</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No. Payment</th><th>No. Transaction</th><th>Total</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($payments as $p)
            <tr>
                <td>{{ $p->no_payment }}</td>
                <td>{{ $p->no_transaction }}</td>
                <td>{{ $p->total }}</td>
                <td>{{ ucfirst($p->status) }}</td>
                <td>
                    <a href="{{ route('payments.edit', $p->no_payment) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('payments.destroy', $p->no_payment) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this payment?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
