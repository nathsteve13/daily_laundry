@extends('layouts.app')

@section('title', 'Tambah Pengantaran')

@section('content')
<div class="p-6 space-y-6">
    <h1 class="text-2xl font-semibold">📤 Tambah Pengantaran</h1>

    <form action="{{ route('kurir.pengantaran.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        
        <div>
            <label class="form-label">No Transaksi</label>
            <select name="no_transaction" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach($transactions as $trx)
                    <option value="{{ $trx }}">{{ $trx }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Kurir</label>
            <select name="kurir_id" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach($kurirs as $k)
                    <option value="{{ $k->id }}">{{ $k->username }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Tanggal Diantar</label>
            <input type="datetime-local" name="tanggal_diantar" class="form-control" required>
        </div>
        <div>
            <label class="form-label">Tanggal Terkirim</label>
            <input type="datetime-local" name="tanggal_terkirim" class="form-control" required>
        </div>
        <div>
            <label class="form-label">Bukti Terima</label>
            <input type="file" name="bukti_terima" class="form-control">
        </div>
        <div>
            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('kurir.pengantaran.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
