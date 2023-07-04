<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_level_socials', function (Blueprint $table) {
            $table->string('twitter')->nullable()->after('instagram');
            $table->string('linkedin')->nullable()->after('instagram');
            $table->string('youtube')->nullable()->after('instagram');
        });
    }

    public function down(): void
    {
        Schema::table('access_level_socials', function (Blueprint $table) {
            $table->dropColumn('twitter');
            $table->dropColumn('linkedin');
            $table->dropColumn('youtube');
        });
    }
};
