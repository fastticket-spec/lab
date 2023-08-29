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
        Schema::table('preferences', function (Blueprint $table) {
            $table->string('email_footer_image_url')->nullable()->after('email_logo_url');
            $table->string('email_header_image_url')->nullable()->after('email_logo_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preferences', function (Blueprint $table) {
            $table->dropColumn('email_header_image_url');
            $table->dropColumn('email_footer_image_url');
        });
    }
};
