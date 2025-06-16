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

    <form action="{{ route('kurir.pengambilan.update', $pengambilan->no_pickup) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="no_transaction">No. Transaksi</label>
            <select name="transactions_id" id="no_transaction" class="form-control" required>
                @foreach($transactions as $t)
                    <option value="{{ $t->id }}" {{ $pengambilan->transactions_id == $t->id ? 'selected' : '' }}>{{ $t->no_transaction }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="kurir">Kurir</label>
            <select name="kurirs_id" id="kurir" class="form-control" required>
                @foreach($kurirs as $k)
                    <option value="{{ $k->id }}" {{ $pengambilan->kurirs_id == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="tanggal_diambil">Tanggal Diambil</label>
            <input type="datetime-local" name="tanggal_diambil" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($pengambilan->tanggal_diambil)) }}" required>
        </div>

        <div class="form-group">
            <label for="tanggal_sampai">Tanggal Sampai</label>
            <input type="datetime-local" name="tanggal_sampai" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($pengambilan->tanggal_sampai)) }}" required>
        </div>

        <div class="form-group">
            <label for="bukti_ambil">Bukti Pengambilan (opsional)</label>
            <input type="file" name="bukti_ambil" class="form-control">
            @if ($pengambilan->bukti_ambil)
                <p class="mt-2 text-sm text-gray-500">File saat ini: <a href="{{ asset('storage/' . $pengambilan->bukti_ambil) }}" target="_blank">Lihat</a></p>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('kurir.pengambilan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
