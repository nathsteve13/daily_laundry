@extends('layouts.admin.app')

@section('title', 'Form Pemesanan')

@section('content')
    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card p-5 shadow-sm border-0">
                    <h3 class="text-center mb-4 fw-bold">Form Pemesanan</h3>

                    @if (session('success'))
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

                        <div class="mb-4">
                            <label for="delivery_type" class="form-label">Jenis Pengantaran</label>
                            <select name="delivery_type" id="delivery_type" class="form-select" required>
                                <option value="kirim">Kirim</option>
                                <option value="ambil-kirim">Ambil - Kirim</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Layanan</label>
                            <div id="layanan-container">
                                <div class="layanan-item border p-3 rounded mb-3">
                                    <div class="mb-2">
                                        <label class="form-label">Jenis Layanan</label>
                                        <select name="details[0][service_type_id]" class="form-select" required>
                                            <option value="">-- Pilih Layanan --</option>
                                            @foreach ($serviceTypes as $type)
                                                <option value="{{ $type->id }}">
                                                    {{ $type->name }} - {{ number_format($type->price, 0) }} /
                                                    {{ $type->unit }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Estimasi Berat / Kuantitas</label>
                                        <input type="number" name="details[0][estimated_value]" class="form-control"
                                            step="0.01" required>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="add-layanan" class="btn btn-outline-secondary mb-3">Tambah
                                Layanan</button>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill">Kirim Pesanan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        let index = 1;
        document.getElementById('add-layanan').onclick = function() {
            const container = document.getElementById('layanan-container');
            const original = container.querySelector('.layanan-item');
            const clone = original.cloneNode(true);

            clone.querySelectorAll('select, input').forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/\[\d+\]/, `[${index}]`);
                input.setAttribute('name', newName);
                input.removeAttribute('style'); // fix critical error: select not focusable
                input.value = '';
            });

            container.appendChild(clone);
            index++;
        };
    </script>
@endpush
