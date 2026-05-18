<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Reservation;
use App\Models\Incident; 
use App\Models\Notification;
use App\Models\User; // Import ajouté pour trouver le technicien
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Affiche le catalogue avec filtres
     */
    public function index(Request $request)
    {
        $query = Resource::where('status', '!=', 'disabled');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $resources = $query->get();
        $categories = Resource::distinct()->pluck('category');

        return view('user.catalogue', compact('resources', 'categories'));
    }

    /**
     * Enregistre la demande de réservation + NOTIFICATION TECH
     */
    public function store(Request $request)
    {
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'justification' => 'required|min:5',
        ]);

        // Vérification automatique des disponibilités (Exigence Prof)
        $conflict = Reservation::where('resource_id', $request->resource_id)
            ->whereIn('status', ['En attente', 'Approuvée', 'Active'])
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
            })->exists();

        if ($conflict) {
            return back()->with('error', 'Désolé, cette ressource est déjà réservée pour cette période.');
        }

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'resource_id' => $request->resource_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'justification' => $request->justification,
            'status' => 'En attente'
        ]);

        // --- NOTIFICATION AUTOMATIQUE POUR LE TECHNICIEN ---
        $technicien = User::where('role', 'tech')->first();
        if ($technicien) {
            Notification::create([
                'user_id' => $technicien->id,
                'title' => 'Nouvelle Demande',
                'message' => Auth::user()->name . " a demandé la ressource : " . $reservation->resource->name,
                'is_read' => false
            ]);
        }

        return redirect()->route('user.reservations')->with('success', 'Votre demande de réservation a été envoyée !');
    }

    /**
     * Liste des réservations de l'étudiant
     */
    public function myReservations()
    {
        $reservations = Reservation::where('user_id', Auth::id())
            ->with('resource')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $pendingUsers = User::where('status', 'pending')->get();

        return view('user.reservations', compact('reservations', 'pendingUsers'));
    }

    /**
     * SIGNALER UN INCIDENT (Exigence Prof n°2)
     */
    public function storeIncident(Request $request) 
    {
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'description' => 'required|min:10'
        ]);

        Incident::create([
            'user_id' => Auth::id(),
            'resource_id' => $request->resource_id,
            'description' => $request->description,
            'status' => 'Ouvert'
        ]);

        return back()->with('success', 'Votre signalement a été transmis aux responsables techniques.');
    }

    /**
     * APPROUVER UNE RÉSERVATION (Action Technicien) + NOTIFICATION UTILISATEUR
     */
    public function approve($id) 
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->status = 'Approuvée'; 
        $reservation->save();

        // --- NOTIFICATION AUTOMATIQUE POUR L'UTILISATEUR ---
        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Réservation Approuvée',
            'message' => "Votre demande pour la ressource " . $reservation->resource->name . " a été acceptée.",
            'is_read' => false,
        ]);

        return back()->with('success', 'La réservation a été approuvée et l\'utilisateur a été notifié.');
    }

    /**
     * REFUSER UNE RÉSERVATION (Action Technicien) + NOTIFICATION UTILISATEUR
     */
    public function reject($id) 
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->status = 'Refusée'; 
        $reservation->save();

        // --- NOTIFICATION AUTOMATIQUE POUR L'UTILISATEUR ---
        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Réservation Refusée',
            'message' => "Désolé, votre demande pour " . $reservation->resource->name . " a été refusée.",
            'is_read' => false,
        ]);

        return back()->with('success', 'La réservation a été refusée et l\'utilisateur a été notifié.');
    }
}