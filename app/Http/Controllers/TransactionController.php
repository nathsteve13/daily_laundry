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

}
