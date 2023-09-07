<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendee_check_ins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attendee_id')->constrained('attendees')->cascadeOnDelete();
            $table->dateTime('checkin')->default(now());
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendee_check_ins');
    }
};
