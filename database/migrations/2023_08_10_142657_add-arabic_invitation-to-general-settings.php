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
        Schema::table('access_level_general_settings', function (Blueprint $table) {
            $table->boolean('arabic_invitation')->default(false)->after('invitation_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('access_level_general_settings', function (Blueprint $table) {
            $table->dropColumn('arabic_invitation');
        });
    }
};
