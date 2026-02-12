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
        Schema::create('order_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['product_id1']);
            $table->dropForeign(['product_id2']);
            $table->dropForeign(['product_id3']);
            $table->dropColumn([
                'product_id1',
                'num1',
                'product_id2',
                'num2',
                'product_id3',
                'num3',
            ]);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('product_id1')->constrained('products');
            $table->integer('num1');
            $table->foreignId('product_id2')->nullable()->constrained('products');
            $table->integer('num2')->nullable();
            $table->foreignId('product_id3')->nullable()->constrained('products');
            $table->integer('num3')->nullable();
        });
        Schema::dropIfExists('order_product_table_and_modify_orders');
    }
};
