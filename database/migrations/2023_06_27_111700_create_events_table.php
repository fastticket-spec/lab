<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('title_arabic')->nullable();
            $table->foreignUuid('organiser_id')->constrained('organisers');
            $table->longText('description');
            $table->longText('description_arabic')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('status')->default(\App\Models\Event::EVENT_STATUS['ACTIVE']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
