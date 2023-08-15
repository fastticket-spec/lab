<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organiser_id')->constrained('organisers')->cascadeOnDelete();
            $table->string('email_bg_color')->default('#ffffff');
            $table->string('email_font_color')->default('#000000');
            $table->string('email_qr_color')->default('#000000');
            $table->string('email_logo_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferences');
    }
};
