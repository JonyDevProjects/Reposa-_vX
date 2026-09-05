<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Muestra el formulario de captura obligatoria de dirección de entrega.
     */
    public function showShippingForm(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // Si ya completó la dirección, redirigir al catálogo o destino
        if ($user->addresses()->exists()) {
            return redirect()->route('catalog');
        }

        return view('auth.onboarding-shipping', compact('user'));
    }

    /**
     * Guarda la dirección de entrega obligatoria y actualiza el teléfono del usuario.
     */
    public function storeShipping(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'zip_code' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:30'],
            'province' => ['nullable', 'string', 'max:100'],
        ]);

        $user = Auth::user();

        DB::transaction(function () use ($user, $validated) {
            $user->addresses()->create([
                'street' => $validated['street'],
                'city' => $validated['city'],
                'zip_code' => $validated['zip_code'],
                'is_main' => true,
            ]);

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name' => $user->name,
                    'phone' => $validated['phone'],
                ]
            );
        });

        // Si tiene carrito pendiente, enviarlo a checkout directamente
        if ($user->cartItems()->exists() || ! empty(session()->get('cart', []))) {
            return redirect()->route('checkout.page')->with('success', __('messages.auth.onboarding_success'));
        }

        return redirect()->intended(route('catalog'))->with('success', __('messages.auth.onboarding_success'));
    }
}
