<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            // Lien avec l'utilisateur qui signale
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Lien avec la ressource en panne
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            
            $table->text('description');
            // Statuts : Ouvert, En cours, Résolu
            $table->string('status')->default('Ouvert'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};