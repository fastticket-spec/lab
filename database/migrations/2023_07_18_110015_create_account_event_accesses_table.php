<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_event_accesses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id');
            $table->uuid('event_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_event_accesses');
    }
};
