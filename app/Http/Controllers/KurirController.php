<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PickupList;
use App\Models\Transaction;
use App\Models\DeliveryList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KurirController extends Controller
{
    public function index()
    {
        $deliveries = DeliveryList::with('kurir')->get();
        return view('kurir.pengantaran', compact('deliveries'));
    }

    public function create()
    {
        $transactions = Transaction::pluck('no_transaction');
        $kurirs = User::where('role', 'kurir')->get();
        return view('kurir.create-pengantaran', compact('transactions', 'kurirs'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validate([
                'no_transaction' => 'required',
                'kurir_id' => 'required|exists:users,id',
                'tanggal_diantar' => 'required|date',
                'tanggal_terkirim' => 'required|date',
                'bukti_terima' => 'nullable|file|mimes:jpg,png,jpeg|max:2048'
            ]);

            $date = now()->format('Ymd');
            $lastDelivery = DeliveryList::whereDate('created_at', now())->orderBy('no_delivery', 'desc')->first();

            if ($lastDelivery) {
                $lastIncrement = (int) substr($lastDelivery->no_delivery, -4);
                $newIncrement = str_pad($lastIncrement + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newIncrement = '0001';
            }

            $data['no_delivery'] = 'DV-' . $date . '-' . $newIncrement;

            if ($request->hasFile('bukti_terima')) {
                $filename = 'bukti/' . uniqid() . '.' . $request->file('bukti_terima')->getClientOriginalExtension();
                $request->file('bukti_terima')->move(public_path('bukti'), $filename);
                $data['bukti_terima'] = $filename;
            }

            DeliveryList::create($data);
            DB::commit();
            return redirect()->route('kurir.pengantaran.index')->with('success', 'Pengantaran berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e->getMessage());
            report($e);
            return back()->withInput()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function edit($no_delivery)
    {
        $delivery = DeliveryList::where('no_delivery', $no_delivery)->firstOrFail();

        $transactions = Transaction::pluck('no_transaction');
        $kurirs = User::where('role', 'kurir')->get();
        return view('kurir.edit-pengantaran', compact('delivery', 'transactions', 'kurirs'));
    }

    public function update(Request $request, $id)
    {
        try {
            $delivery = DeliveryList::where('no_delivery', $id)->firstOrFail();

            $validated = $request->validate([
                'no_transaction'   => 'required',
                'kurir_id'         => 'required|exists:users,id',
                'tanggal_diantar'  => 'required|date',
                'tanggal_terkirim' => 'required|date',
                'bukti_terima'     => 'nullable|file|mimes:jpg,jpeg,png|max:40000',
            ]);
            

            if ($request->hasFile('bukti_terima')) {
                // Hapus file lama jika ada
                if (!empty($delivery->bukti_terima)) {
                    $oldFile = public_path($delivery->bukti_terima);
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                // Simpan file baru ke public/bukti
                $filename = 'bukti/' . uniqid() . '.' . $request->file('bukti_terima')->getClientOriginalExtension();
                $request->file('bukti_terima')->move(public_path('bukti'), $filename);
                $validated['bukti_terima'] = $filename;
            }

            $delivery->update($validated);

            return redirect()->route('kurir.pengantaran.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }


    public function destroy($id)
    {
        try {
            $delivery = DeliveryList::where('no_delivery', $id)->firstOrFail();

            if ($delivery->bukti_terima) {
                $filePath = public_path($delivery->bukti_terima);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $delivery->delete();

            return redirect()->route('kurir.pengantaran.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            report($e);
            return back()->with('error', 'Gagal menghapus data.');
        }
    }




    public function pengambilanIndex()
    {
        $data = PickupList::with(['transaction', 'kurir'])->latest()->get();
        return view('kurir.pengambilan', compact('data'));
    }

    public function pengambilanCreate()
    {
        $transactions = Transaction::pluck('no_transaction');
        $kurirs = User::where('role', 'kurir')->get();
        return view('kurir.create-pengambilan', compact('transactions', 'kurirs'));
    }

    public function pengambilanStore(Request $request)
    {
        try {

            $request->validate([
                'no_transaction' => 'required',
                'kurir_id' => 'required|exists:users,id',
                'tanggal_pengambilan' => 'required',
                'tanggal_diambil' => 'required',
                'bukti_ambil' => 'nullable|image|max:40000'
            ]);
            DB::beginTransaction();
            $filename = null;

            if ($request->hasFile('bukti_ambil')) {
                $filename = 'pickup/' . uniqid() . '.' . $request->file('bukti_ambil')->getClientOriginalExtension();
                $request->file('bukti_ambil')->move(public_path('pickup'), $filename);
            } else {
                $filename = null;
            }

            $date = now()->format('Ymd');
            $lastPickup = PickupList::whereDate('created_at', now())->orderBy('no_pickup', 'desc')->first();

            if ($lastPickup) {
                $lastIncrement = (int) substr($lastPickup->no_pickup, -4);
                $newIncrement = str_pad($lastIncrement + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newIncrement = '0001';
            }

            $request->merge(['no_pickup' => 'PU-' . $date . '-' . $newIncrement]);

            PickupList::create([
                'no_pickup' => $request->no_pickup,
                'no_transaction' => $request->no_transaction,
                'kurir_id' => $request->kurir_id,
                'tanggal_pengambilan' => $request->tanggal_pengambilan,
                'tanggal_diambil' => $request->tanggal_diambil,
                'bukti_pengambilan' => $filename
            ]);

            DB::commit();
            return redirect()->route('kurir.pengambilan.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollBack();
            report($e);
            return back()->with('error', 'Gagal menyimpan data.')->withInput();
        }
    }

    public function pengambilanEdit($id)
    {
        $pengambilan = PickupList::findOrFail($id);
        $transactions = Transaction::pluck('no_transaction');
        $kurirs = User::where('role', 'kurir')->get();
        return view('kurir.edit-pengambilan', compact('pengambilan', 'transactions', 'kurirs'));
    }

    public function pengambilanUpdate(Request $request, $id)
    {
        try {
            $request->validate([
                'no_transaction' => 'required',
                'kurir_id' => 'required|exists:users,id',
                'tanggal_pengambilan' => 'required|date',
                'tanggal_diambil' => 'required|date',
                'bukti_ambil' => 'nullable|image|max:2048',
            ]);

            DB::beginTransaction();

            $pickup = PickupList::findOrFail($id);

            if ($request->hasFile('bukti_ambil')) {
                // Hapus file lama jika ada
                if ($pickup->bukti_pengambilan && file_exists(public_path($pickup->bukti_pengambilan))) {
                    unlink(public_path($pickup->bukti_pengambilan));
                }

                $filename = 'pickup/' . uniqid() . '.' . $request->file('bukti_ambil')->getClientOriginalExtension();
                $request->file('bukti_ambil')->move(public_path('pickup'), $filename);

                $pickup->bukti_pengambilan = $filename;
            }

            $pickup->update([
                'no_transaction' => $request->no_transaction,
                'kurir_id' => $request->kurir_id,
                'tanggal_pengambilan' => $request->tanggal_pengambilan,
                'tanggal_diambil' => $request->tanggal_diambil,
            ]);

            DB::commit();
            return redirect()->route('kurir.pengambilan.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollBack();
            report($e);
            return back()->with('error', 'Gagal mengupdate data.')->withInput();
        }
    }


    public function pengambilanDestroy($id)
    {
        try {
            $pickup = PickupList::findOrFail($id);
            $pickup->delete();
            return redirect()->route('kurir.pengambilan')->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}
