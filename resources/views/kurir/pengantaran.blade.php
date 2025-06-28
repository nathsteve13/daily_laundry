@extends('layouts.app')

@section('title', 'Daftar Pengantaran')

@section('content')

@if (session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        {{ session('error') }}
    </div>
@endif

<div class="p-6 space-y-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-semibold text-gray-800">🚚 Daftar Pengantaran</h1>
        <a href="{{ route('kurir.pengantaran.create') }}" class="btn btn-dark notion-btn">+ Tambah Pengantaran</a>
    </div>

    <div class="bg-white notion-box overflow-auto">
        <table class="table align-middle mb-0 table-hover text-nowrap w-full">
            <thead class="bg-light">
                <tr class="text-muted text-uppercase small">
                    <th>No. Delivery</th>
                    <th>No. Transaction</th>
                    <th>Kurir</th>
                    <th>Tanggal Diantar</th>
                    <th>Tanggal Terkirim</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $d)
                <tr>
                    <td>{{ $d->no_delivery }}</td>
                    <td>{{ $d->no_transaction }}</td>
                    <td>{{ $d->kurir->username ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($d->tanggal_diantar)->format('d/m/Y H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($d->tanggal_terkirim)->format('d/m/Y H:i') }}</td>

                    <td class="text-end d-flex gap-2 justify-end">
                        <a href="{{ route('kurir.pengantaran.edit', $d->no_delivery) }}" class="btn btn-outline-dark btn-sm notion-btn">Edit</a>
                        <form action="{{ route('kurir.pengantaran.destroy', $d->no_delivery) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-dark btn-sm notion-btn" onclick="return confirm('Hapus pengantaran ini?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada data pengantaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
