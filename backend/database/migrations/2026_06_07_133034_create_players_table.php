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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('real_player_id')->nullable()->index();
            $table->string('name');
            $table->integer('number')->nullable();
            $table->integer('attack')->default(50);
            $table->integer('defense')->default(50);
            $table->integer('stamina')->default(50);
            $table->integer('speed')->default(50);
            $table->integer('rating')->default(50);
            $table->foreignId('team_id')->nullable()->constrained();
            $table->foreignId('season_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
