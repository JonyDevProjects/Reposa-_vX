<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert existing string values to JSON format first
        $products = DB::table('products')->select('id', 'name', 'description', 'material', 'firmness', 'dimensions')->get();
        foreach ($products as $product) {
            DB::table('products')->where('id', $product->id)->update([
                'name' => json_encode(['es' => $product->name]),
                'description' => $product->description ? json_encode(['es' => $product->description]) : null,
                'material' => $product->material ? json_encode(['es' => $product->material]) : null,
                'firmness' => $product->firmness ? json_encode(['es' => $product->firmness]) : null,
                'dimensions' => $product->dimensions ? json_encode(['es' => $product->dimensions]) : null,
            ]);
        }

        $categories = DB::table('categories')->select('id', 'name')->get();
        foreach ($categories as $category) {
            DB::table('categories')->where('id', $category->id)->update([
                'name' => json_encode(['es' => $category->name]),
            ]);
        }

        // Now change column types to JSON
        Schema::table('products', function (Blueprint $table) {
            $table->json('name')->change();
            $table->json('description')->nullable()->change();
            $table->json('material')->nullable()->change();
            $table->json('firmness')->nullable()->change();
            $table->json('dimensions')->nullable()->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->json('name')->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->change();
            $table->text('description')->nullable()->change();
            $table->string('material')->nullable()->change();
            $table->string('firmness')->nullable()->change();
            $table->string('dimensions')->nullable()->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('name')->change();
        });
    }
};
