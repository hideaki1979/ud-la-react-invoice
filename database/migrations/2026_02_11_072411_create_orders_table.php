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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('product_id1')->constrained('products');
            $table->integer('num1');
            $table->foreignId('product_id2')->nullable()->constrained('products');
            $table->integer('num2')->nullable();
            $table->foreignId('product_id3')->nullable()->constrained('products');
            $table->integer('num3')->nullable();
            $table->datetime('orderday');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
