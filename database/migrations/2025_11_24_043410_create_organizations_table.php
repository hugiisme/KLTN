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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_org_id')->nullable()->constrained('organizations')->onDelete('set null');
            $table->foreignId('org_type_id')->constrained('org_types')->onDelete('cascade');
            $table->foreignId('org_level_id')->constrained('org_levels')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['name', 'parent_org_id'], 'unique_name_parent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
