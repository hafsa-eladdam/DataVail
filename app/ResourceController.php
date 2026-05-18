<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Liste des ressources pour Admin et Technicien
     */
    public function index()
    {
        $resources = Resource::all();
        return view('admin.resources.index', compact('resources'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('admin.resources.create');
    }

    /**
     * AJOUTÉ : Formulaire de modification (INDISPENSABLE pour le bouton Modifier)
     * Permet d'ouvrir la vue avec les données actuelles de la ressource.
     */
    public function edit($id)
    {
        $resource = Resource::findOrFail($id);
        return view('admin.resources.edit', compact('resource'));
    }

    /**
     * Enregistrement d'une nouvelle ressource (Admin/Tech)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('resources', 'public');
        }

        Resource::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'cpu' => $request->specs, // Utilise le champ specs du formulaire vers cpu
            'location' => $request->location,
            'image' => $imagePath,
            'status' => $request->status
        ]);

        return redirect()->route('admin.resources.index')->with('success', 'Ressource ajoutée avec succès.');
    }

    /**
     * MISE À JOUR (Temps réel/Live) + NOTIFICATIONS MAINTENANCE
     */
    public function update(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);
        
        $resource->update([
            'name' => $request->name,
            'category' => $request->category,
            'cpu' => $request->cpu,
            'description' => $request->description,
            'location' => $request->location,
            'status' => $request->status 
        ]);

        // Gestion de l'image si elle est modifiée
        if ($request->hasFile('image')) {
            if ($resource->image) {
                Storage::disk('public')->delete($resource->image);
            }
            $resource->image = $request->file('image')->store('resources', 'public');
            $resource->save();
        }

        // SI MISE EN MAINTENANCE : Notifier automatiquement les utilisateurs impactés
        if ($request->status === 'maintenance') {
            $impactedUsers = Reservation::where('resource_id', $id)
                ->whereIn('status', ['Approuvée', 'approved', 'Active'])
                ->pluck('user_id');

            foreach ($impactedUsers as $userId) {
                Notification::create([
                    'user_id' => $userId,
                    'title' => 'Alerte Maintenance',
                    'message' => "Attention : La ressource " . $resource->name . " que vous avez réservée vient d'être mise en maintenance.",
                    'is_read' => false
                ]);
            }
        }

        return redirect()->route('admin.resources.index')->with('success', 'Ressource mise à jour en temps réel.');
    }

    /**
     * GESTION DE LA MAINTENANCE (Vue dédiée)
     */
    public function maintenance()
    {
        $resources = Resource::where('status', 'maintenance')->get();
        $pendingUsers = User::where('status', 'pending')->get();

        return view('admin.maintenance', compact('resources', 'pendingUsers'));
    }

    /**
     * RÉACTIVATION RAPIDE
     */
    public function activate($id) {
        $res = Resource::findOrFail($id);
        $res->status = 'available';
        $res->save();
        return back()->with('success', 'Ressource remise en service !');
    }

    /**
     * ACTIVATION / DÉSACTIVATION GLOBALE
     */
    public function toggleStatus($id)
    {
        $resource = Resource::findOrFail($id);
        
        if($resource->status == 'disabled') {
            $resource->status = 'available';
            $msg = "La ressource est de nouveau active.";
        } else {
            $resource->status = 'disabled';
            $msg = "La ressource a été désactivée (masquée).";
        }
        
        $resource->save();
        return back()->with('success', $msg);
    }

    /**
     * SUPPRESSION
     */
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);
        if ($resource->image) {
            Storage::disk('public')->delete($resource->image);
        }
        $resource->delete();
        return back()->with('success', 'Ressource supprimée définitivement.');
    }
}