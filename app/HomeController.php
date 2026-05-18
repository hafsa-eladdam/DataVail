<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource; 
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $featuredResources = Resource::orderBy('created_at', 'desc')->take(3)->get();
        $categories = Resource::select('category')->distinct()->get();
        $allResources = Resource::all();

        return view('welcome', compact('featuredResources', 'categories', 'allResources'));
    }

    public function publicCatalogue(Request $request)
    {
        // On commence par filtrer les ressources qui ne sont pas désactivées
        $query = Resource::where('status', '!=', 'disabled');

        // --- NOUVEAU : LOGIQUE DE RECHERCHE PAR MOT-CLÉ ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('cpu', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par catégorie (existant)
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $resources = $query->get();
        $categories = Resource::distinct()->pluck('category');

        return view('catalogue_public', compact('resources', 'categories'));
    }
}