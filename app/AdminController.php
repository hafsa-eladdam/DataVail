<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // PAGE 1 : VUE D'ENSEMBLE
    public function index()
{
    // 1. Statistiques des Utilisateurs
    $totalUsers = \App\Models\User::count();
    $pendingUsers = \App\Models\User::where('status', 'pending')->get();
    $pendingCount = $pendingUsers->count();

    // 2. Statistiques des Ressources
    $totalResources = \App\Models\Resource::count();
    
    // On considère une ressource "occupée" si elle a une réservation Approuvée ou Active aujourd'hui
    $occupiedCount = \App\Models\Reservation::whereIn('status', ['Approuvée', 'Active'])
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->distinct('resource_id')
        ->count('resource_id');

    // Calcul du taux d'occupation (éviter la division par zéro)
    $occupancyRate = $totalResources > 0 ? round(($occupiedCount / $totalResources) * 100) : 0;

    // 3. Dernières activités pour la traçabilité
    $lastResources = \App\Models\Resource::orderBy('updated_at', 'desc')->take(5)->get();
    $lastReservations = \App\Models\Reservation::with(['user', 'resource'])->latest()->take(5)->get();

    return view('admin.dashboard', compact(
        'totalUsers', 
        'pendingCount', 
        'pendingUsers', 
        'totalResources', 
        'occupancyRate', 
        'occupiedCount',
        'lastResources',
        'lastReservations'
    ));
}

    // PAGE 2 : GÉRER UTILISATEURS
    public function users()
    {
        // La liste pour le tableau
        $activeUsers = User::where('status', 'active')
                           ->orWhere('status', 'banned')
                           ->where('id', '!=', Auth::id())
                           ->get();

        // >>> LA CORRECTION EST ICI <<<
        // On DOIT envoyer pendingUsers sinon le menu plante
        $pendingUsers = User::where('status', 'pending')->orderBy('created_at', 'desc')->get();

        return view('admin.users', compact('activeUsers', 'pendingUsers'));
    }

    // --- ACTIONS ---

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();
        return redirect()->back()->with('success', "Compte activé.");
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('error', "Demande refusée.");
    }

    public function updateUserRole(Request $request, $id)
    {
        $request->validate(['role' => 'required']);
        $user = User::findOrFail($id);
        if($user->id == Auth::id()) return back()->with('error', 'Action interdite.');
        
        $user->role = $request->role;
        $user->save();
        return back()->with('success', "Rôle mis à jour.");
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        if($user->id == Auth::id()) return back()->with('error', 'Action interdite.');

        $user->status = ($user->status == 'banned') ? 'active' : 'banned';
        $user->save();
        return back()->with('success', "Statut mis à jour.");
    }

    public function reservationsIndex() 
{
    $reservations = \App\Models\Reservation::with(['user', 'resource'])->latest()->get();
    $pendingUsers = \App\Models\User::where('status', 'pending')->get();
    
    return view('admin.reservations.index', compact('reservations', 'pendingUsers'));
}

public function updateReservationStatus(Request $request, $id) 
{
    $reser = \App\Models\Reservation::findOrFail($id);
    $reser->update([
        'status' => $request->status,
        'admin_comment' => $request->admin_comment
    ]);
    
    return back()->with('success', 'La réservation a été ' . $request->status);
}

public function incidentsIndex() 
{
    // Récupère les incidents avec les infos user et resource
    $incidents = \App\Models\Incident::with(['user', 'resource'])->latest()->get();
    return view('admin.incidents.index', compact('incidents'));
}

public function resolveIncident($id) 
{
    $incident = \App\Models\Incident::findOrFail($id);
    $incident->update(['status' => 'Résolu']);
    
    return back()->with('success', 'L\'incident a été marqué comme résolu.');
}
}