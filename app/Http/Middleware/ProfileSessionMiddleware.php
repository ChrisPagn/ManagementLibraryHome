<?php
// app/Http/Middleware/ProfileSessionMiddleware.php

namespace App\Http\Middleware;

use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;

class ProfileSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Si pas de profil en session → retour à la sélection
        if (! session()->has('active_profile_id')) {
            // Sauf si on est déjà sur les pages de sélection/PIN
            if (! $request->routeIs('famille.index', 'famille.pin.*')) {
                return redirect()->route('famille.index');
            }
        }

        // Injecte le profil actif dans toutes les vues
        if (session()->has('active_profile_id')) {
            $profile = Profile::find(session('active_profile_id'));
            view()->share('activeProfile', $profile);
        }

        return $next($request);
    }
}