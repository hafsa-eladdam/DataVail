<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. On vérifie si les tables existent pour éviter les crashs si la BDD est vide
        // Si tu n'as pas encore créé la table 'bookings', on met 0 par défaut.
        
        try {
            $totalResources = DB::table('resources')->count();
            $availableResources = DB::table('resources')->where('status', 'available')->count();
            
            // On vérifie si la table bookings existe avant d'essayer de compter
            $myBookings = 0;
            if(DB::getSchemaBuilder()->hasTable('bookings')) {
                $myBookings = DB::table('bookings')->where('user_id', Auth::id())->count();
            }

        } catch (\Exception $e) {
            // En cas d'erreur (tables inexistantes), on met tout à zéro pour que la page s'affiche quand même
            $totalResources = 0;
            $availableResources = 0;
            $myBookings = 0;
        }

        // 2. Calcul simple du taux (évite la division par zéro)
        $occupancyRate = $totalResources > 0 ? round((($totalResources - $availableResources) / $totalResources) * 100) : 0;

        // 3. IMPORTANT : C'est ici qu'on envoie les variables à la vue pour corriger tes 9 erreurs
        return view('dashboard', compact('totalResources', 'myBookings', 'availableResources', 'occupancyRate'));
    }
}