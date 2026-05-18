@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')

<nav>
    <a href="#" class="logo">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" height="35" style="margin-right:10px;"> 
        DATA<span style="color:var(--primary);">VAIL</span>
    </a>
    <div class="nav-menu">
    <a href="{{ route('catalogue.public') }}">Catalogue</a>
    <a href="#contact">Contact</a>
<a href="{{ route('login') }}?mode=signup" class="btn-nav">
    Demander un compte
</a>
    <a href="{{ route('login') }}" class="btn-nav">Connexion</a>
</div>
</nav>

<header class="hero">
    <video autoplay muted loop class="video-bg">
        <source src="{{ asset('img/hero-bg.mp4') }}" type="video/mp4">
    </video>
    <div class="hero-content">
        <h1>
            L'Infrastructure de <span style="color:var(--primary);">Demain</span>,<br>
            Disponible <span style="color:var(--secondary);">Aujourd'hui</span>.
        </h1>
        <p>Gérez, réservez et optimisez les ressources du Data Center Universitaire.<br>Une plateforme centralisée pour étudiants et chercheurs.</p>
        <div style="margin-top: 30px;">
            <a href="#catalogue" class="btn-hero">Voir le Catalogue</a>
        </div>
    </div>
</header>

<section class="stats-section">
    <div class="stat-item">
        <span class="stat-number">99.9%</span>
        <span class="stat-label">Disponibilité</span>
    </div>
    <div class="stat-item">
        <span class="stat-number">+50</span>
        <span class="stat-label">Ressources</span>
    </div>
    <div class="stat-item">
        <span class="stat-number">24/7</span>
        <span class="stat-label">Monitoring</span>
    </div>
</section>

<section id="catalogue" class="catalogue-section">
    <h2 class="section-title"><span>Nos Ressources</span></h2>

    <div class="marquee-container">
        <div class="marquee-content">
            {{-- Première boucle pour le contenu --}}
            @foreach($allResources as $resource)
            <div class="resource-card">
                <div class="card-img-top" style="height: 150px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #1a1f35;">
                    @if($resource->image)
                        {{-- Affichage de la vraie photo si elle existe --}}
                        <img src="{{ asset('storage/' . $resource->image) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $resource->name }}">
                    @else
                        {{-- Icônes de secours basées sur la catégorie --}}
                        @php
                            $cat = strtolower($resource->category_name ?? '');
                            $icon = 'network-icon.png';
                            if(str_contains($cat, 'server')) $icon = 'server-icon.png';
                            elseif(str_contains($cat, 'vm')) $icon = 'vm-icon.png';
                            elseif(str_contains($cat, 'stockage')) $icon = 'storage-icon.png';
                        @endphp
                        <img src="{{ asset('img/' . $icon) }}" width="60" style="opacity: 0.5;">
                    @endif
                </div>
                <div class="card-body">
                    <span class="cat-badge">{{ $resource->category_name }}</span>
                    <div class="card-title">{{ $resource->name }}</div>
                    @if($resource->status == 'available')
                        <div class="status-available">● Disponible</div>
                    @else
                        <div class="status-maintenance">● Indisponible</div>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Deuxième boucle identique pour l'effet de défilement infini --}}
            @foreach($allResources as $resource)
            <div class="resource-card">
                <div class="card-img-top" style="height: 150px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #1a1f35;">
                    @if($resource->image)
                        <img src="{{ asset('storage/' . $resource->image) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $resource->name }}">
                    @else
                        @php
                            $cat = strtolower($resource->category_name ?? '');
                            $icon = 'network-icon.png';
                            if(str_contains($cat, 'server')) $icon = 'server-icon.png';
                            elseif(str_contains($cat, 'vm')) $icon = 'vm-icon.png';
                            elseif(str_contains($cat, 'stockage')) $icon = 'storage-icon.png';
                        @endphp
                        <img src="{{ asset('img/' . $icon) }}" width="60" style="opacity: 0.5;">
                    @endif
                </div>
                <div class="card-body">
                    <span class="cat-badge">{{ $resource->category_name }}</span>
                    <div class="card-title">{{ $resource->name }}</div>
                    @if($resource->status == 'available')
                        <div class="status-available">● Disponible</div>
                    @else
                        <div class="status-maintenance">● Indisponible</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<footer id="contact">
    <div class="footer-content">
        <div class="footer-col">
            <h3>À propos de DataVail</h3>
            <p>
                DataVail est la plateforme de référence pour la gestion et l'optimisation des infrastructures informatiques de notre université. 
                Nous offrons un accès simplifié à des ressources de haute performance pour soutenir l'innovation académique et les projets complexes.
            </p>
            <p>
                Notre mission est de fournir aux étudiants et chercheurs un environnement technologique stable, sécurisé et disponible 24/7. 
                Grâce à une surveillance constante, nous garantissons la pérennité de vos travaux pratiques et de vos recherches scientifiques.
            </p>
        </div>

        <div class="footer-col footer-links">
            <h3>Liens Rapides</h3>
            <a href="{{ route('catalogue.public') }}">Catalogue des ressources</a>
            <a href="{{ route('login') }}">Connexion / Inscription</a>
            <a href="{{ asset('uploads/reglement.pdf') }}" target="_blank">Règlement intérieur</a>
        </div>

        <div class="footer-col">
            <h3>Contact Support</h3>
            <p class="contact-item">
                <img src="{{ asset('img/icon-location.png') }}" width="20">
                <span>Tanger, Campus Universitaire</span>
            </p>
            <p class="contact-item">
                <img src="{{ asset('img/icon-email.png') }}" width="20">
                <span>support@datavail.univ</span>
            </p>
            <p class="contact-item">
                <img src="{{ asset('img/icon-phone.png') }}" width="20">
                <span>+212 5 39 00 00 00</span>
            </p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2026 DataVail System. Tous droits réservés.
    </div>
</footer>

@endsection