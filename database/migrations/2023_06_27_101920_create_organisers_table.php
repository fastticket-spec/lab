<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('account_id')
                ->constrained('accounts')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('name_arabic')->nullable();
            $table->longText('about')->nullable();
            $table->longText('about_arabic')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('snapchat')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('organiser_logo')->nullable();
            $table->string('organiser_logo_arabic')->nullable();
            $table->string('banner')->nullable();
            $table->string('banner_arabic')->nullable();
            $table->boolean('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisers');
    }
};
