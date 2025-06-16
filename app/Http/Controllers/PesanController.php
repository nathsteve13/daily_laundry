<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderRequest;
use App\Models\ServiceType;
use Illuminate\Support\Facades\DB;

class PesanController extends Controller
{
    public function create()
    {
        try {
            $serviceTypes = ServiceType::all();
            return view('pesan', compact('serviceTypes'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal membuka halaman form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'address' => 'required|string',
                'phone_number' => 'required|string|max:20',
                'estimated_value' => 'required|numeric',
                'service_type_id' => 'required|exists:service_type,id',
                'delivery_type' => 'required|in:ambil-kirim,kirim',
            ]);

            DB::beginTransaction();

            $today = now()->format('dmY');
            $countToday = OrderRequest::whereDate('created_at', now()->toDateString())->count() + 1;
            $noOrder = 'RO-NO-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            OrderRequest::create([
                'no_order' => $noOrder,
                'name' => $validated['name'],
                'address' => $validated['address'],
                'phone_number' => $validated['phone_number'],
                'estimated_value' => $validated['estimated_value'],
                'service_type_id' => $validated['service_type_id'],
                'delivery_type' => $validated['delivery_type'],
                'status' => 'diterima',
            ]);

            DB::commit();
            return redirect()->route('pesan.create')->with('success', 'Pesanan berhasil dikirim.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pesanan.');
        }
    }

}
