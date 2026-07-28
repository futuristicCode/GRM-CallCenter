<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->unique();
            $table->uuid('client_id');
            $table->foreignId('type_id')->constrained('types_reclamation');
            $table->foreignId('sous_type_id')->nullable()->constrained('sous_types')->nullOnDelete();
            $table->string('sujet');
            $table->text('description');
            $table->string('priorite')->default('moyenne');
            $table->string('reference_externe')->nullable();
            $table->string('statut')->default('en_attente')->index();
            $table->foreignId('assigne_a')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motif_rejet')->nullable();
            $table->timestamp('date_creation')->index();
            $table->timestamp('date_derniere_modification')->nullable();
            $table->timestamp('date_cloture')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamations');
    }
};
