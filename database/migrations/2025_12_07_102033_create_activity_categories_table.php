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
        Schema::create('activity_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_multi_level')->default(false)->comment('Đánh dấu hoạt động có thể áp dụng cho nhiều cấp (trường, liên chi đoàn, chi đoàn). Thường TRUE với hoạt động Đoàn, FALSE với NCKH và NVSP.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_categories');
    }
};
