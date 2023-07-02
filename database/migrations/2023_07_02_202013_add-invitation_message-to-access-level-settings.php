<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_level_general_settings', function (Blueprint $table) {
            $table->longText('invitation_message')->nullable()->after('email_message_arabic');
            $table->string('invitation_title')->nullable()->after('email_message_arabic');
        });
    }

    public function down(): void
    {
        Schema::table('access_level_general_settings', function (Blueprint $table) {
            $table->dropColumn('invitation_message');
            $table->dropColumn('invitation_title');
        });
    }
};
