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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('guest_token', 64)->nullable()->unique()->after('user_id');
            $table->string('shipping_name')->nullable()->after('guest_token');
            $table->string('shipping_email')->nullable()->after('shipping_name');
            $table->string('shipping_phone')->nullable()->after('shipping_email');
            $table->string('shipping_street')->nullable()->after('shipping_phone');
            $table->string('shipping_city')->nullable()->after('shipping_street');
            $table->string('shipping_zip_code')->nullable()->after('shipping_city');
            $table->string('shipping_province')->nullable()->after('shipping_zip_code');
            $table->string('shipping_country')->default('ES')->after('shipping_province');
            $table->string('shipping_service_type')->default('standard_48h')->after('shipping_country');
            $table->decimal('shipping_cost', 8, 2)->default(0.00)->after('shipping_service_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'guest_token',
                'shipping_name',
                'shipping_email',
                'shipping_phone',
                'shipping_street',
                'shipping_city',
                'shipping_zip_code',
                'shipping_province',
                'shipping_country',
                'shipping_service_type',
                'shipping_cost',
            ]);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
