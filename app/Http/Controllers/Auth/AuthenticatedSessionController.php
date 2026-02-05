<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        // ✅ Si on arrive avec ?redirect=/books/xxx on le stocke comme intended
        $redirect = (string) $request->query('redirect', '');
        if ($redirect !== '' && $this->isSafeRedirect($redirect)) {
            $request->session()->put('url.intended', $redirect);
        }

        // ✅ Si déjà connecté → on part vers intended (sinon fallback)
        if (Auth::check()) {
            return redirect()->intended(route('books.index'));
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // ✅ Si on POST un redirect, on force intended (safe)
        $redirect = (string) $request->input('redirect', '');
        if ($redirect !== '' && $this->isSafeRedirect($redirect)) {
            $request->session()->put('url.intended', $redirect);
        }

        $request->authenticate();
        $request->session()->regenerate();

        // ✅ intended d'abord, sinon books.index
        return redirect()->intended(route('books.index'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function isSafeRedirect(string $url): bool
    {
        // Autorise URL relative: /books/xxx
        if (str_starts_with($url, '/')) {
            return true;
        }

        // Autorise URL absolue uniquement si même host que APP_URL
        $host = parse_url($url, PHP_URL_HOST);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        return !empty($host) && !empty($appHost) && strtolower($host) === strtolower($appHost);
    }
}
