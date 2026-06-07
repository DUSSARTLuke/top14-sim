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
    $table->string('name');
    $table->string('position'); // 9,10,15 etc.
    $table->integer('attack');
    $table->integer('defense');
    $table->integer('stamina');
    $table->foreignId('team_id')->nullable()->constrained();
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
