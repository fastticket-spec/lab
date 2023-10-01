<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_level_general_settings', function (Blueprint $table) {
            $table->boolean('decline_invitation')->default(false)->after('invitation_message_sms');
            $table->text('decline_text')->after('invitation_message_sms');
        });
    }

    public function down(): void
    {
        Schema::table('access_level_general_settings', function (Blueprint $table) {
            $table->dropColumn('decline_invitation');
            $table->dropColumn('decline_text');
        });
    }
};
