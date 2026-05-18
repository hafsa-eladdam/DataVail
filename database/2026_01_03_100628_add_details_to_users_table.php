<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajout des colonnes manquantes signalées par l'erreur
            $table->string('cne')->nullable()->after('email');
            $table->string('role_request')->nullable()->after('cne');
            $table->text('justification')->nullable()->after('role_request');
            $table->string('id_card')->nullable()->after('justification');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cne', 'role_request', 'justification', 'id_card']);
        });
    }
};