<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_level_general_settings', function (Blueprint $table) {
            $table->boolean('share_link')->default(false);
            $table->text('selected_socials')->nullable();
            $table->text('social_share_message')->nullable();
            $table->text('social_share_message_arabic')->nullable();
            $table->string('link_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('access_level_general_settings', function (Blueprint $table) {
            $table->dropColumn('share_link');
            $table->dropColumn('selected_socials');
            $table->dropColumn('social_share_message');
            $table->dropColumn('social_share_message_arabic');
            $table->dropColumn('link_address');
        });
    }
};
