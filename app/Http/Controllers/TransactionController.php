<?php


namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        try {
            $transactions = Transaction::with(['customers', 'details', 'status'])->get();
            return view('transaction.index', compact('transactions'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load transactions');
        }
    }

    public function create()
    {
        $customers = Customer::all();
        $services = ServiceType::all();
        return view('transaction.create', compact('customers', 'services'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'no_transaction' => 'required|string',
                'subtotal' => 'required|numeric',
                'discount' => 'required|numeric',
                'total' => 'required|numeric',
                'users_id' => 'required|integer|exists:users,id',
                'customers_id' => 'required|array',
                'customers_id.*' => 'integer|exists:customers,id',
                'details' => 'required|array',
                'details.*.pickup' => 'required|boolean',
                'details.*.value_per_unit' => 'required|numeric',
                'details.*.service_type_id' => 'required|integer|exists:service_type,id',
                'status.status' => 'required|in:pending,proccessed,ready,done'
            ]);

            DB::transaction(function () use ($validated) {
                $transaction = Transaction::create([
                    'no_transaction' => $validated['no_transaction'],
                    'subtotal' => $validated['subtotal'],
                    'discount' => $validated['discount'],
                    'total' => $validated['total'],
                    'users_id' => $validated['users_id'],
                ]);

                $transaction->customers()->attach($validated['customers_id']);

                foreach ($validated['details'] as $detail) {
                    $transaction->details()->create($detail);
                }

                $transaction->status()->create(['status' => $validated['status']['status']]);
            });

            return redirect()->route('transactions.index')->with('success', 'Transaction created');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create transaction');
        }
    }

    public function edit($no_transaction)
    {
        try {
            $transaction = Transaction::with(['customers', 'details', 'status'])->findOrFail($no_transaction);
            $customers = Customer::all();
            $services = ServiceType::all();
            return view('transaction.edit', compact('transaction', 'customers', 'services'));
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')->with('error', 'Transaction not found');
        }
    }

    public function update(Request $request, $no_transaction)
    {
        try {
            $validated = $request->validate([
                'subtotal' => 'required|numeric',
                'discount' => 'required|numeric',
                'total' => 'required|numeric',
                'users_id' => 'required|integer|exists:users,id',
                'customers_id' => 'required|array',
                'customers_id.*' => 'integer|exists:customers,id',
                'details' => 'required|array',
                'details.*.pickup' => 'required|boolean',
                'details.*.value_per_unit' => 'required|numeric',
                'details.*.service_type_id' => 'required|integer|exists:service_type,id',
                'status.status' => 'required|in:pending,proccessed,ready,done'
            ]);

            DB::transaction(function () use ($no_transaction, $validated) {
                $transaction = Transaction::findOrFail($no_transaction);

                $transaction->update([
                    'subtotal' => $validated['subtotal'],
                    'discount' => $validated['discount'],
                    'total' => $validated['total'],
                    'users_id' => $validated['users_id'],
                ]);

                $transaction->customers()->sync($validated['customers_id']);
                $transaction->details()->delete();

                foreach ($validated['details'] as $detail) {
                    $transaction->details()->create($detail);
                }

                $transaction->status()->update(['status' => $validated['status']['status']]);
            });

            return redirect()->route('transactions.index')->with('success', 'Transaction updated');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update transaction');
        }
    }

    public function destroy($no_transaction)
    {
        try {
            DB::transaction(function () use ($no_transaction) {
                $transaction = Transaction::findOrFail($no_transaction);
                $transaction->customers()->detach();
                $transaction->details()->delete();
                $transaction->status()->delete();
                $transaction->delete();
            });

            return redirect()->route('transactions.index')->with('success', 'Transaction deleted');
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')->with('error', 'Failed to delete transaction');
        }
    }
}
