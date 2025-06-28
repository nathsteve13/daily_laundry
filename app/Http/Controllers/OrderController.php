<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\Transaction;
use App\Models\OrderRequest;
use App\Models\TransactionDetail;
use App\Models\TransactionStatus;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = OrderRequest::with('details.serviceType')->latest()->get();
            $customers = Customer::all();
            $serviceTypes = ServiceType::all();

            return view('order.index', compact('orders', 'customers', 'serviceTypes'));
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
                'details' => 'required|array|min:1',
                'details.*.service_type_id' => 'required|exists:service_type,id',
                'details.*.estimated_value' => 'required|numeric|min:0.1',
                'discount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
            ]);
            DB::beginTransaction();

            $order = OrderRequest::where('no_order', $validated['no_order'])->firstOrFail();

            $today = now()->format('dmY');
            $countToday = Transaction::whereDate('created_at', now())->count() + 1;
            $noTransaction = 'TRX-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $subtotal = 0;
            foreach ($validated['details'] as $item) {
                $service = ServiceType::findOrFail($item['service_type_id']);
                $subtotal += $service->price * $item['estimated_value'];
            }

            $trx = Transaction::create([
                'no_transaction' => $noTransaction,
                'subtotal' => $subtotal,
                'discount' => $validated['discount'],
                'total' => $validated['total'],
                'users_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('transactions_has_customers')->insert([
                'no_transaction' => $noTransaction,
                'customers_id' => $validated['customers_id'],
            ]);

            foreach ($validated['details'] as $item) {
                TransactionDetail::create([
                    'no_transaction' => $noTransaction,
                    'pickup' => $validated['pickup'],
                    'value_per_unit' => (float) $item['estimated_value'],
                    'service_type_id' => (int) $item['service_type_id'],
                ]);
            }


            $statusId = TransactionStatus::max('id') + 1;
            TransactionStatus::create([
                'id' => $statusId,
                'status' => 'pending',
                'no_transaction' => $noTransaction,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $order->update(['status' => 'selesai']);

            DB::commit();
            return redirect()->route('order.index')->with('success', 'Transaksi berhasil dibuat.');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollBack();
            report($e);
            return back()->with('error', 'Gagal menerima pesanan.');
        }
    }
}
