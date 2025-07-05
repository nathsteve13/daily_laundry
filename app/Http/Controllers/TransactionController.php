<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Models\PickupList;
use App\Models\ServiceType;
use App\Models\Transaction;
use App\Models\DeliveryList;
use Illuminate\Http\Request;
use App\Models\TransactionStatus;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Transaction::with(['transactionStatus', 'customers']);

            if ($request->filled('status')) {
                $query->whereHas('transactionStatus', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            if ($request->filled('search')) {
                $query->whereHas('customers', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->filled('sort') && in_array($request->sort, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->sort);
            } else {
                $query->latest();
            }

            $transactions = $query->get();
            $kurirs = User::where('role', 'kurir')->get();

            return view('transaction.index', compact('transactions', 'kurirs'));
        } catch (\Throwable $e) {
            dd($e->getMessage());
            report($e);
            return back()->with('error', 'Gagal memuat data transaksi.');
        }
    }



    public function assignKurir(Request $request)
    {
        try {
            $validated = $request->validate([
                'no_transaction'        => 'required|exists:transactions,no_transaction',
                'kurir_id'              => 'required|exists:users,id',
                'jenis'                 => 'required|in:ambil,terima',
                'tanggal_pengambilan'   => 'nullable|date',
                'tanggal_pengiriman'    => 'nullable|date',
            ]);

            if ($validated['jenis'] === 'ambil') {
                // Simpan ke PickupList
                $date = now()->format('Ymd');
                $last = PickupList::whereDate('created_at', now())->orderBy('no_pickup', 'desc')->first();
                $increment = $last ? str_pad((int) substr($last->no_pickup, -4) + 1, 4, '0', STR_PAD_LEFT) : '0001';
                $noPickup = 'PU-' . $date . '-' . $increment;

                PickupList::create([
                    'no_pickup'           => $noPickup,
                    'no_transaction'      => $validated['no_transaction'],
                    'kurir_id'            => $validated['kurir_id'],
                    'tanggal_pengambilan' => $validated['tanggal_pengambilan'] ?? now(),
                    'tanggal_diambil'     => $validated['tanggal_pengambilan'] ?? now(), // bisa ubah sesuai alurmu
                    'bukti_pengambilan'   => null
                ]);
            } elseif ($validated['jenis'] === 'terima') {
                // Simpan ke DeliveryList
                $date = now()->format('Ymd');
                $last = DeliveryList::whereDate('created_at', now())->orderBy('no_delivery', 'desc')->first();
                $increment = $last ? str_pad((int) substr($last->no_delivery, -4) + 1, 4, '0', STR_PAD_LEFT) : '0001';
                $noDelivery = 'DV-' . $date . '-' . $increment;

                DeliveryList::create([
                    'no_delivery'      => $noDelivery,
                    'no_transaction'   => $validated['no_transaction'],
                    'kurir_id'         => $validated['kurir_id'],
                    'tanggal_diantar'  => $validated['tanggal_pengiriman'] ?? now(),
                    'tanggal_terkirim' => $validated['tanggal_pengiriman'] ?? now(),
                    'bukti_terima'     => null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kurir berhasil di-assign.'
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'no_transaction' => 'required|exists:transactions,no_transaction',
                'status' => 'required|in:pending,pickup,proccessed,ready,delivered,done',
            ]);

            $status = TransactionStatus::create([
                'no_transaction' => $request->no_transaction,
                'status' => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'status' => ucfirst($status->status),
                'badge' => match ($status->status) {
                    'pending' => 'secondary',
                    'pickup' => 'warning',
                    'proccessed' => 'info',
                    'ready' => 'primary',
                    'delivered' => 'dark',
                    'done' => 'success',
                    default => 'light'
                },
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false]);
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
                'subtotal' => 'required|numeric',
                'discount' => 'required|numeric',
                'total' => 'required|numeric',
                'users_id' => 'required|integer|exists:users,id',
                'customers_id' => 'required|array',
                'customers_id.*' => 'integer|exists:customers,id',
                'details' => 'required|array',
                'details.*.service_type_id' => 'required|integer|exists:service_type,id',
                'details.*.value_per_unit' => 'required|numeric',
                'details.*.pickup' => 'nullable|boolean',
                'status.status' => 'required|in:pending,proccessed,ready,done',
            ]);

            DB::transaction(function () use ($validated) {
                $datePrefix = now()->format('dmY');
                $prefix = "TRX-{$datePrefix}-";

                $lastTransaction = Transaction::where('no_transaction', 'like', "$prefix%")
                    ->orderBy('no_transaction', 'desc')
                    ->first();

                if ($lastTransaction) {
                    $lastNumber = (int) substr($lastTransaction->no_transaction, -4);
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }

                $noTransaction = $prefix . $newNumber;

                $transaction = Transaction::create([
                    'no_transaction' => $noTransaction,
                    'subtotal' => $validated['subtotal'],
                    'discount' => $validated['discount'],
                    'total' => $validated['total'],
                    'users_id' => $validated['users_id'],
                ]);

                $transaction->customers()->attach($validated['customers_id']);

                foreach ($validated['details'] as $detail) {
                    $pickup = isset($detail['pickup']) ? (bool) $detail['pickup'] : false;

                    $transaction->details()->create([
                        'service_type_id' => $detail['service_type_id'],
                        'value_per_unit' => $detail['value_per_unit'],
                        'pickup' => $pickup,
                    ]);
                }

                $transaction->status()->create([
                    'status' => $validated['status']['status'],
                ]);
            });

            return redirect()->route('transactions.index')->with('success', 'Transaction created');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Failed to create transaction: ' . $e->getMessage());
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
                'status.status' => 'required|in:pending,proccessed,ready,done',
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
