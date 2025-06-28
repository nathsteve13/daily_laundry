<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_request_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_request_id');
            $table->unsignedBigInteger('service_type_id');
            $table->double('estimated_value');
            $table->timestamps();

            $table->foreign('order_request_id')->references('id')->on('order_requests')->onDelete('cascade');
            $table->foreign('service_type_id')->references('id')->on('service_type')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_request_details');
    }
};
