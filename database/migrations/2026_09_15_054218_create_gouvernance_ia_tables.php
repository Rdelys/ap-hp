<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Historique des décisions de gouvernance IA — traçabilité exigée par le CCTP
        Schema::create('decisions_gouvernance_ia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type_document')->nullable(); // null = s'applique à tout le service
            $table->boolean('ia_autorisee');
            $table->text('motif')->nullable();
            $table->foreignId('decide_par_id')->constrained('users');
            $table->timestamps();
        });

        // Restrictions actives par type de document (vide = pas de restriction spécifique, on suit le réglage global du service)
        Schema::create('restrictions_ia_par_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('type_document');
            $table->boolean('ia_autorisee')->default(true);
            $table->timestamps();

            $table->unique(['service_id', 'type_document']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restrictions_ia_par_type');
        Schema::dropIfExists('decisions_gouvernance_ia');
    }
};