@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Transactions</h1>
    <a href="{{ route('transactions.create') }}" class="btn btn-primary mb-3">Add Transaction</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No. Transaction</th><th>Subtotal</th><th>Discount</th><th>Total</th><th>User ID</th><th>Actions</th>
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
                    <a href="{{ route('transactions.edit', $t->no_transaction) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('transactions.destroy', $t->no_transaction) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this transaction?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
