<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_level_general_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('access_level_id')->constrained('access_levels')->cascadeOnDelete();
            $table->string("visibility");
            $table->string("accept_reject");
            $table->string("waiting_list");
            $table->string("send_tc");
            $table->longText("description");
            $table->longText("description_arabic")->nullable();
            $table->longText("success_message");
            $table->longText("success_message_arabic")->nullable();
            $table->string("approval_message_title")->nullable();
            $table->longText("approval_message")->nullable();
            $table->string("email_message_title")->nullable();
            $table->longText("email_message")->nullable();
            $table->longText("email_message_arabic")->nullable();
            $table->dateTime("start_on")->nullable();
            $table->dateTime("end_on")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_level_general_settings');
    }
};
