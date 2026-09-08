<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajout du statut 'en_validation' (dossier relu, en attente de validation humaine — Sprint 5)
        DB::statement("ALTER TABLE demandes MODIFY statut ENUM(
            'depose','en_file','en_transcription','en_relecture',
            'en_validation','valide','restitue','hors_delai','renvoye_correction'
        ) DEFAULT 'depose'");

        Schema::table('demandes', function (Blueprint $table) {
            $table->foreignId('relecteur_id')->nullable()->after('operateur_id')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('relu_le')->nullable()->after('relecteur_id');
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropForeign(['relecteur_id']);
            $table->dropColumn(['relecteur_id', 'relu_le']);
        });

        DB::statement("ALTER TABLE demandes MODIFY statut ENUM(
            'depose','en_file','en_transcription','en_relecture',
            'valide','restitue','hors_delai','renvoye_correction'
        ) DEFAULT 'depose'");
    }
};