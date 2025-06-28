@extends('layouts.app')

@section('title', 'Tambah Data Pengambilan')

@section('content')
<div class="p-6 space-y-6">
    <h1 class="text-2xl font-semibold text-gray-700">+ Tambah Pengambilan</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kurir.pengambilan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div class="form-group">
            <label for="no_transaction">No. Transaksi</label>
            <select name="no_transaction" id="no_transaction" class="form-control" required>
                <option value="">-- Pilih Transaksi --</option>
                @foreach($transactions as $trx)
                    <option value="{{ $trx }}">{{ $trx }}</option>
                @endforeach

            </select>
        </div>

        <div class="form-group">
            <label for="kurir">Kurir</label>
            <select name="kurir_id" id="kurir" class="form-control" required>
                <option value="">-- Pilih Kurir --</option>
                @foreach($kurirs as $k)
                    <option value="{{ $k->id }}">{{ $k->username }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="tanggal_pengambilan">Tanggal Pengambilan</label>
            <input type="datetime-local" name="tanggal_pengambilan" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="tanggal_diambil">Tanggal Diambil</label>
            <input type="datetime-local" name="tanggal_diambil" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="bukti_ambil">Bukti Pengambilan (opsional)</label>
            <input type="file" name="bukti_ambil" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('kurir.pengambilan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
