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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('current_word', 5);
            $table->unsignedSmallInteger('round')->default(1);
            $table->foreignId('player1_id')->constrained('users');
            $table->foreignId('player2_id')->constrained('users');
            $table->unsignedSmallInteger('player1_score')->default(0);
            $table->unsignedSmallInteger('player2_score')->default(0);
            $table->foreignId('current_turn_user_id')->constrained('users');
            $table->foreignId('round_first_user_id')->constrained('users');
            $table->enum('status', ['in_progress', 'finished'])->default('in_progress');
            $table->foreignId('winner_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
