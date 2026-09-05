<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('tracking_number')->unique();
            $table->string('carrier')->default('Correos Express');
            $table->string('service_type')->default('standard_48h');
            $table->string('service_name')->default('Reposa+ Estándar (48-72h)');
            $table->string('status')->default('pre_registered');
            $table->decimal('shipping_cost', 8, 2)->default(0.00);
            $table->string('recipient_name');
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('street');
            $table->string('city');
            $table->string('zip_code');
            $table->string('province')->nullable();
            $table->string('country')->default('ES');
            $table->date('estimated_delivery_date')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('tracking_history')->nullable();
            $table->json('label_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
