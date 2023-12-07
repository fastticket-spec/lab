<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendee_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('attendee_id')->constrained('attendees')->cascadeOnDelete();
            $table->dateTime('scan')->default(now());
            $table->string('scan_user_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendee_scans');
    }
};
