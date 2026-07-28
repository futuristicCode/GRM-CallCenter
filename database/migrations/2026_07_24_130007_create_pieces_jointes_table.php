<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pieces_jointes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reclamation_id');
            $table->foreign('reclamation_id')->references('id')->on('reclamations')->cascadeOnDelete();
            $table->string('nom_fichier');
            $table->string('chemin_stockage');
            $table->integer('taille_octets');
            $table->string('type_mime');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_jointes');
    }
};
