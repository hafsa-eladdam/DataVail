<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\CustomAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| 1. ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| 2. AUTHENTIFICATION (Login, Register, Logout)
|--------------------------------------------------------------------------
*/
Route::get('/auth', [CustomAuthController::class, 'showLoginRegister'])->name('login');
Route::post('/auth/register', [CustomAuthController::class, 'register'])->name('register.submit');
Route::post('/auth/login', [CustomAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 3. ESPACE UTILISATEUR INTERNE (Étudiant / Enseignant / Doctorant)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Dashboard personnel avec Statistiques et Notifications
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Catalogue avec Filtres (Exigence Prof)
    Route::get('/catalogue', [ReservationController::class, 'index'])->name('user.catalogue');
    
    // Système de Réservation (Dates + Justification + Conflits)
    Route::post('/reserve', [ReservationController::class, 'store'])->name('user.reserve');
    Route::get('/mes-reservations', [ReservationController::class, 'myReservations'])->name('user.reservations');

    // SIGNALEMENT D'INCIDENT (Nouveau : Exigence Prof n°2)
    Route::post('/incident/report', [ReservationController::class, 'storeIncident'])->name('user.incident.store');
});

/*
|--------------------------------------------------------------------------
| 4. ESPACE GESTION (Admin + Responsable Technique)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // --- Dashboard Global & Statistiques d'Occupation ---
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // --- Gestion des Utilisateurs, Rôles et Permissions ---
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/user/{id}/approve', [AdminController::class, 'approveUser'])->name('admin.approve');
    Route::post('/user/{id}/reject', [AdminController::class, 'rejectUser'])->name('admin.reject');
    Route::post('/user/{id}/role', [AdminController::class, 'updateUserRole'])->name('admin.user.role');
    Route::post('/user/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('admin.user.toggle');

    // --- Gestion du Catalogue de Ressources ---
    Route::get('/resources', [ResourceController::class, 'index'])->name('admin.resources.index');
    Route::get('/resources/create', [ResourceController::class, 'create'])->name('admin.resources.create');
    Route::post('/resources', [ResourceController::class, 'store'])->name('admin.resources.store');
    
    // --- ROUTES AJOUTÉES POUR CORRIGER L'ERREUR DE MODIFICATION ---
    Route::get('/resources/{id}/edit', [ResourceController::class, 'edit'])->name('admin.resources.edit');
    Route::put('/resources/{id}', [ResourceController::class, 'update'])->name('admin.resources.update');
    // --------------------------------------------------------------

    Route::delete('/resources/{id}', [ResourceController::class, 'destroy'])->name('admin.resources.delete');
    
    // Activation / Désactivation / Maintenance Planifiée
    Route::post('/resources/{id}/toggle', [ResourceController::class, 'toggleStatus'])->name('admin.resources.toggle');
    Route::post('/resources/{id}/activate', [ResourceController::class, 'activate'])->name('admin.resources.activate');
    Route::get('/maintenance', [ResourceController::class, 'maintenance'])->name('admin.maintenance');

    // --- Gestion des Réservations (Validation & Suivi détaillé) ---
    Route::get('/reservations-manage', [AdminController::class, 'reservationsIndex'])->name('admin.reservations.index');
    Route::post('/reservations/{id}/update', [AdminController::class, 'updateReservationStatus'])->name('admin.reservations.update');
    
    // Nouvelles routes pour approuver/rejeter les réservations (pour les notifications)
    Route::post('/reservations/{id}/approve', [ReservationController::class, 'approve'])->name('admin.reservations.approve');
    Route::post('/reservations/{id}/reject', [ReservationController::class, 'reject'])->name('admin.reservations.reject');

    // --- Modération des Alertes & Incidents (Nouveau : Exigence Prof n°3) ---
    Route::get('/incidents-manage', [AdminController::class, 'incidentsIndex'])->name('admin.incidents.index');
    Route::post('/incidents/{id}/resolve', [AdminController::class, 'resolveIncident'])->name('admin.incidents.resolve');
});

// Cette route est accessible à tout le monde (invités)
Route::get('/catalogue-public', [App\Http\Controllers\HomeController::class, 'publicCatalogue'])->name('catalogue.public');