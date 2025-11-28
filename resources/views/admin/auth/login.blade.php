@extends('admin.layouts.app')

@section('title', 'Connexion Admin – Fasolivre')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-sm mt-5">
            <div class="card-body p-4">
                <h1 class="h5 mb-1">Espace administration</h1>
                <p class="text-muted small mb-4">
                    Connectez-vous pour gérer les livres, auteurs, catégories et soumissions.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger small py-2">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small">Adresse e-mail</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control form-control-sm @error('email') is-invalid @enderror"
                            required
                            autofocus
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Mot de passe</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control form-control-sm @error('password') is-invalid @enderror"
                            required
                        >
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label small" for="remember">Se souvenir de moi</label>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary btn-sm">
                            Connexion
                        </button>
                    </div>
                </form>
            </div>

            <div class="card-footer text-center small text-muted">
                © {{ date('Y') }} Fasolivre – Admin
            </div>
        </div>
    </div>
</div>
@endsection
