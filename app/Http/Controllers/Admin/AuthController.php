<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function showLoginForm()
    {
        // Si déjà connecté, on redirige vers le dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Traite la tentative de connexion.
     */
    public function login(Request $request)
    {
        // 1. Validation des champs
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'L\'adresse email est requise.',
            'email.email'       => 'Le format de l\'email est invalide.',
            'password.required' => 'Le mot de passe est requis.',
        ]);

        // 2. Tentative de connexion avec "Remember Me"
        $remember = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {

            // Sécurité : régénérer l'ID de session pour éviter la fixation de session
            $request->session()->regenerate();

            // Redirection vers l'URL initialement demandée ou le dashboard par défaut
            return redirect()->intended(route('admin.dashboard'));
        }

        // 3. Echec de connexion
        return back()
            ->withErrors([
                'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
            ])
            ->onlyInput('email');
    }

    /**
     * Déconnexion.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
