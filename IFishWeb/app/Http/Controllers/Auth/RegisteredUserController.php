<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Criadero;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Usamos una transacción para asegurar que ambas operaciones (crear usuario y criadero)
        // se completen con éxito. Si una falla, la otra se deshace (rollback).
        $user = DB::transaction(function () use ($request) {

            // 1. Creamos el nuevo usuario con el rol de "Dueño"
            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => 'Dueño', // Asignamos el nuevo rol por defecto
            ]);

            // 2. Creamos un criadero para este nuevo usuario
            $newCriadero = Criadero::create([
                'nombre' => 'Criadero de ' . $newUser->name, // Creamos un nombre por defecto
                'user_id' => $newUser->id, // Lo asignamos al usuario que acabamos de crear
            ]);

            // 3. Actualizamos al usuario para "etiquetarlo" con el ID de su nuevo criadero
            $newUser->criadero_id = $newCriadero->id;
            $newUser->save();

            return $newUser;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
