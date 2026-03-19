<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['cod', 'vnpay', 'momo']);
            $table->decimal('amount', 15, 0);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['card', 'ewallet']);
            $table->string('provider', 50);
            $table->string('display_name', 100);
            $table->string('masked_number', 50)->nullable();
            $table->string('card_holder')->nullable();
            $table->string('expiry', 10)->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payments');
    }
};
