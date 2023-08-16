<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preferences', function (Blueprint $table) {
            $table->string('email_logo_width')->default(200);
            $table->string('email_logo_height')->default(100);
        });
    }

    public function down(): void
    {
        Schema::table('preferences', function (Blueprint $table) {
            $table->dropColumn('email_logo_width', 'email_logo_height');
        });
    }
};
