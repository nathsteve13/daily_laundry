@extends('layouts.admin.app')

@section('title', 'Form Pemesanan')

@push('styles')
    <style>
        /* Custom search input style for select */
        .custom-select-search {
            width: 100%;
            padding: 8px 12px;
            margin-bottom: 8px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .custom-select-search:focus {
            outline: none;
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endpush

@section('content')
    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="text-center mb-5 fw-bold text-primary">📝 Form Pemesanan Laundry</h3>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @elseif(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('pesan.store') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="bi bi-person-fill text-primary me-2"></i>Nama Lengkap
                                </label>
                                <input type="text" name="name" id="name" class="form-control form-control-lg"
                                    placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="mb-4">
                                <label for="address" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt-fill text-primary me-2"></i>Alamat Lengkap
                                </label>
                                <textarea name="address" id="address" class="form-control form-control-lg" rows="3"
                                    placeholder="Masukkan alamat lengkap untuk pengambilan/pengantaran" required></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="kecamatan_id" class="form-label fw-semibold">
                                        <i class="bi bi-pin-map-fill text-primary me-2"></i>Kecamatan
                                    </label>
                                    <select name="kecamatan_id" id="kecamatan_id" class="form-select form-select-lg"
                                        required>
                                        <option value="">-- Pilih Kecamatan --</option>
                                        @foreach ($kecamatans as $kec)
                                            <option value="{{ $kec->id }}">{{ $kec->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="kelurahan_id" class="form-label fw-semibold">
                                        <i class="bi bi-geo-fill text-primary me-2"></i>Kelurahan
                                    </label>
                                    <select name="kelurahan_id" id="kelurahan_id" class="form-select form-select-lg"
                                        required disabled>
                                        <option value="">-- Pilih Kelurahan --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="phone_number" class="form-label fw-semibold">
                                    <i class="bi bi-telephone-fill text-primary me-2"></i>Nomor Telepon
                                </label>
                                <input type="tel" name="phone_number" id="phone_number"
                                    class="form-control form-control-lg" pattern="0[0-9]{9,14}" required
                                    placeholder="08xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                <small class="text-muted">Format: 10-15 digit, contoh: 081234567890</small>
                            </div>

                            <div class="mb-5">
                                <label for="delivery_type" class="form-label fw-semibold">
                                    <i class="bi bi-truck text-primary me-2"></i>Jenis Pengantaran
                                </label>
                                <select name="delivery_type" id="delivery_type" class="form-select form-select-lg" required>
                                    <option value="">-- Pilih Jenis Pengantaran --</option>
                                    <option value="kirim">🚚 Kirim (Delivery)</option>
                                    <option value="ambil-kirim">🔄 Ambil & Kirim (Pick-up & Delivery)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-3">
                                    <i class="bi bi-basket-fill text-primary me-2"></i>Layanan yang Dipilih
                                </label>
                                <div id="layanan-container">
                                    <div class="layanan-item bg-light border-2 p-4 rounded-3 mb-3">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">
                                                <i class="bi bi-tag-fill text-secondary me-1"></i>Jenis Layanan
                                            </label>
                                            <select name="details[0][service_type_id]" class="form-select form-select-lg"
                                                required>
                                                <option value="">-- Pilih Layanan --</option>
                                                @foreach ($serviceTypes as $type)
                                                    <option value="{{ $type->id }}">
                                                        {{ $type->name }} - Rp
                                                        {{ number_format($type->price, 0, ',', '.') }}
                                                        / {{ $type->unit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-medium">
                                                <i class="bi bi-speedometer2 text-secondary me-1"></i>Estimasi Berat /
                                                Kuantitas
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="number" name="details[0][estimated_value]"
                                                    class="form-control" step="0.01" placeholder="0.00" required>
                                                <span class="input-group-text bg-primary text-white fw-bold">kg</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="add-layanan"
                                    class="btn btn-outline-primary btn-lg w-100 mb-4">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Layanan Lainnya
                                </button>
                            </div>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm py-3">
                                    <i class="bi bi-send-fill me-2"></i>Kirim Pesanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Destroy nice-select if it was applied to our location dropdowns
            const kecamatanSelect = document.getElementById('kecamatan_id');
            const kelurahanSelect = document.getElementById('kelurahan_id');

            // Remove nice-select wrapper and restore original select elements
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.niceSelect !== 'undefined') {
                jQuery('#kecamatan_id').niceSelect('destroy');
                jQuery('#kelurahan_id').niceSelect('destroy');
            }

            // Make sure selects are visible
            kecamatanSelect.style.display = 'block';
            kelurahanSelect.style.display = 'block';

            console.log('Script loaded');
            console.log('Kecamatan select:', kecamatanSelect);
            console.log('Kelurahan select:', kelurahanSelect);

            kecamatanSelect.addEventListener('change', function() {
                const kecamatanId = this.value;

                console.log('Kecamatan changed to:', kecamatanId);

                // Reset kelurahan dropdown
                kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
                kelurahanSelect.disabled = true;
                kelurahanSelect.style.display = 'block';

                if (kecamatanId) {
                    console.log('Fetching kelurahan for kecamatan:', kecamatanId);

                    // Fetch kelurahan
                    fetch(`/api/kelurahan/${kecamatanId}`)
                        .then(response => {
                            console.log('Response status:', response.status);
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Data received:', data);
                            console.log('Number of kelurahan:', data.length);

                            // Clear existing options
                            kelurahanSelect.innerHTML =
                                '<option value="">-- Pilih Kelurahan --</option>';

                            // Enable dropdown
                            kelurahanSelect.disabled = false;
                            kelurahanSelect.style.display = 'block';
                            console.log('Kelurahan select disabled status:', kelurahanSelect.disabled);

                            if (data && data.length > 0) {
                                data.forEach(function(kelurahan) {
                                    const option = document.createElement('option');
                                    option.value = kelurahan.id;
                                    option.textContent = kelurahan.name;
                                    kelurahanSelect.appendChild(option);
                                    console.log('Added option:', kelurahan.name);
                                });
                                console.log('Total options in select:', kelurahanSelect.options.length);
                            } else {
                                const option = document.createElement('option');
                                option.value = '';
                                option.textContent = 'Tidak ada kelurahan';
                                kelurahanSelect.appendChild(option);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching kelurahan:', error);
                            alert('Gagal mengambil data kelurahan: ' + error.message);
                        });
                } else {
                    console.log('No kecamatan selected');
                }
            });
        });

        let index = 1;

        document.getElementById('add-layanan').onclick = function() {
            const container = document.getElementById('layanan-container');
            const original = container.querySelector('.layanan-item');
            const clone = original.cloneNode(true);

            // Create a wrapper div for the new item
            const wrapper = document.createElement('div');
            wrapper.className = 'position-relative mb-3';

            // Update name attributes for form elements
            clone.querySelectorAll('select, input').forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/\[\d+\]/, `[${index}]`);
                input.setAttribute('name', newName);
                input.value = ''; // Clear the values
            });

            // Remove margin bottom from clone since wrapper has it
            clone.classList.remove('mb-3');

            // Create "Hapus" button
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-danger btn-sm w-100 mt-2 remove-layanan';
            removeButton.innerHTML = '<i class="bi bi-trash-fill me-1"></i>Hapus Layanan Ini';

            // Append clone and button to wrapper
            wrapper.appendChild(clone);
            wrapper.appendChild(removeButton);

            // Append wrapper to container
            container.appendChild(wrapper);
            index++;
        };

        // Event delegation for remove button
        document.getElementById('layanan-container').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-layanan') || event.target.closest('.remove-layanan')) {
                const wrapper = event.target.closest('.position-relative');
                if (wrapper) {
                    wrapper.remove();
                }
            }
        });
    </script>
@endpush
