<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\ServiceType;
use App\Models\OrderRequest;
use Illuminate\Http\Request;
use App\Models\OrderRequestDetail;
use Illuminate\Support\Facades\DB;

class PesanController extends Controller
{
    public function create()
    {
        try {
            $serviceTypes = ServiceType::all();
            $kecamatans = Kecamatan::orderBy('name')->get();
            return view('pesan', compact('serviceTypes', 'kecamatans'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal membuka halaman form.');
        }
    }

    public function getKelurahan($kecamatan_id)
    {
        try {
            $kelurahans = Kelurahan::where('kecamatan_id', $kecamatan_id)
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json($kelurahans);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Gagal mengambil data kelurahan'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'address' => 'required|string',
                'kecamatan_id' => 'required|exists:kecamatan,id',
                'kelurahan_id' => 'required|exists:kelurahan,id',
                'phone_number' => 'required|string|max:20',
                'delivery_type' => 'required|in:ambil-kirim,kirim',
                'details' => 'required|array|min:1',
                'details.*.service_type_id' => 'required|exists:service_type,id',
                'details.*.estimated_value' => 'required|numeric|min:0.1',
            ]);

            DB::beginTransaction();

            $today = now()->format('dmY');
            $countToday = OrderRequest::whereDate('created_at', now()->toDateString())->count() + 1;
            $noOrder = 'RO-NO-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            OrderRequest::create([
                'no_order' => $noOrder,
                'name' => $validated['name'],
                'address' => $validated['address'],
                'kecamatan_id' => $validated['kecamatan_id'],
                'kelurahan_id' => $validated['kelurahan_id'],
                'phone_number' => $validated['phone_number'],
                'delivery_type' => $validated['delivery_type'],
                'status' => 'diterima',
            ]);

            foreach ($validated['details'] as $item) {
                OrderRequestDetail::create([
                    'order_request_no_order' => $noOrder,
                    'service_type_id' => $item['service_type_id'],
                    'estimated_value' => $item['estimated_value'],
                ]);
            }

            DB::commit();
            return redirect()->route('pesan.create')->with('success', 'Pesanan berhasil dikirim.');
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e->getMessage());
            report($e);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pesanan.');
        }
    }
}
