<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('secretariat_medical')->after('email');

            $table->foreignId('etablissement_id')->nullable()
                ->after('role')->constrained()->nullOnDelete();

            $table->foreignId('service_id')->nullable()
                ->after('etablissement_id')->constrained()->nullOnDelete();

            $table->boolean('actif')->default(true)->after('service_id');
            $table->timestamp('derniere_connexion_at')->nullable()->after('actif');

            // Durcissement sécurité — verrouillage après tentatives échouées
            $table->unsignedTinyInteger('tentatives_echouees')->default(0)->after('derniere_connexion_at');
            $table->timestamp('verrouille_jusqu_a')->nullable()->after('tentatives_echouees');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['etablissement_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn([
                'role', 'etablissement_id', 'service_id', 'actif',
                'derniere_connexion_at', 'tentatives_echouees', 'verrouille_jusqu_a',
            ]);
        });
    }
};