@extends('layouts.app')

@section('content')

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">✏️ Edit Transaction</h1>
            <a href="{{ route('transactions.index') }}" class="btn btn-light notion-btn">Back</a>
        </div>

        <div class="bg-white notion-box overflow-hidden p-6 shadow rounded-3 border-0">
            <form action="{{ route('transactions.update', $transaction->no_transaction) }}" method="POST">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label">No. Transaction</label>
                    <input type="text" class="form-control mb-3" value="{{ $transaction->no_transaction }}" disabled>
                </div>
                <input type="hidden" name="users_id" value="{{ auth()->id() }}">

                {{-- Kecamatan & Kelurahan --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Kecamatan
                        </label>
                        <select name="kecamatan_id" id="kecamatan_id" class="form-control" required>
                            <option value="">-- Pilih Kecamatan Dahulu --</option>
                            @foreach ($kecamatans as $kec)
                                <option value="{{ $kec->id }}"
                                    {{ old('kecamatan_id', $transaction->kecamatan_id) == $kec->id ? 'selected' : '' }}>
                                    {{ $kec->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('kecamatan_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fas fa-map-pin me-2"></i>Kelurahan
                        </label>
                        <select name="kelurahan_id" id="kelurahan_id" class="form-control" required
                            {{ empty($transaction->kecamatan_id) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>
                            @foreach ($kelurahans as $kel)
                                <option value="{{ $kel->id }}"
                                    {{ old('kelurahan_id', $transaction->kelurahan_id) == $kel->id ? 'selected' : '' }}>
                                    {{ $kel->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelurahan_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Subtotal (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" step="0.01" id="subtotal" name="subtotal" class="form-control"
                                value="{{ old('subtotal', $transaction->subtotal) }}" required readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Discount (%)</label>
                        <div class="input-group">
                            <span class="input-group-text">%</span>
                            <input type="number" step="0.01" id="discount_percent" name="discount_percent"
                                class="form-control"
                                value="{{ old('discount_percent', $transaction->subtotal > 0 ? round(($transaction->discount / $transaction->subtotal) * 100, 2) : 0) }}"
                                min="0" max="100">
                        </div>
                        <small class="text-muted">Masukkan persentase discount (0-100)</small>
                        <!-- Hidden field untuk discount dalam rupiah -->
                        <input type="hidden" id="discount_amount" name="discount"
                            value="{{ old('discount', $transaction->discount) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" step="0.01" id="total" name="total" class="form-control"
                                value="{{ old('total', $transaction->total) }}" required readonly>
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Details</h5>
                <div id="details">
                    @foreach (old('details', $transaction->details->toArray()) as $i => $d)
                        <div class="detail-row row mb-2">
                            <div class="col">
                                <label class="form-label">Service Type</label>
                                <select name="details[{{ $i }}][service_type_id]" class="form-control">
                                    @foreach ($services as $s)
                                        <option value="{{ $s->id }}"
                                            {{ ($d['service_type_id'] ?? '') == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label class="form-label">Pickup</label><br>
                                <input type="checkbox" name="details[{{ $i }}][pickup]" value="1"
                                    {{ !empty($d['pickup']) ? 'checked' : '' }}>
                            </div>
                            <div class="col">
                                <label class="form-label">Value per unit</label>
                                <input type="number" step="0.01" name="details[{{ $i }}][value_per_unit]"
                                    class="form-control" value="{{ $d['value_per_unit'] }}">
                            </div>
                            <div class="col-auto align-self-end">
                                <button type="button" class="btn btn-danger remove-detail">×</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-detail" class="btn btn-outline-secondary mb-4 notion-btn">Add
                    Detail</button>

                @php
                    $statuses = ['pending', 'proccessed', 'ready', 'done'];
                    $current = $transaction->status->status;
                    $curIdx = array_search($current, $statuses);
                @endphp

                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <select name="status[status]" class="form-control">
                        @foreach ($statuses as $i => $st)
                            <option value="{{ $st }}"
                                {{ old('status.status', $current) == $st ? 'selected' : '' }}
                                {{ $i !== $curIdx && $i !== $curIdx + 1 ? 'disabled' : '' }}>
                                {{ ucfirst($st) }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="flex justify-end gap-2">
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary notion-btn">Cancel</a>
                    <button type="submit" class="btn btn-dark notion-btn">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Fungsi untuk menghitung total berdasarkan discount percent
            function calculateTotal() {
                const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
                const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
                const discountAmount = (subtotal * discountPercent) / 100;

                // Update hidden field discount amount
                document.getElementById('discount_amount').value = discountAmount.toFixed(2);

                // Update total
                const total = subtotal - discountAmount;
                document.getElementById('total').value = total.toFixed(2);
            }

            // Event listener untuk discount percent
            document.getElementById('discount_percent').addEventListener('input', calculateTotal);

            // AJAX untuk load kelurahan berdasarkan kecamatan
            const kecamatanSelect = document.getElementById('kecamatan_id');
            const kelurahanSelect = document.getElementById('kelurahan_id');

            kecamatanSelect.addEventListener('change', function() {
                const kecamatanId = this.value;

                // Reset kelurahan
                kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

                if (kecamatanId) {
                    // Enable kelurahan dropdown
                    kelurahanSelect.disabled = false;

                    fetch(`/api/kelurahan/${kecamatanId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(kelurahan => {
                                const option = document.createElement('option');
                                option.value = kelurahan.id;
                                option.textContent = kelurahan.name;
                                kelurahanSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                } else {
                    // Disable kelurahan dropdown jika kecamatan belum dipilih
                    kelurahanSelect.disabled = true;
                    kelurahanSelect.innerHTML =
                        '<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>';
                }
            });

            let detailIndex = {{ $transaction->details->count() }};

            document.getElementById('add-detail').addEventListener('click', () => {
                const template = document.querySelector('.detail-row').cloneNode(true);
                template.querySelectorAll('select, input').forEach(el => {
                    el.name = el.name.replace(/\d+/, detailIndex);
                    if (el.type !== 'checkbox') el.value = '';
                    else el.checked = false;
                });
                document.getElementById('details').append(template);
                detailIndex++;
            });

            document.getElementById('details').addEventListener('click', e => {
                if (e.target.classList.contains('remove-detail') && document.querySelectorAll('.detail-row')
                    .length > 1) {
                    e.target.closest('.detail-row').remove();
                }
            });
        });
    </script>

@endsection
