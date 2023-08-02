<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('access_level_page_designs', function (Blueprint $table) {
            $table->string('field_color')->nullable();
            $table->string('font_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('access_level_page_designs', function (Blueprint $table) {
            $table->dropColumn('field_color');
            $table->dropColumn('font_color');
        });
    }
};
