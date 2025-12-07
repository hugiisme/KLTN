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
        Schema::create('submission_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean("require_file_upload")->default(false);
            $table->boolean("require_text_input")->default(false);
            $table->string('allowed_file_types')->nullable()->comment('Danh sách các định dạng file được phép tải lên, cách nhau bằng dấu phẩy. Ví dụ: "pdf,docx,jpg"');
            $table->integer('max_file_size_mb')->nullable()->comment('Kích thước tối đa cho mỗi file tải lên, tính bằng MB.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_requirements');
    }
};
