@extends('layouts.dashboard')

@section('content')

{{-- 1. SECTION NOTIFICATIONS (Exigence Prof) --}}
@php
    $userNotifications = \App\Models\Notification::where('user_id', Auth::id())
        ->where('is_read', false)
        ->latest()
        ->get();
@endphp

@if($userNotifications->count() > 0)
<div style="margin-bottom: 30px;">
    <h3 style="color: var(--primary); margin-bottom: 15px;">🔔 Nouvelles Notifications</h3>
    @foreach($userNotifications as $notif)
        <div style="background: rgba(103, 255, 242, 0.05); border: 1px solid var(--primary); padding: 15px; border-radius: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong style="color: #fff; display: block;">{{ $notif->title }}</strong>
                <span style="color: #aaa; font-size: 0.9rem;">{{ $notif->message }}</span>
            </div>
            <small style="color: #666;">{{ $notif->created_at->diffForHumans() }}</small>
        </div>
    @endforeach
</div>
@endif

{{-- 2. SECTION STATISTIQUES --}}
@php
    $res = $myBookings ?? 0;
    $dispo = $availableResources ?? 0;
    $total = $totalResources ?? 0;
    $taux = $occupancyRate ?? 0;

    // Calcul des largeurs (en pourcentage)
    $w_res = ($res > 0) ? min(($res / 5) * 100, 100) : 0;
    $w_dispo = ($total > 0) ? ($dispo / $total) * 100 : 0;
    
    // Détermination de la couleur du taux
    $bg_taux = ($taux > 80) ? '#ff6b6b' : '#f1c40f';
@endphp

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
    
    <div class="stat-card">
        <h3>Mes Réservations</h3>
        <div class="stat-value" style="font-size: 2rem; color: #fff; margin-bottom: 10px;">{{ $res }}</div>
        
        <div class="progress-bar" style="height: 8px; background: #1a1f35; border-radius: 10px; overflow: hidden;">
            <div style="height: 100%; background: var(--primary); width: <?php echo $w_res; ?>%;"></div>
        </div>
        <p style="font-size:0.8rem; margin-top:10px; color:#aaa;">Demandes en cours</p>
    </div>

    <div class="stat-card">
        <h3>Ressources Disponibles</h3>
        <div class="stat-value" style="font-size: 2rem; color: #fff; margin-bottom: 10px;">
            {{ $dispo }} <span style="font-size:1rem; color:#aaa;">/ {{ $total }}</span>
        </div>
        
        <div class="progress-bar" style="height: 8px; background: #1a1f35; border-radius: 10px; overflow: hidden;">
            <div style="height: 100%; background: var(--success); width: <?php echo $w_dispo; ?>%;"></div>
        </div>
        <p style="font-size:0.8rem; margin-top:10px; color:#aaa;">Prêtes à l'emploi</p>
    </div>

    <div class="stat-card">
        <h3>Occupation Data Center</h3>
        <div class="stat-value" style="font-size: 2rem; color: #fff; margin-bottom: 10px;">{{ $taux }}%</div>
        
        <div class="progress-bar" style="height: 8px; background: #1a1f35; border-radius: 10px; overflow: hidden;">
            <div style="height: 100%; background: <?php echo $bg_taux; ?>; width: <?php echo $taux; ?>%;"></div>
        </div>
        <p style="font-size:0.8rem; margin-top:10px; color:#aaa;">Charge actuelle du parc</p>
    </div>

</div>

<div class="stat-card" style="margin-top: 30px;">
    <h3 style="font-size: 1.1rem; color:#fff; margin-bottom:15px;">Dernières Activités</h3>
    <p style="color:#aaa; font-size: 0.9rem;">
        Bienvenue sur votre espace personnel. Vous pouvez consulter l'état de vos ressources et effectuer de nouvelles demandes via le menu latéral.
    </p>
</div>

@endsection