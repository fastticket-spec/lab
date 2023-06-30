<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_surveys', function (Blueprint $table) {
            $table->boolean('status')->after('event_id')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('event_surveys', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
