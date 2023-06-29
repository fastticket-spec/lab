<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_design_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('design_image');
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('organiser_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_design_images');
    }
};
