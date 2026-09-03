<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();

            $table->foreignId('etablissement_id')->constrained();
            $table->foreignId('service_id')->constrained();
            $table->foreignId('demandeur_id')->constrained('users');

            $table->string('type_document');
            $table->string('nom_demandeur')->nullable();
            $table->string('numero_dictant')->nullable();

            $table->enum('niveau_urgence', ['economique', 'normal', 'urgent'])->default('normal');

            $table->enum('statut', [
                'depose', 'en_file', 'en_transcription', 'en_relecture',
                'valide', 'restitue', 'hors_delai', 'renvoye_correction',
            ])->default('depose');

            $table->string('mode_production')->default('manuel');

            $table->timestamp('date_depot');
            $table->timestamp('echeance_sla')->nullable();
            $table->timestamp('date_restitution')->nullable();

            $table->text('signalement_anomalie')->nullable();

            $table->timestamps();

            $table->index(['statut', 'echeance_sla']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};