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
            <h1 class="text-3xl font-semibold text-gray-800">💰 Payment List</h1>
            <a href="{{ route('payments.create') }}" class="btn btn-dark notion-btn">+ New Payment</a>
        </div>

        <div class="bg-white notion-box overflow-hidden">
            <table class="table align-middle mb-0 table-hover text-nowrap w-full">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase small">
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
                        <td class="text-end d-flex gap-2 justify-end">
                            <a href="{{ route('payments.edit', $p->no_payment) }}" class="btn btn-outline-dark btn-sm notion-btn">Edit</a>
                            <form action="{{ route('payments.destroy', $p->no_payment) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-dark btn-sm notion-btn" onclick="return confirm('Delete this payment?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
