<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. IMPORTANT : On désactive les contraintes de clé étrangère
        // Cela permet de supprimer la table 'users' même si 'bookings' est liée à elle.
        Schema::disableForeignKeyConstraints();

        // 2. On supprime la table si elle existe
        Schema::dropIfExists('users');

        // 3. On recrée la table proprement avec les colonnes du Prof
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            
            // --- COLONNES OBLIGATOIRES POUR LE CAHIER DES CHARGES ---
            $table->string('role')->default('guest'); // 'admin', 'tech', 'user', 'guest'
            $table->string('status')->default('pending'); // 'active', 'pending', 'banned'
            // --------------------------------------------------------

            $table->rememberToken();
            $table->timestamps();
        });

        // 4. On réactive les contraintes de sécurité
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
};