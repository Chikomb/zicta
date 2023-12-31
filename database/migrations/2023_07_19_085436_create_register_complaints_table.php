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
        Schema::create('register_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number')->unique();
            $table->text('description');
            $table->string('session_id');
            $table->string('status')->default('Pending'); // Set a default value for the status column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_complaints');
    }
};
