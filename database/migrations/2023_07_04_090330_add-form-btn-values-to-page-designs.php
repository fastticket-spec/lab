<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_level_page_designs', function (Blueprint $table) {
            $table->string('form_btn_value_ar')->nullable()->after('register_btn_value_ar');
            $table->string('form_btn_value')->nullable()->after('register_btn_value_ar');
        });
    }

    public function down(): void
    {
        Schema::table('access_level_page_designs', function (Blueprint $table) {
            $table->dropColumn('form_btn_value');
            $table->dropColumn('form_btn_value_ar');
        });
    }
};
