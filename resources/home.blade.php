@extends('layouts.app')

@section('content')
<div class="landing-container" style="background: #0f1221; color: white; min-height: 100vh; padding: 20px;">
    
    {{-- Barre de recherche et Filtres (Style de ton screenshot) --}}
    <div style="text-align: center; margin-bottom: 50px; padding-top: 30px;">
        <div style="display: inline-flex; background: #1a1f35; padding: 10px; border-radius: 50px; width: 60%; max-width: 600px;">
            <input type="text" placeholder="Rechercher (ex: Serveur, Dell...)" style="flex: 1; background: transparent; border: none; color: white; padding: 10px 20px; outline: none;">
            <button style="background: #4deeea; color: black; border: none; padding: 10px 25px; border-radius: 50px; font-weight: bold; cursor: pointer;">Chercher</button>
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <button class="filter-btn active">Tout voir</button>
            @foreach($categories as $cat)
                <button class="filter-btn">{{ $cat }}</button>
            @endforeach
        </div>
    </div>

    {{-- Grille des Ressources (Style de ton screenshot) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; padding: 0 50px;">
        @foreach($resources as $res)
        <div class="res-card" style="background: #161b30; border-radius: 15px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <div style="height: 180px; position: relative;">
                @if($res->image)
                    <img src="{{ asset('storage/' . $res->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <div style="width: 100%; height: 100%; background: #1a1f35; display: flex; align-items: center; justify-content: center; font-size: 3rem;">🖥️</div>
                @endif
            </div>

            <div style="padding: 20px;">
                <span style="color: #4deeea; font-size: 0.7rem; text-transform: uppercase; font-weight: bold;">{{ $res->category }}</span>
                <h3 style="margin: 5px 0; font-size: 1.2rem;">{{ $res->name }}</h3>
                <p style="color: #aaa; font-size: 0.8rem; margin-bottom: 15px;">{{ Str::limit($res->description, 60) }}</p>
                
                <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <span style="background: rgba(77, 238, 234, 0.1); color: #4deeea; padding: 5px 10px; border-radius: 5px; font-size: 0.7rem;">
                        {{ $res->status }}
                    </span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.7rem; color: #666;">📍 {{ $res->location ?? 'Baie A - Rack 4' }}</span>
                    
                    {{-- LOGIQUE INVITÉ : Redirige vers login s'il n'est pas connecté --}}
                    @guest
                        <a href="{{ route('login') }}" style="background: #4deeea; color: #000; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 0.8rem;">Réserver</a>
                    @else
                        <a href="{{ route('user.catalogue') }}" style="background: #4deeea; color: #000; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 0.8rem;">Réserver</a>
                    @endguest
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .filter-btn {
        background: rgba(255,255,255,0.05);
        color: white;
        border: 1px solid rgba(255,255,255,0.1);
        padding: 8px 20px;
        border-radius: 50px;
        cursor: pointer;
        transition: 0.3s;
        font-size: 0.8rem;
    }
    .filter-btn.active, .filter-btn:hover {
        background: #4deeea;
        color: black;
        border-color: #4deeea;
    }
</style>
@endsection