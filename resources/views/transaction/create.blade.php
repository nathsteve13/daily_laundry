@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Transaction</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>No. Transaction</label>
            <input type="text" name="no_transaction" class="form-control" value="{{ old('no_transaction') }}">
            @error('no_transaction') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <input type="hidden" name="users_id" value="{{ auth()->id() }}">

        <div class="mb-3">
            <label>Customers</label>
            <select name="customers_id[]" class="form-control" multiple>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ collect(old('customers_id'))->contains($c->id)?'selected':'' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
            @error('customers_id') <div class="text-danger">{{ $message }}</div> @enderror
            @error('customers_id.*') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label>Subtotal</label>
                <input type="number" step="0.01" name="subtotal" class="form-control" value="{{ old('subtotal') }}">
                @error('subtotal') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label>Discount</label>
                <input type="number" step="0.01" name="discount" class="form-control" value="{{ old('discount') }}">
                @error('discount') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label>Total</label>
                <input type="number" step="0.01" name="total" class="form-control" value="{{ old('total') }}">
                @error('total') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>

        <hr>

        <h5>Details</h5>
        <div id="details">
            <div class="detail-row row mb-2">
                <div class="col">
                    <label>Service Type</label>
                    <select name="details[0][service_type_id]" class="form-control">
                        @foreach($services as $s)
                            <option value="{{ $s->id }}" {{ old('details.0.service_type_id')==$s->id?'selected':'' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('details.0.service_type_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col">
                    <label>Pickup</label><br>
                    <input type="checkbox" name="details[0][pickup]" value="1" {{ old('details.0.pickup')?'checked':'' }}>
                    @error('details.0.pickup') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col">
                    <label>Value per unit</label>
                    <input type="number" step="0.01" name="details[0][value_per_unit]" class="form-control" value="{{ old('details.0.value_per_unit') }}">
                    @error('details.0.value_per_unit') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="col-auto align-self-end">
                    <button type="button" class="btn btn-danger remove-detail">×</button>
                </div>
            </div>
        </div>
        <button type="button" id="add-detail" class="btn btn-secondary mb-3">Add Detail</button>

        <div class="mb-3">
            <label>Status</label>
            <select name="status[status]" class="form-control">
                @foreach(['pending','proccessed','ready','done'] as $st)
                    <option value="{{ $st }}" {{ old('status.status')==$st?'selected':'' }}>
                        {{ ucfirst($st) }}
                    </option>
                @endforeach
            </select>
            @error('status.status') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-success">Save</button>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    let detailIndex = 1;
    document.getElementById('add-detail').addEventListener('click', () => {
        const template = document.querySelector('.detail-row').cloneNode(true);
        template.querySelectorAll('select, input').forEach(el => {
            let nm = el.name.replace(/\d+/, detailIndex);
            el.name = nm;
            if(el.type!=='checkbox') el.value = ''; else el.checked = false;
        });
        document.getElementById('details').append(template);
        detailIndex++;
    });
    document.getElementById('details').addEventListener('click', e => {
        if(e.target.classList.contains('remove-detail')) {
            if(document.querySelectorAll('.detail-row').length > 1) {
                e.target.closest('.detail-row').remove();
            }
        }
    });
});
</script>
@endsection
