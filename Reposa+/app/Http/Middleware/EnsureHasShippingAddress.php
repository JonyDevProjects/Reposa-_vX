<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasShippingAddress
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Si es un usuario de Google que aún no ha completado su dirección de envío
            if ($user->google_id && $user->role !== 'admin' && ! $user->addresses()->exists()) {
                // Rutas exentas para evitar bucles de redirección
                if (! $request->routeIs('onboarding.*') && ! $request->is('logout') && ! $request->routeIs('lang.switch')) {
                    return redirect()->route('onboarding.shipping')
                        ->with('warning', __('messages.auth.onboarding_required_notice'));
                }
            }
        }

        return $next($request);
    }
}
