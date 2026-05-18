@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <style>
        /* Couleur de fond identique à ton accueil */
        body, .public-wrapper {
            background-color: #1e2340 !important;
            color: white;
            min-height: 100vh;
        }

        .container-public { padding: 40px 60px; }

        /* Bouton Retour */
        .top-navigation { margin-bottom: 30px; }
        .back-btn { 
            color: var(--primary); 
            text-decoration: none; 
            font-weight: bold; 
            display: flex; 
            align-items: center; 
            gap: 10px;
            font-size: 0.9rem;
        }

        /* Barre de recherche (Design exact photo) */
        .search-container-neon { text-align: center; margin-bottom: 50px; }
        .search-group {
            display: inline-flex;
            background: #070912;
            border-radius: 50px;
            padding: 8px;
            width: 100%;
            max-width: 600px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .search-group input {
            flex: 1; background: transparent; border: none; color: white;
            padding-left: 20px; outline: none; font-size: 1rem;
        }
        .btn-neon-find {
            background: var(--primary); color: #000; border: none;
            padding: 12px 30px; border-radius: 50px; font-weight: 800; cursor: pointer;
        }

        /* SECTION FILTRAGE (Indispensable) */
        .filters-wrapper {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }
        .category-pill {
            background: rgba(255,255,255,0.05);
            color: white;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 10px 22px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .category-pill:hover, .category-pill.active {
            background: var(--primary);
            color: #000;
            border-color: var(--primary);
        }

        /* GRILLE ET CARTES (Design exact photo) */
        .resources-display {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }
        .res-card-neon {
            background: #161b30;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.05);
            overflow: hidden;
            transition: 0.3s;
        }
        .res-card-neon:hover { transform: translateY(-8px); border-color: var(--primary); }
        
        .res-card-header { height: 190px; width: 100%; background: #1a1f35; }
        .res-card-header img { width: 100%; height: 100%; object-fit: cover; }

        .res-card-body { padding: 25px; }
        .res-card-body .cat-name { color: var(--primary); font-size: 0.7rem; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; }
        .res-card-body .title { font-size: 1.3rem; font-weight: bold; margin-bottom: 15px; }
        
        .res-specs { display: flex; gap: 15px; color: #8a8f9d; font-size: 0.8rem; margin-bottom: 20px; }

        /* Statuts */
        .status-badge {
            display: inline-block; padding: 5px 12px; border-radius: 4px; font-size: 0.65rem; font-weight: bold; text-transform: uppercase;
        }
        .status-available { color: #2ecc71; background: rgba(46, 204, 113, 0.1); border: 1px solid #2ecc71; }
        .status-occupied { color: #f1c40f; background: rgba(241, 196, 15, 0.1); border: 1px solid #f1c40f; }

        /* Footer Carte */
        .res-card-footer {
            padding: 15px 25px;
            border-top: 1px solid rgba(255,255,255,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-reserver-action {
            background: var(--primary); color: #000; padding: 8px 18px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.8rem;
        }
    </style>
@endpush

@section('content')
<div class="public-wrapper">
    <div class="container-public">
        
        <div class="top-navigation">
            <a href="{{ route('home') }}" class="back-btn">← Retour à l'accueil</a>
        </div>

        <div style="background: rgba(103, 255, 242, 0.03); border-left: 4px solid var(--primary); padding: 25px; border-radius: 8px; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <h3 style="color: var(--primary); margin-top: 0; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">
                📜 Règles d'utilisation du Data Center
            </h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                <ul style="color: #aab; font-size: 0.85rem; line-height: 1.6; margin: 0; padding-left: 20px;">
                    <li><strong>Consultation :</strong> Libre et gratuite pour tous les visiteurs.</li>
                    <li><strong>Réservation :</strong> Réservée aux membres authentifiés et approuvés.</li>
                </ul>
                <ul style="color: #aab; font-size: 0.85rem; line-height: 1.6; margin: 0; padding-left: 20px;">
                    <li><strong>Responsabilité :</strong> Usage strictement pédagogique et de recherche.</li>
                    <li><strong>Sécurité :</strong> Tout comportement suspect entraîne la révocation du compte.</li>
                </ul>
            </div>
        </div>

        <div class="search-container-neon">
            <form action="{{ route('catalogue.public') }}" method="GET" class="search-group">
                <input type="text" name="search" placeholder="Rechercher (ex: Dell, Serveur...)" value="{{ request('search') }}">
                <button type="submit" class="btn-neon-find">Chercher</button>
            </form>
        </div>

        {{-- FILTRAGE PAR CATÉGORIE --}}
        <div class="filters-wrapper">
            <a href="{{ route('catalogue.public') }}" class="category-pill {{ !request('category') ? 'active' : '' }}">Tout voir</a>
            @foreach($categories as $cat)
                <a href="?category={{ $cat }}" class="category-pill {{ request('category') == $cat ? 'active' : '' }}">
                    {{-- On peut ajouter des icônes selon le nom --}}
                    @if(str_contains(strtolower($cat), 'serveur')) 🖥️ 
                    @elseif(str_contains(strtolower($cat), 'cloud')) ☁️ 
                    @elseif(str_contains(strtolower($cat), 'stockage')) 🗄️ 
                    @endif
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        <div class="resources-display">
            @foreach($resources as $res)
            <div class="res-card-neon">
                <div class="res-card-header">
                    @if($res->image)
                        <img src="{{ asset('storage/' . $res->image) }}" alt="{{ $res->name }}">
                    @else
                        <div style="height:100%; display:flex; align-items:center; justify-content:center; background:#1a1f35;">🖥️</div>
                    @endif
                </div>

                <div class="res-card-body">
                    <div class="cat-name">{{ $res->category }}</div>
                    <div class="title">{{ $res->name }}</div>
                    
                    <div class="res-specs">
                        <span>📟 CPU: {{ $res->cpu ?? 'N/A' }}</span>
                        <span>💾 RAM: {{ $res->ram ?? 'N/A' }}</span>
                    </div>

                    <div class="status-badge {{ $res->status == 'available' ? 'status-available' : 'status-occupied' }}">
                        ● {{ $res->status == 'available' ? 'Disponible' : 'Occupé' }}
                    </div>
                </div>

                <div class="res-card-footer">
                    <span style="font-size: 0.75rem; color: #666;">📍 {{ $res->location ?? 'Site A' }}</span>
                    
                    {{-- Redirige vers login pour forcer l'authentification --}}
                    <a href="{{ route('login') }}" class="btn-reserver-action">Réserver</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection