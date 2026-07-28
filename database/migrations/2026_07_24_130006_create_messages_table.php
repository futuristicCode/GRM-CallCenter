<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reclamation_id');
            $table->foreign('reclamation_id')->references('id')->on('reclamations')->cascadeOnDelete();
            $table->foreignId('expediteur_id')->constrained('users');
            $table->boolean('est_interne')->default(false);
            $table->text('contenu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
