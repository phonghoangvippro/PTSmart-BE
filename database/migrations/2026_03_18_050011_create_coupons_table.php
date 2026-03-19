<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['fixed', 'percent', 'shipping']);
            $table->decimal('discount_value', 15, 0);
            $table->decimal('min_order', 15, 0)->default(0);
            $table->decimal('max_discount', 15, 0)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->timestamp('expired_at');
            $table->string('category', 50)->default('shopping');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'coupon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_coupons');
        Schema::dropIfExists('coupons');
    }
};
