<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sous_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('types_reclamation')->cascadeOnDelete();
            $table->string('libelle');
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sous_types');
    }
};
