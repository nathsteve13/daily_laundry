<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            $customers = Customer::all();
            return view('customer.index', compact('customers'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load customers');
        }
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:45',
                'address' => 'required|string|max:255',
                'phone_number' => 'required|string|max:45',
            ]);

            DB::transaction(fn () => Customer::create($validated));

            return redirect()->route('customers.index')->with('success', 'Customer created');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create customer');
        }
    }

    public function edit($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            return view('customer.edit', compact('customer'));
        } catch (\Exception $e) {
            return redirect()->route('customers.index')->with('error', 'Customer not found');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:45',
                'address' => 'required|string|max:255',
                'phone_number' => 'required|string|max:45',
            ]);

            DB::transaction(function () use ($id, $validated) {
                $customer = Customer::findOrFail($id);
                $customer->update($validated);
            });

            return redirect()->route('customers.index')->with('success', 'Customer updated');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update customer');
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            return redirect()->route('customers.index')->with('success', 'Customer deleted');
        } catch (\Exception $e) {
            return redirect()->route('customers.index')->with('error', 'Failed to delete customer');
        }
    }
}
