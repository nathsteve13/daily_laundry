@extends('layouts.app')

@section('title', 'Edit Pengantaran')

@section('content')
    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-semibold">✏️ Edit Pengantaran</h1>

        <form action="{{ route('kurir.pengantaran.update', $delivery->no_delivery) }}" method="POST"
            enctype="multipart/form-data">
            @csrf @method('PUT')
            <div>
                <label class="form-label">No Transaksi</label>
                <select name="no_transaction" class="form-select" required>
                    @foreach ($transactions as $trx)
                        <option value="{{ $trx }}" {{ $delivery->no_transaction == $trx ? 'selected' : '' }}>
                            {{ $trx }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Kurir</label>
                <select name="kurir_id" class="form-select" required>
                    @foreach ($kurirs as $k)
                        <option value="{{ $k->id }}" {{ $delivery->kurir_id == $k->id ? 'selected' : '' }}>
                            {{ $k->username }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Tanggal Diantar</label>
                <input type="datetime-local" name="tanggal_diantar" class="form-control"
                    value="{{ \Carbon\Carbon::parse($delivery->tanggal_diantar)->format('Y-m-d\TH:i') }}">
            </div>
            <div>
                <label class="form-label">Tanggal Terkirim</label>
                <input type="datetime-local" name="tanggal_terkirim" class="form-control"
                    value="{{ \Carbon\Carbon::parse($delivery->tanggal_terkirim)->format('Y-m-d\TH:i') }}">
            </div>
            <div>
                <label class="form-label">Bukti Terima (upload untuk mengganti)</label>
                <input type="file" name="bukti_terima" class="form-control">
                @if ($delivery->bukti_terima)
                    <p class="mt-2"><a href="{{ asset($delivery->bukti_terima) }}" target="_blank">Lihat File
                            Sebelumnya</a></p>
                @endif
            </div>
            <div>
                <button class="btn btn-primary">Update</button>
                <a href="{{ route('kurir.pengantaran.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@endsection
