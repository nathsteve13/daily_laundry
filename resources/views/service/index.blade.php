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

    @if (session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">🛎️ Service Types</h1>
            <a href="{{ route('service-types.create') }}" class="btn btn-dark notion-btn">+ New Service</a>
        </div>

        <div class="bg-white notion-box overflow-hidden">
            <table class="table align-middle mb-0 table-hover text-nowrap w-full">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase small">
                        <th>ID</th><th>Name</th><th>Price</th><th>Duration</th><th>Unit</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td>{{ $service->id }}</td>
                        <td>{{ $service->name }}</td>
                        <td>{{ $service->price }}</td>
                        <td>{{ $service->duration }}</td>
                        <td>{{ $service->unit }}</td>
                        <td class="text-end d-flex gap-2 justify-end">
                            <a href="{{ route('service-types.edit', $service->id) }}" class="btn btn-outline-dark btn-sm notion-btn">Edit</a>
                            <form action="{{ route('service-types.destroy', $service->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-dark btn-sm notion-btn" onclick="return confirm('Delete this service?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
