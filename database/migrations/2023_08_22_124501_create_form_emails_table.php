<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_emails', function (Blueprint $table) {
            $table->id();
            $table->string('organiser_id');
            $table->string('event_id');
            $table->string('access_level_id');
            $table->string('email')->unique();
            $table->integer('severity')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_emails');
    }
};
