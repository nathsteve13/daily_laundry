@extends('layouts.app')

@section('title', 'Data Pengambilan')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">📦 Data Pengambilan</h1>
            <a href="{{ route('kurir.pengambilan.create') }}" class="btn btn-dark notion-btn">+ Tambah Pengambilan</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="bg-white notion-box overflow-hidden">
            <table class="table align-middle mb-0 table-hover text-nowrap w-full">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase small">
                        <th>No. Pickup</th>
                        <th>No. Transaksi</th>
                        <th>Kurir</th>
                        <th>Tanggal Diambil</th>
                        <th>Tanggal Sampai</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            <td>{{ $row->no_pickup }}</td>
                            <td>{{ $row->transaction->no_transaction ?? '-' }}</td>
                            <td>{{ $row->kurir->name ?? '-' }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($row->tanggal_diambil)) }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($row->tanggal_sampai)) }}</td>
                            <td>
                                @if($row->bukti_ambil)
                                    <a href="{{ asset('storage/' . $row->bukti_ambil) }}" target="_blank">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="d-flex gap-2 justify-end">
                                <a href="{{ route('kurir.pengambilan.edit', $row->no_pickup) }}" class="btn btn-outline-dark btn-sm notion-btn">Edit</a>
                                <form action="{{ route('kurir.pengambilan.destroy', $row->no_pickup) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-dark btn-sm notion-btn" onclick="return confirm('Hapus data ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
