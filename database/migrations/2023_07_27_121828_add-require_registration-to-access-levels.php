<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_levels', function (Blueprint $table) {
            $table->boolean('registration')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('access_levels', function (Blueprint $table) {
            $table->dropColumn('registration');
        });
    }
};
