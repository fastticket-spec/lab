<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges_areas', function (Blueprint $table) {
            $table->id();
            $table->string('event_id');
            $table->string('badge_id');
            $table->string('area_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges_areas');
    }
};
