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

    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">+ Create Service Type</h1>
            <a href="{{ route('service-types.index') }}" class="btn btn-light notion-btn">Back</a>
        </div>

        <div class="bg-white notion-box overflow-hidden p-6 shadow rounded-3 border-0">
            <form action="{{ route('service-types.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control mb-3" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control mb-3" value="{{ old('price') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Duration</label>
                    <input type="number" step="0.1" name="duration" class="form-control mb-3" value="{{ old('duration') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit</label>
                    <input type="text" name="unit" class="form-control mb-3" value="{{ old('unit') }}" required>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('service-types.index') }}" class="btn btn-outline-secondary notion-btn">Cancel</a>
                    <button type="submit" class="btn btn-dark notion-btn">Save</button>
                </div>
            </form>
        </div>
    </div>

@endsection
