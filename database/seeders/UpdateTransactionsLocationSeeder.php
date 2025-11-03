<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

class UpdateTransactionsLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all kecamatan with their kelurahan
        $kecamatans = Kecamatan::with('kelurahan')->get();

        // Get all transactions without location data
        $transactions = Transaction::whereNull('kecamatan_id')
            ->orWhereNull('kelurahan_id')
            ->get();

        echo "Found {$transactions->count()} transactions without location data\n";

        foreach ($transactions as $transaction) {
            // Pick random kecamatan
            $randomKecamatan = $kecamatans->random();

            // Pick random kelurahan from that kecamatan
            $randomKelurahan = $randomKecamatan->kelurahan->random();

            // Update transaction
            $transaction->update([
                'kecamatan_id' => $randomKecamatan->id,
                'kelurahan_id' => $randomKelurahan->id,
            ]);

            echo "Updated {$transaction->no_transaction} with {$randomKecamatan->name} - {$randomKelurahan->name}\n";
        }

        echo "\nAll transactions updated successfully!\n";
    }
}
