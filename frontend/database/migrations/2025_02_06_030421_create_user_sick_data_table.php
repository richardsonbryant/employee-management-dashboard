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
        Schema::create('user_sick_data', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('new_name');
            $table->date('start_off_date');
            $table->date('end_off_date');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('total_off_day');
            $table->string('reason');
            $table->string('permission_letter');
            $table->string('request_type');

            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->rememberToken();
            $table->timestamps();

            $table->foreign('email')->references('email')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sick_data');
    }
};
