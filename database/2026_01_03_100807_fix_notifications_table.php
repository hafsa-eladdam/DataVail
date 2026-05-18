<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // On vérifie chaque colonne individuellement avant de l'ajouter
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->after('user_id');
            }
            
            // Si 'message' existe déjà, Laravel l'ignorera grâce à cette condition
            if (!Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->after('type');
            }

            if (!Schema::hasColumn('notifications', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_read']);
            // On ne drop pas 'message' si il était déjà là avant
        });
    }
};