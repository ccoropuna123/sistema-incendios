<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {   
        // Muestra la vista del perfil y le pasa
        // los datos del usuario logueado
        return view('profile.edit', [
            'user' => $request->user(),// Obtiene el usuario de la sesión actual
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // fill() llena el modelo con los datos validados
        // (nombre, email, etc.) pero SIN guardar aún en la BD
        $request->user()->fill($request->validated());

        // isDirty('email') detecta si el email fue cambiado
        // Si cambió, se anula la verificación del correo
        // (el usuario tendrá que verificar el nuevo email)
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Ahora sí guarda todos los cambios en la base de datos
        $request->user()->save();

        //mensaje con exito y la devuelve al perfil
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    //aca se elimina la cuenta del usuario
    public function destroy(Request $request): RedirectResponse
    {
        // Valida que el usuario haya ingresado su contraseña correcta
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        // Guardamos referencia al usuario ANTES de cerrar sesión
        $user = $request->user();

        //aca se cierra la sesion
        Auth::logout();

        //aca se elimina la cuenta del usuario de la base de datos
        $user->delete();

        $request->session()->invalidate(); //aca borra los datos de la sesion
        $request->session()->regenerateToken();//aca se genera un nuevo token se seguridad

        //redirige al inicio
        return Redirect::to('/');
    }
}
