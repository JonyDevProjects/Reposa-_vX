<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vista para resumen de pedidos
        DB::statement("DROP VIEW IF EXISTS v_order_summary");
        DB::statement("
            CREATE VIEW v_order_summary AS
            SELECT 
                u.id as user_id, 
                u.name as user_name, 
                COUNT(o.id) as total_orders, 
                SUM(o.total_amount) as total_spent
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            GROUP BY u.id, u.name;
        ");

        // Vista para productos más deseados
        DB::statement("DROP VIEW IF EXISTS v_top_favorited_products");
        DB::statement("
            CREATE VIEW v_top_favorited_products AS
            SELECT 
                p.id as product_id, 
                p.name as product_name, 
                COUNT(fp.user_id) as favorites_count
            FROM products p
            LEFT JOIN favorite_product fp ON p.id = fp.product_id
            GROUP BY p.id, p.name
            ORDER BY favorites_count DESC;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_order_summary");
        DB::statement("DROP VIEW IF EXISTS v_top_favorited_products");
    }
};
