<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirige al usuario al portal de autenticación OAuth 2.0 de Google.
     */
    public function redirect(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('profile');
        }

        $previous = url()->previous();
        if ($request->get('redirect') === 'checkout'
            || ($previous && (str_contains($previous, '/checkout') || $previous === route('checkout.page')))) {
            session()->put('url.intended', route('checkout.page'));
            session()->put('from_checkout', true);
        } elseif ($request->filled('redirect')) {
            session()->put('url.intended', $request->get('redirect'));
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Procesa la respuesta de Google tras la autorización del usuario.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth callback error: '.$e->getMessage());

            return redirect()->route('login')->with('error', __('messages.auth.google_failed'));
        }

        $googleId = (string) $googleUser->getId();
        $email = (string) $googleUser->getEmail();
        $name = $googleUser->getName() ?: (explode('@', $email)[0] ?? 'Usuario');
        $avatar = $googleUser->getAvatar();

        $isFromCheckout = session('from_checkout', false)
            || str_contains(session('url.intended', ''), '/checkout');

        $defaultRedirect = $isFromCheckout ? route('checkout.page') : route('catalog');

        // Escenario A: Usuario ya registrado previamente con google_id
        $user = User::where('google_id', $googleId)->first();

        if ($user) {
            session()->forget('from_checkout');

            if ($avatar && $user->avatar !== $avatar) {
                $user->update(['avatar' => $avatar]);
            }

            Auth::login($user, true);

            // Si por algún motivo aún no tiene dirección de envío
            if (! $user->addresses()->exists()) {
                return redirect()->route('onboarding.shipping')
                    ->with('info', __('messages.auth.google_welcome_complete_address'));
            }

            return redirect()->intended($defaultRedirect)->with('success', __('messages.auth.google_login_success'));
        }

        // Escenario B: Usuario registrado previamente por email tradicional
        $user = User::where('email', $email)->first();

        if ($user) {
            session()->forget('from_checkout');

            $user->update([
                'google_id' => $googleId,
                'avatar' => $user->avatar ?: $avatar,
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);

            Auth::login($user, true);

            if (! $user->addresses()->exists()) {
                return redirect()->route('onboarding.shipping')
                    ->with('info', __('messages.auth.google_linked_complete_address'));
            }

            return redirect()->intended($defaultRedirect)->with('success', __('messages.auth.google_linked_success'));
        }

        // Escenario C: Nuevo usuario registrado vía Google OAuth
        $newUser = DB::transaction(function () use ($googleId, $email, $name, $avatar) {
            return User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $avatar,
                'email_verified_at' => now(),
                'password' => null,
                'role' => 'client',
            ]);
        });

        Auth::login($newUser, true);

        // Nuevo usuario de Google no tiene dirección -> Redirección obligatoria al Onboarding
        return redirect()->route('onboarding.shipping')
            ->with('info', __('messages.auth.google_welcome_complete_address'));
    }
}
