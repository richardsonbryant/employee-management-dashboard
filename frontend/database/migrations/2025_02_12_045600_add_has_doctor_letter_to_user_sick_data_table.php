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
        Schema::table('user_sick_data', function (Blueprint $table) {
            $table->boolean('has_doctor_letter')->default(false); // Menambahkan kolom baru
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_sick_data', function (Blueprint $table) {
            $table->dropColumn('has_doctor_letter');
        });
    }
};
