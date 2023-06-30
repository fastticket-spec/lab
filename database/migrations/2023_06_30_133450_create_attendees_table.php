<?php

use App\Models\Attendee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('access_level_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('organiser_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->string('ref');
            $table->string('email');
            $table->json('answers')->nullable();
            $table->tinyInteger('status')->default(Attendee::STATUS['PENDING']);
            $table->tinyInteger('accept_status')->default(Attendee::ACCEPT_STATUS['NOT_ACCEPTED']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
};
