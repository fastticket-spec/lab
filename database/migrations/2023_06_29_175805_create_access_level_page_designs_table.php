<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_level_page_designs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('access_level_id')->constrained('access_levels')->cascadeOnDelete();
            $table->string('btn_color_code')->nullable();
            $table->string('btn_font_color_code')->nullable();
            $table->string('register_btn_value')->nullable();
            $table->string('register_btn_value_ar')->nullable();
            $table->string('bg_color')->nullable();
            $table->string('bg_type')->nullable();
            $table->string('bg_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_level_page_designs');
    }
};
