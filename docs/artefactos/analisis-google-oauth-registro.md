# Análisis y Diseño Técnico: Autenticación y Registro con Google OAuth 2.0 en Reposa+

## 1. Introducción y Justificación de Negocio

El registro tradicional basado en correo electrónico y contraseña presenta fricciones documentadas en el comercio electrónico:
- **Fatiga de contraseñas (*Password Fatigue*):** Los usuarios tienden a abandonar registros si deben inventar y recordar una nueva contraseña con requisitos complejos (mayúsculas, números, caracteres especiales).
- **Verificación de correo inmediata:** Con Google OAuth 2.0, el correo electrónico ya está verificado por Google, eliminando registros con cuentas falsas o correos mal escritos que impiden la recepción de facturas y notificaciones de envío.
- **Incremento de conversión (CRO):** El inicio de sesión social con un solo clic (*Social Sign-On*) suele incrementar las tasas de registro en un **20% a 35%**, especialmente en dispositivos móviles donde autocompletar credenciales es más engorroso.
- **Alineación con la propuesta de valor de Reposa+:** Brinda una experiencia serena, rápida y segura, respaldada por la infraestructura de seguridad de Google.

---

## 2. Arquitectura de Integración con Laravel Socialite

Para integrar Google OAuth 2.0 en el stack tecnológico de Reposa+ (Laravel 11/13, Blade, Bootstrap 5.3), la solución estándar de la industria es el paquete oficial **`laravel/socialite`**.

```mermaid
sequenceDiagram
    autonumber
    actor Usuario as Cliente
    participant App as Reposa+ Web
    participant Google as Servidor OAuth 2.0 de Google
    participant DB as Base de Datos (MySQL)

    Usuario->>App: Clic en "Continuar con Google"
    App->>Google: Redirección con client_id, redirect_uri, scope y state (CSRF)
    Google->>Usuario: Pantalla de consentimiento de Google (selección de cuenta)
    Usuario->>Google: Autoriza el acceso a perfil y email
    Google-->>App: Redirección a /auth/google/callback con ?code=...&state=...
    App->>Google: Intercambio seguro del código por token de acceso (POST backchannel)
    Google-->>App: Retorna datos del usuario (id, name, email, avatar)
    App->>DB: Busca por google_id o email existente
    alt Usuario ya existe con google_id
        App->>Usuario: Inicia sesión (Auth::login) y redirige a /profile o /checkout
    alt Usuario existe con email pero sin google_id
        App->>DB: Vincula google_id a la cuenta existente
        App->>Usuario: Inicia sesión y redirige
    alt Usuario nuevo
        App->>DB: Crea User (name, email, google_id, password aleatorio/null)
        App->>Usuario: Redirige a pantalla de Onboarding de Dirección de Envío
    end
```

---

## 3. Configuración Requerida en Google Cloud Platform (GCP)

1. **Creación del Proyecto:**
   - Acceder a [Google Cloud Console](https://console.cloud.google.com/).
   - Crear un proyecto denominado `Reposa-Plus-Production` (o `Reposa-Plus-Dev`).
2. **Pantalla de Consentimiento OAuth (*OAuth Consent Screen*):**
   - Tipo de usuario: **Externo**.
   - Nombre de la aplicación: `Reposa+`.
   - Correo electrónico de soporte y desarrollador.
   - Dominios autorizados: `reposaplus.es` (y `localhost` para entornos de desarrollo).
   - Enlaces obligatorios: Política de Privacidad y Términos del Servicio de Reposa+.
3. **Ámbitos (*Scopes*) Solicitados:**
   - `openid`: Identificador único de OpenID Connect.
   - `profile` (`https://www.googleapis.com/auth/userinfo.profile`): Nombre completo y avatar.
   - `email` (`https://www.googleapis.com/auth/userinfo.email`): Correo electrónico principal verificado.
4. **Credenciales OAuth 2.0:**
   - Tipo: **Aplicación Web**.
   - Orígenes de JavaScript autorizados: `http://localhost:8000`, `https://reposaplus.es`.
   - URIs de redireccionamiento autorizados:
     - Desarrollo: `http://localhost:8000/auth/google/callback`
     - Producción: `https://reposaplus.es/auth/google/callback`
   - Generación de:
     - `GOOGLE_CLIENT_ID`: Identificador público de la aplicación.
     - `GOOGLE_CLIENT_SECRET`: Clave secreta almacenada en variables de entorno.

---

## 4. El Gran Reto Arquitectónico: La Dirección de Envío Obligatoria

En Reposa+, **los datos de envío (calle, ciudad, código postal y teléfono) son estrictamente obligatorios en el registro**. Sin embargo, la API de Google OAuth 2.0 solo proporciona:
- Nombre y apellidos.
- Correo electrónico.
- Fotografía de perfil (avatar).
- Identificador numérico de Google (`sub`).

Google **no proporciona la dirección postal del usuario** (por razones de privacidad y porque las cuentas personales no tienen una dirección postal única estandarizada en su perfil público).

### Estrategia de Solución: Flujo de Onboarding en Dos Fases (*Two-Step Progressive Registration*)

Para cumplir simultáneamente con la facilidad de Google OAuth y el requisito ineludible de contar con la dirección física, se define la siguiente arquitectura:

```
Paso 1: Autenticación Social (Google OAuth)
   │
   ▼
¿El usuario ya tiene dirección registrada en Reposa+?
   ├── SÍ ──► Iniciar sesión completa y continuar al carrito/perfil.
   └── NO ──► Redirección inmediata a: /register/complete-profile
               │
               ▼
Paso 2: Formulario de Onboarding de Envío
   - Nombre y Email ya precargados y deshabilitados (vienen de Google).
   - Campos obligatorios a completar:
       • Dirección (Calle, nº, piso/puerta).
       • Código Postal (5 dígitos).
       • Ciudad y Provincia.
       • Teléfono móvil (para paquetería).
   - Botón: "Finalizar registro y empezar a descansar"
               │
               ▼
Creación de Address (is_main = true) + Profile (phone)
               │
               ▼
Sesión plenamente operativa + Fusión automática del carrito de sesión
```

#### Protección de Rutas mediante Middleware (*EnsureProfileCompleted*):
Si un usuario registrado mediante Google intenta navegar por la tienda sin haber completado su dirección de entrega en el paso 2, un middleware interceptor redirige con el aviso:
> *"Para garantizar la correcta entrega de tus pedidos, completa tu dirección de envío habitual."*

---

## 5. Cambios Técnicos y Esquema de Base de Datos

### 5.1. Migración de Base de Datos (`users` table)
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('google_id')->nullable()->unique()->after('email');
    $table->string('avatar')->nullable()->after('google_id');
    $table->string('password')->nullable()->change(); // Permite usuarios sin password local
});
```

### 5.2. Variables de Entorno (`.env`)
```env
GOOGLE_CLIENT_ID=xxxxxxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxxxxxx
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### 5.3. Configuración en `config/services.php`
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

### 5.4. Controlador de Autenticación Social (`GoogleAuthController.php`)
```php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', __('messages.auth.google_failed'));
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Vincular cuenta si no tenía google_id
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }
            Auth::login($user);

            // Si le falta la dirección, enviar al onboarding
            if ($user->addresses()->count() === 0) {
                return redirect()->route('register.complete-profile');
            }

            return redirect()->intended('/profile');
        }

        // Crear nuevo usuario provisional
        $newUser = DB::transaction(function () use ($googleUser) {
            return User::create([
                'name' => $googleUser->getName() ?? 'Usuario Google',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => null, // Autenticación 100% delegada a OAuth
            ]);
        });

        Auth::login($newUser);

        // Redirigir a completar la dirección obligatoria
        return redirect()->route('register.complete-profile')
            ->with('info', __('messages.auth.google_welcome_complete_address'));
    }
}
```

---

## 6. Consideraciones de Seguridad y Buenas Prácticas

1. **Mitigación de CSRF (Ataques de fijación de sesión):**
   - `laravel/socialite` valida automáticamente el parámetro criptográfico `state` almacenado en la sesión de Laravel. Esto previene ataques donde un atacante induce a la víctima a asociar la cuenta de Google del atacante a la sesión de la víctima.
2. **Cuentas sin contraseña local (*Passwordless OAuth*):**
   - Al permitir `password` nullable, los usuarios de Google no tienen contraseña que pueda ser comprometida por fuerza bruta en la pantalla de login tradicional. Si intentan hacer login tradicional por formulario, se les informa: *"Esta cuenta utiliza inicio de sesión con Google"*.
   - Opcionalmente, desde su panel de perfil pueden asignar una contraseña local si en el futuro desean acceder por ambos métodos.
3. **Vinculación segura por correo electrónico (*Account Linking*):**
   - Si un usuario ya se había registrado con `usuario@gmail.com` y contraseña local, y más tarde hace clic en *"Continuar con Google"* con esa misma cuenta de Gmail, el sistema vincula el `google_id` automáticamente porque Google certifica fehacientemente la titularidad del correo (`email_verified: true`).
4. **Cumplimiento RGPD (Consentimiento y Transferencias Internacionales):**
   - El botón de Google debe acompañarse de texto informativo: *"Al continuar con Google, aceptas los Términos y la Política de Privacidad de Reposa+."*
   - Los datos recuperados (nombre, email, avatar) quedan sujetos a la misma política de derechos ARCO/POL (acceso, rectificación, supresión) que los registros tradicionales.

---

## 7. Plan de Trabajo e Integración Futura (Roadmap)

| Fase | Tareas Clave | Dependencias |
| :---: | :--- | :--- |
| **Fase 1** | Registrar credenciales en Google Cloud Console e instalar `composer require laravel/socialite`. | Cuenta de Google Cloud de Reposa+. |
| **Fase 2** | Crear migración de `google_id`, `avatar` y `password` nullable en `users`. | Aprobación de esquema de base de datos. |
| **Fase 3** | Implementar `GoogleAuthController` y vistas de redirección y callback. | `routes/web.php` y `config/services.php`. |
| **Fase 4** | Implementar la pantalla de Onboarding de Dirección obligatoria (`register/complete-profile.blade.php`). | Componentes Blade Midnight Sanctuary. |
| **Fase 5** | Botón de UI accesible con la marca oficial de Google en `login.blade.php` y `register.blade.php`. | Directrices de marca de Google Identity Services. |
| **Fase 6** | Tests de integración (mocking de Socialite con `Socialite::shouldReceive('driver->...')`). | Suite de pruebas PHPUnit/Pest. |
