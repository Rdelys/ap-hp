<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('referent_nom')->nullable();
            $table->string('referent_email')->nullable();
            $table->string('referent_telephone')->nullable();
            $table->string('modele_documentaire_path')->nullable();
            $table->string('regle_nommage')->nullable();
            $table->boolean('ia_autorisee')->default(true);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};