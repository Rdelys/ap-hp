<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->foreignId('valide_par_id')->nullable()->after('relu_le')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le')->nullable()->after('valide_par_id');
            $table->boolean('verrouille')->default(false)->after('valide_le');
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropForeign(['valide_par_id']);
            $table->dropColumn(['valide_par_id', 'valide_le', 'verrouille']);
        });
    }
};