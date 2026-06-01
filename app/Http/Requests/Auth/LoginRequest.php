<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        // Primero verifica que no haya demasiados intentos fallidos
        $this->ensureIsNotRateLimited();

        // Auth::attempt() busca en la BD si el email y password son correctos
        // $this->only('email', 'password') = toma solo esos dos campos del formulario
        // $this->boolean('remember') = si marcó "recuérdame" o no
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Si el login falló: registra un intento fallido
            RateLimiter::hit($this->throttleKey());

            // Y lanza un error visible en el formulario
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),// Mensaje: "Credenciales incorrectas"
            ]);
        }

        // Si el login fue exitoso: limpia el contador de intentos fallidos
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */

    // Protege contra ataques de fuerza bruta
    // (alguien que prueba miles de contraseñas)
    public function ensureIsNotRateLimited(): void
    {
        // Si tiene MENOS de 5 intentos fallidos, puede continuar
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        // Si llegó a 5 intentos fallidos:
        // Dispara el evento Lockout (puede usarse para enviar notificaciones)
        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Lanza error con mensaje: "Demasiados intentos. Espera X segundos"
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),// Convierte segundos a minutos
            ]),
        ]);
    }

    //  Generar clave única para el rate limiter
    public function throttleKey(): string
    {   
        // Str::lower() = convierte email a minúsculas
        // Str::transliterate() = convierte caracteres especiales (ñ, á, etc.)
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
