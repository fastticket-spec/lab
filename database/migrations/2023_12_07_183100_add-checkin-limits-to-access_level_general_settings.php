<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_level_general_settings', function (Blueprint $table) {
            $table->string('checkin_limit')->nullable();
            $table->string('checkout_limit')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('access_level_general_settings', function (Blueprint $table) {
            $table->dropColumn('checkin_limit');
            $table->dropColumn('checkout_limit');
        });
    }
};
