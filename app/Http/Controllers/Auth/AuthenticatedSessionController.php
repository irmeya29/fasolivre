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
    public function create(Request $request): View|RedirectResponse
    {
        // ✅ Si on arrive avec ?redirect=/books/xxx, on force l'intended
        $redirect = (string) $request->query('redirect', '');
        if ($redirect !== '' && $this->isSafeRedirect($redirect)) {
            $request->session()->put('url.intended', $redirect);
        }

        // Si déjà connecté → on va là où Laravel veut (intended), sinon /account
        if (Auth::check()) {
            return redirect()->intended(route('account.index'));
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
{
    // ✅ si on a un redirect=... on le force comme intended
    if ($request->filled('redirect')) {
        $request->session()->put('url.intended', $request->input('redirect'));
    }

    $request->authenticate();
    $request->session()->regenerate();

    // ✅ d'abord intended, sinon books.index (ou home)
    return redirect()->intended(route('books.index'));
}


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
        if (str_starts_with($url, '/')) return true;

        // Autorise URL absolue uniquement si même host que APP_URL
        $host = parse_url($url, PHP_URL_HOST);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        return $host && $appHost && strtolower($host) === strtolower($appHost);
    }
}
