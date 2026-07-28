<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_statuts', function (Blueprint $table) {
            $table->id();
            $table->uuid('reclamation_id');
            $table->foreign('reclamation_id')->references('id')->on('reclamations')->cascadeOnDelete();
            $table->string('ancien_statut')->nullable();
            $table->string('nouveau_statut');
            $table->foreignId('utilisateur_id')->constrained('users');
            $table->text('commentaire')->nullable();
            $table->timestamp('date_changement')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_statuts');
    }
};
