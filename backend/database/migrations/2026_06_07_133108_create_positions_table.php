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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // ex: "Pilier"
            $table->string('code');           // ex: "PR" (prop), "HO" (hooker)
            $table->integer('number');        // numéro réglementaire 1-15
            $table->enum('zone', ['forward', 'back']); // avants / arrières

            // Pondération des stats pour la simulation (total = 100)
            $table->integer('weight_attack')->default(25);
            $table->integer('weight_defense')->default(25);
            $table->integer('weight_stamina')->default(25);
            $table->integer('weight_speed')->default(25);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
