<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar', 500)->nullable()->after('phone');
            $table->date('birthday')->nullable()->after('avatar');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birthday');
            $table->string('city', 100)->nullable()->after('gender');
            $table->enum('role', ['admin', 'customer'])->default('customer')->after('city');
            $table->string('membership', 50)->nullable()->after('role');
            $table->tinyInteger('status')->default(1)->after('membership');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'avatar', 'birthday', 'gender', 'city', 'role', 'membership', 'status']);
        });
    }
};
