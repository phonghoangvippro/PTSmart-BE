<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 20)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->constrained();
            $table->decimal('subtotal', 15, 0);
            $table->decimal('discount', 15, 0)->default(0);
            $table->decimal('shipping_fee', 15, 0)->default(0);
            $table->decimal('total', 15, 0);
            $table->enum('status', ['pending', 'confirmed', 'shipping', 'completed', 'canceled'])->default('pending');
            $table->string('payment_method', 50);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('product_name');
            $table->decimal('price', 15, 0);
            $table->integer('quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
