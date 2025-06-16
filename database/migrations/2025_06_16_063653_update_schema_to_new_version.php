<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'kasir', 'kurir', 'all'])->after('password');
            });

            DB::statement("ALTER TABLE transaction_status MODIFY COLUMN status ENUM('pending', 'pickup', 'proccessed', 'ready', 'delivered', 'done') NOT NULL");

            Schema::create('order_requests', function (Blueprint $table) {
                $table->string('no_order', 45)->primary();
                $table->string('name', 45);
                $table->enum('status', ['diterima', 'selesai']);
                $table->string('address', 45);
                $table->string('phone_number', 45);
                $table->double('estimated_value');
                $table->unsignedInteger('service_type_id');
                $table->timestamps();

                $table->foreign('service_type_id')->references('id')->on('service_type')->onUpdate('no action')->onDelete('no action');
            });

            Schema::create('delivery_lists', function (Blueprint $table) {
                $table->integer('no_delivery');
                $table->string('no_transaction', 45);
                $table->unsignedInteger('kurir_id');
                $table->timestamp('tanggal_diantar');
                $table->timestamp('tanggal_terkirim');
                $table->string('bukti_terima', 45);
                $table->timestamps();

                $table->primary(['no_delivery', 'no_transaction']);
                $table->foreign('no_transaction')->references('no_transaction')->on('transactions')->onUpdate('no action')->onDelete('no action');
                $table->foreign('kurir_id')->references('id')->on('users')->onUpdate('no action')->onDelete('no action');
            });

            Schema::create('pickup_lists', function (Blueprint $table) {
                $table->integer('no_pickup')->primary();
                $table->string('no_transaction', 45);
                $table->unsignedInteger('kurir_id');
                $table->timestamp('tanggal_pengambilan');
                $table->timestamp('tanggal_diambil');
                $table->string('bukti_pengambilan', 45);
                $table->timestamps();

                $table->foreign('no_transaction')->references('no_transaction')->on('transactions')->onUpdate('no action')->onDelete('no action');
                $table->foreign('kurir_id')->references('id')->on('users')->onUpdate('no action')->onDelete('no action');
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    public function down(): void
    {
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });

            DB::statement("ALTER TABLE transaction_status MODIFY COLUMN status ENUM('pending', 'proccessed', 'ready', 'done') NOT NULL");

            Schema::dropIfExists('order_requests');
            Schema::dropIfExists('delivery_lists');
            Schema::dropIfExists('pickup_lists');
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }
};
