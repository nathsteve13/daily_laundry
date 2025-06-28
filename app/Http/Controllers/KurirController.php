<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PickupList;
use App\Models\Transaction;
use App\Models\DeliveryList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $data = $request->validate([
                'no_delivery' => 'required|integer|unique:delivery_lists,no_delivery',
                'no_transaction' => 'required|exists:transactions,no_transaction',
                'kurir_id' => 'required|exists:users,id',
                'tanggal_diantar' => 'required|date',
                'tanggal_terkirim' => 'required|date',
                'bukti_terima' => 'nullable|file|mimes:jpg,png,jpeg|max:2048'
            ]);

            if ($request->hasFile('bukti_terima')) {
                $data['bukti_terima'] = $request->file('bukti_terima')->store('bukti', 'public');
            }

            DeliveryList::create($data);
            return redirect()->route('kurir.pengantaran.index')->with('success', 'Pengantaran berhasil ditambahkan.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function edit($id)
    {
        $delivery = DeliveryList::findOrFail($id);
        $transactions = Transaction::pluck('no_transaction');
        $kurirs = User::where('role', 'kurir')->get();
        return view('kurir.edit', compact('delivery', 'transactions', 'kurirs'));
    }

    public function update(Request $request, $id)
    {
        try {
            $delivery = DeliveryList::findOrFail($id);
            $data = $request->validate([
                'no_transaction' => 'required|exists:transactions,no_transaction',
                'kurir_id' => 'required|exists:users,id',
                'tanggal_diantar' => 'required|date',
                'tanggal_terkirim' => 'required|date',
                'bukti_terima' => 'nullable|file|mimes:jpg,png,jpeg|max:2048'
            ]);

            if ($request->hasFile('bukti_terima')) {
                if ($delivery->bukti_terima) {
                    Storage::disk('public')->delete($delivery->bukti_terima);
                }
                $data['bukti_terima'] = $request->file('bukti_terima')->store('bukti', 'public');
            }

            $delivery->update($data);
            return redirect()->route('kurir.pengantaran.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function destroy($id)
    {
        try {
            $delivery = DeliveryList::findOrFail($id);
            if ($delivery->bukti_terima) {
                Storage::disk('public')->delete($delivery->bukti_terima);
            }
            $delivery->delete();
            return redirect()->route('kurir.pengantaran.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $e) {
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
