<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\Transaction;
use App\Models\OrderRequest;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\TransactionStatus;use App\Models\Customer;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = OrderRequest::with('serviceType')->latest()->get();
            $customers = Customer::all();
            $serviceTypes = $orders->pluck('serviceType')->unique('id');
            return view('order.index', compact('orders', 'customers','serviceTypes'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal menampilkan data pesanan.');
        }
    }


    public function terima(Request $request)
    {
        try {
            $validated = $request->validate([
                'no_order' => 'required|exists:order_requests,no_order',
                'customers_id' => 'required|exists:customers,id',
                'pickup' => 'required|boolean',
                'value_per_unit' => 'required|numeric',
                'service_type_id' => 'required|exists:service_type,id',
                'discount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            // ambil order
            $order = OrderRequest::where('no_order', $request->no_order)->firstOrFail();

            // generate nomor transaksi
            $today = now()->format('dmY');
            $countToday = Transaction::whereDate('created_at', now())->count() + 1;
            $noTransaction = 'TRX-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $subtotal = $validated['value_per_unit'] * ServiceType::findOrFail($validated['service_type_id'])->price;
            $discount = $validated['discount'];
            $total = $validated['total'];

            // simpan transaksi
            $trx = Transaction::create([
                'no_transaction' => $noTransaction,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'users_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // relasi customer
            DB::table('transactions_has_customers')->insert([
                'no_transaction' => $trx->no_transaction,
                'customers_id' => $validated['customers_id'],
            ]);

            // detail transaksi
            TransactionDetail::create([
                'no_transaction' => $trx->no_transaction,
                'pickup' => $validated['pickup'],
                'value_per_unit' => $validated['value_per_unit'],
                'service_type_id' => $validated['service_type_id'],
            ]);

            // status transaksi
            $statusId = TransactionStatus::max('id') + 1;
            TransactionStatus::create([
                'id' => $statusId,
                'status' => 'pending',
                'no_transaction' => $trx->no_transaction,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // tandai order selesai
            $order->update(['status' => 'selesai']);

            DB::commit();
            return redirect()->route('order.index')->with('success', 'Transaksi berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Gagal menerima pesanan.');
        }
    }


}
