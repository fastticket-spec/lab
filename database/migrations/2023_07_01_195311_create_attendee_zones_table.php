<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendee_zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attendee_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('zone_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendee_zones');
    }
};
