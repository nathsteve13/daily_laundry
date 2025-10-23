<!-- Modal Terima Pesanan -->
<div id="terimaModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('order.terima') }}" method="POST">
                @csrf
                <input type="hidden" name="no_order" id="modal_no_order">

                <div class="modal-header">
                    <h5 class="modal-title">Terima Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Data Pesanan</label>
                        <div id="orderData" class="border p-3 rounded bg-light text-sm"></div>
                    </div>

                    <div>
                        <label class="form-label">Pilih Customer</label>
                        <div class="d-flex gap-2 mb-2">
                            <input type="text" name="customer_search" class="form-control"
                                placeholder="Cari customer...">
                            <a href="{{ route('customers.create') }}" class="btn btn-outline-primary">+ Customer
                                Baru</a>
                        </div>

                        <select name="customers_id" class="form-select" required>
                            <option value="">-- Pilih Customer --</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->phone_number }}
                                </option>
                            @endforeach
                        </select>

                        <div id="customer-suggestions" class="mt-2"></div>
                    </div>

                    <div>
                        <label class="form-label">Detail Transaksi</label>
                        <div id="layanan-list" class="row g-3"></div>

                        <div class="row mt-4">
                            <div class="col-md-4">
                                <label class="form-label">Subtotal</label>
                                <input type="number" name="subtotal_display" id="subtotal_display"
                                    class="form-control bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Diskon (Rp)</label>
                                <input type="number" name="discount" id="discount" class="form-control" step="0.01"
                                    value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Total</label>
                                <input type="number" name="total_display" id="total_display"
                                    class="form-control bg-light" readonly>
                            </div>
                        </div>

                        <input type="hidden" name="subtotal" id="subtotal">
                        <input type="hidden" name="total" id="total">
                    </div>
                </div>

                <input type="hidden" name="pickup" id="pickup">

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">✔ Terima</button>
                </div>
            </form>
        </div>
    </div>
</div>
