<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            // Lien avec l'étudiant qui reçoit la notification
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Contenu de la notification
            $table->string('title');
            $table->text('message');
            
            // État de lecture (pour afficher le petit point rouge)
            $table->boolean('is_read')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};