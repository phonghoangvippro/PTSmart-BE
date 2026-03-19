<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 15, 0);
            $table->decimal('sale_price', 15, 0)->nullable();
            $table->integer('stock')->default(0);
            $table->string('thumbnail', 500)->nullable();
            $table->decimal('rating_avg', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('sold_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->index(['category_id', 'status']);
            $table->index(['brand_id', 'status']);
            $table->index(['status', 'price']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
