@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Service Types</h1>
    <a href="{{ route('service-types.create') }}" class="btn btn-primary mb-3">Add Service</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
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
                <td>
                    <a href="{{ route('service-types.edit', $service->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('service-types.destroy', $service->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
