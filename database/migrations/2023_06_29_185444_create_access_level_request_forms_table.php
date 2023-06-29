<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('access_level_request_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('access_level_id')->constrained()->cascadeOnDelete();
            $table->text('message_before')->nullable();
            $table->text('message_before_arabic')->nullable();
            $table->text('message_after')->nullable();
            $table->text('message_after_arabic')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_level_request_forms');
    }
};
