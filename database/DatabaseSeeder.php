<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Resource;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CRÉATION DES UTILISATEURS
        
        // Admin (TOI)
        User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@datavail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Responsable
        User::create([
            'name' => 'Sami Tech',
            'email' => 'tech@datavail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tech',
            'status' => 'active',
        ]);

        // Étudiant
        User::create([
            'name' => 'Karim Etudiant',
            'email' => 'etudiant@datavail.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
            'status' => 'active',
        ]);

        // 2. CRÉATION DES 7 RESSOURCES
        
        // --- 4 DISPONIBLES ---
        Resource::create([
            'name' => 'Dell PowerEdge R740',
            'category' => 'Serveurs',
            'description' => 'Serveur IA',
            'cpu' => 'Xeon Gold', 'ram' => '128 Go',
            'status' => 'available'
        ]);
        Resource::create([
            'name' => 'MacBook Pro M3',
            'category' => 'Poste de travail',
            'description' => 'PC Dev',
            'cpu' => 'M3 Max', 'ram' => '32 Go',
            'status' => 'available'
        ]);
        Resource::create([
            'name' => 'Switch Cisco 9200',
            'category' => 'Réseau',
            'description' => 'Switch Batiment A',
            'cpu' => 'N/A', 'ram' => 'N/A',
            'status' => 'available'
        ]);
        Resource::create([
            'name' => 'HP EliteDesk',
            'category' => 'Poste de travail',
            'description' => 'PC Labo 2',
            'cpu' => 'i7', 'ram' => '16 Go',
            'status' => 'available'
        ]);

        // --- 1 MAINTENANCE ---
        Resource::create([
            'name' => 'Routeur Principal',
            'category' => 'Réseau',
            'description' => 'En panne alim',
            'cpu' => 'Quad Core', 'ram' => '8 Go',
            'status' => 'maintenance',
            'maintenance_end' => Carbon::now()->addDays(5)
        ]);

        // --- 2 RÉSERVÉS ---
        Resource::create([
            'name' => 'VM DeepLearning',
            'category' => 'VM',
            'description' => 'Projet Fin Etude',
            'cpu' => 'vCPU x8', 'ram' => '32 Go',
            'status' => 'reserved'
        ]);
        Resource::create([
            'name' => 'Baie NetApp',
            'category' => 'Stockage',
            'description' => 'Backup Mensuel',
            'cpu' => 'Dual Ctrl', 'ram' => '64 Go',
            'status' => 'reserved'
        ]);
    }
}