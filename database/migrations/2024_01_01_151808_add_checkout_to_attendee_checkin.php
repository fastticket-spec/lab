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
        Schema::table('attendee_check_ins', function (Blueprint $table) {
            $table->dateTime('checkout')->after('checkin')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('attendee_check_ins', function (Blueprint $table) {
            $table->dropColumn('checkout');
        });
    }
};
