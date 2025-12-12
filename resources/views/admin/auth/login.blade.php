<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administration - Fasolivre</title>

    {{-- Tailwind & Fonts --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Motif de fond subtil */
        .bg-pattern {
            background-color: #f9fafb;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="h-full bg-pattern flex flex-col justify-center py-12 sm:px-6 lg:px-8">

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        {{-- Logo --}}
        <div class="mx-auto h-12 w-12 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg transform rotate-3">
            <i data-feather="book" class="text-white w-6 h-6 transform -rotate-3"></i>
        </div>

        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
            Fasolivre Admin
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Accédez à votre tableau de bord de gestion
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl shadow-gray-200/50 sm:rounded-xl sm:px-10 border border-gray-100">

            {{-- Gestion des Erreurs --}}
            @if($errors->any())
                <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-100">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-feather="alert-circle" class="h-5 w-5 text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Erreur de connexion</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul role="list" class="list-disc pl-5 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('admin.login.submit') }}" method="POST">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Adresse Email</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="mail" class="h-4 w-4 text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out"
                               placeholder="admin@fasolivre.com"
                               value="{{ old('email') }}">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="lock" class="h-4 w-4 text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out"
                               placeholder="••••••••">
                    </div>
                </div>

                {{-- Options (Remember Me & Forgot Password) --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900 cursor-pointer">
                            Se souvenir de moi
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Mot de passe oublié ?
                        </a>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div>
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Se connecter
                    </button>
                </div>
            </form>

        </div>

        {{-- Footer --}}
        <p class="mt-6 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} Fasolivre Platform. Tous droits réservés.
        </p>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>
