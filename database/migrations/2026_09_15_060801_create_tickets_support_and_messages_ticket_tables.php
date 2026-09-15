<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets_support', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demandeur_id')->constrained('users');
            $table->foreignId('demande_id')->nullable()->constrained('demandes')->nullOnDelete();

            $table->enum('categorie', ['fichier_audio', 'document', 'acces_plateforme', 'compte', 'delai', 'incident_securite', 'autre']);
            $table->enum('criticite', ['normale', 'elevee', 'critique'])->default('normale');
            $table->string('sujet');
            $table->text('description');

            $table->enum('statut', ['ouvert', 'en_cours', 'resolu', 'ferme'])->default('ouvert');

            $table->foreignId('assigne_a_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolu_le')->nullable();

            $table->timestamps();
        });

        Schema::create('messages_ticket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets_support')->cascadeOnDelete();
            $table->foreignId('auteur_id')->constrained('users');
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages_ticket');
        Schema::dropIfExists('tickets_support');
    }
};