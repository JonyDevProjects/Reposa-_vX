<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Regularizar pedidos entregados o completados
        DB::statement("
            UPDATE shipments s 
            JOIN orders o ON s.order_id = o.id 
            SET s.status = 'delivered', 
                s.delivered_at = COALESCE(s.delivered_at, o.updated_at, NOW()), 
                s.shipped_at = COALESCE(s.shipped_at, o.created_at, NOW()) 
            WHERE o.status IN ('delivered', 'completed') AND s.status != 'delivered'
        ");

        // 2. Regularizar pedidos enviados con expedición pre-admitida
        DB::statement("
            UPDATE shipments s 
            JOIN orders o ON s.order_id = o.id 
            SET s.status = 'in_transit', 
                s.shipped_at = COALESCE(s.shipped_at, o.updated_at, NOW()) 
            WHERE o.status = 'shipped' AND s.status = 'pre_registered'
        ");

        // 3. Regularizar pedidos cancelados
        DB::statement("
            UPDATE shipments s 
            JOIN orders o ON s.order_id = o.id 
            SET s.status = 'cancelled' 
            WHERE o.status = 'cancelled' AND s.status != 'cancelled'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructivo
    }
};
