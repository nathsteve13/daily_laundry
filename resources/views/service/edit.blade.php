@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Service Type</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('service-types.update', $service->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $service->name) }}">
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $service->price) }}">
            @error('price') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label>Duration</label>
            <input type="number" step="0.1" name="duration" class="form-control" value="{{ old('duration', $service->duration) }}">
            @error('duration') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label>Unit</label>
            <input type="text" name="unit" class="form-control" value="{{ old('unit', $service->unit) }}">
            @error('unit') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <button class="btn btn-success">Update</button>
        <a href="{{ route('service-types.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
