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
        Schema::create('ussd_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->integer('case_no');
            $table->integer('step_no');
            $table->string('session_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ussd_sessions');
    }
};
