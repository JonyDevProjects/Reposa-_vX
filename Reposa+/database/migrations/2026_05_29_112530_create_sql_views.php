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
        DB::statement("DROP VIEW IF EXISTS v_order_summary");
        DB::statement("
            CREATE VIEW v_order_summary AS
            SELECT
                u.id AS user_id,
                u.name AS user_name,
                u.email AS user_email,
                COUNT(o.id) AS total_orders,
                COALESCE(SUM(o.total_amount), 0) AS total_spent
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            GROUP BY u.id, u.name, u.email
        ");

        DB::statement("DROP VIEW IF EXISTS v_top_favorited_products");
        DB::statement("
            CREATE VIEW v_top_favorited_products AS
            SELECT
                p.id AS id,
                p.name AS name,
                p.price AS price,
                COUNT(fp.user_id) AS favorited_by_count
            FROM products p
            INNER JOIN favorite_product fp ON p.id = fp.product_id
            GROUP BY p.id, p.name, p.price
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
