@extends('layouts.admin.app')

@section('title', 'Form Pemesanan')

@section('content')
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="card p-5 shadow-sm border-0">
                <h3 class="text-center mb-4 fw-bold">Form Pemesanan</h3>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @elseif(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('pesan.store') }}" method="POST">
                    @csrf


                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea name="address" id="address" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="estimated_value" class="form-label">Estimasi Berat / Kuantitas </label>
                        <input type="number" name="estimated_value" id="estimated_value" class="form-control" step="0.01" required>
                    </div>

                    <div class="mb-4">
                        <label for="delivery_type" class="form-label">Jenis Pengantaran</label>
                        <select name="delivery_type" id="delivery_type" class="form-select" required>
                            <option value="kirim">Kirim</option>
                            <option value="ambil-kirim">Ambil - Kirim</option>
                        </select>
                    </div>


                    <div class="mb-3">
                        <label for="service_type_id" class="form-label">Jenis Layanan</label>
                        <select name="service_type_id" id="service_type_id" class="form-select" required>
                            <option value="">-- Pilih Layanan --</option>
                            @foreach ($serviceTypes as $type)
                                <option value="{{ $type->id }}">
                                    {{ $type->name }} - {{ number_format($type->price, 0) }} / {{ $type->unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill mt-4">Kirim Pesanan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
