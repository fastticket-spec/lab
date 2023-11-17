<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendee_check_ins', function (Blueprint $table) {
            $table->string('checkin_user_id')->nullable()->after('checkin');
        });
    }

    public function down(): void
    {
        Schema::table('attendee_check_ins', function (Blueprint $table) {
            $table->dropColumn('checkin_user_id');
        });
    }
};
