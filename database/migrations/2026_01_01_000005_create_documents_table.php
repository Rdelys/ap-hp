<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['audio_source', 'transcription', 'document_final']);

            $table->string('nom_fichier');
            $table->string('chemin_stockage'); // jamais accessible publiquement — cf. disk 'documents_prives'
            $table->string('format')->nullable();
            $table->unsignedBigInteger('taille_octets')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->boolean('chiffre')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};