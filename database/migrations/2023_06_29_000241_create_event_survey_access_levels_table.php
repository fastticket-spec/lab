<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_survey_access_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_survey_id')->constrained('event_surveys')->cascadeOnDelete();
            $table->foreignUuid('access_level_id')->constrained('access_levels')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_survey_access_levels');
    }
};
