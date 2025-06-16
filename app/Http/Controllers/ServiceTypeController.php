<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceTypeController extends Controller
{
    public function index()
    {
        try {
            $services = ServiceType::all();
            return view('service.index', compact('services'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load services');
        }
    }

    public function create()
    {
        return view('service.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:45',
                'price' => 'required|numeric',
                'duration' => 'required|numeric',
                'unit' => 'required|string|max:45',
            ]);

            DB::transaction(fn () => ServiceType::create($validated));

            return redirect()->route('service-types.index')->with('success', 'Service created');
        } catch (\Exception $e) {
            dd($e);
            return back()->withInput()->with('error', 'Failed to create service');
        }
    }

    public function edit($id)
    {
        try {
            $service = ServiceType::findOrFail($id);
            return view('service.edit', compact('service'));
        } catch (\Exception $e) {
            return redirect()->route('service-types.index')->with('error', 'Service not found');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:45',
                'price' => 'required|numeric',
                'duration' => 'required|numeric',
                'unit' => 'required|string|max:45',
            ]);

            DB::transaction(function () use ($id, $validated) {
                $service = ServiceType::findOrFail($id);
                $service->update($validated);
            });

            return redirect()->route('service-types.index')->with('success', 'Service updated');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update service');
        }
    }

    public function destroy($id)
    {
        try {
            $service = ServiceType::findOrFail($id);
            $service->delete();
            return redirect()->route('service-types.index')->with('success', 'Service deleted');
        } catch (\Exception $e) {
            return redirect()->route('service-types.index')->with('error', 'Failed to delete service');
        }
    }
}
