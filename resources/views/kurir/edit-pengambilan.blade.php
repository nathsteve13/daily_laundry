@extends('layouts.app')

@section('title', 'Edit Data Pengambilan')

@section('content')
    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-semibold text-gray-700">✏ Edit Pengambilan</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kurir.pengambilan.update', $pengambilan->no_pickup) }}" method="POST"
            enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="no_transaction">No. Transaksi</label>
                <input type="text" class="form-control bg-light" value="{{ $pengambilan->no_transaction }}" readonly>
                <input type="hidden" name="no_transaction" value="{{ $pengambilan->no_transaction }}">
            </div>

            <div class="form-group">
                <label for="kurir">Kurir</label>
                <input type="text" class="form-control bg-light" value="{{ $pengambilan->kurir->username ?? '-' }}"
                    readonly>
                <input type="hidden" name="kurir_id" value="{{ $pengambilan->kurir_id }}">
            </div>


            <div class="form-group">
                <label for="tanggal_diambil">Tanggal Pengambilan</label>
                <input type="datetime-local" name="tanggal_pengambilan" class="form-control"
                    value="{{ date('Y-m-d\TH:i', strtotime($pengambilan->tanggal_pengambilan)) }}" required>
            </div>

            <div class="form-group">
                <label for="tanggal_sampai">Tanggal Diambil</label>
                <input type="datetime-local" name="tanggal_diambil" class="form-control"
                    value="{{ date('Y-m-d\TH:i', strtotime($pengambilan->tanggal_diambil)) }}" required>
            </div>

            <div class="form-group">
                <label for="bukti_ambil">Bukti Pengambilan (opsional)</label>
                <input type="file" name="bukti_ambil" class="form-control">
                @if ($pengambilan->bukti_pengambilan)
                    <p class="mt-2 text-sm text-gray-500">File saat ini: <a
                            href="{{ asset($pengambilan->bukti_pengambilan) }}" target="_blank">Lihat</a></p>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('kurir.pengambilan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
