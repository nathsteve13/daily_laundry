<?php

namespace App\Http\Controllers;

use App\Models\DeliveryList;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
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
}
