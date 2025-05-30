<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        try {
            $payments = Payment::with('transaction')->get();
            return view('payment.index', compact('payments'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load payments');
        }
    }

    public function create()
    {
        $transactions = Transaction::all();
        return view('payment.create', compact('transactions'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'no_payment' => 'required|string|unique:payments,no_payment',
                'no_transaction' => 'required|string|exists:transactions,no_transaction',
                'total' => 'required|numeric',
                'status' => 'required|in:pending,failed,success'
            ]);

            DB::transaction(fn () => Payment::create($validated));

            return redirect()->route('payments.index')->with('success', 'Payment created');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create payment');
        }
    }

    public function edit($no_payment)
    {
        try {
            $payment = Payment::findOrFail($no_payment);
            $transactions = Transaction::all();
            return view('payment.edit', compact('payment', 'transactions'));
        } catch (\Exception $e) {
            return redirect()->route('payments.index')->with('error', 'Payment not found');
        }
    }

    public function update(Request $request, $no_payment)
    {
        try {
            $validated = $request->validate([
                'no_transaction' => 'required|string|exists:transactions,no_transaction',
                'total' => 'required|numeric',
                'status' => 'required|in:pending,failed,success'
            ]);

            DB::transaction(function () use ($no_payment, $validated) {
                $payment = Payment::findOrFail($no_payment);
                $payment->update($validated);
            });

            return redirect()->route('payments.index')->with('success', 'Payment updated');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update payment');
        }
    }

    public function destroy($no_payment)
    {
        try {
            $payment = Payment::findOrFail($no_payment);
            $payment->delete();
            return redirect()->route('payments.index')->with('success', 'Payment deleted');
        } catch (\Exception $e) {
            return redirect()->route('payments.index')->with('error', 'Failed to delete payment');
        }
    }
}
