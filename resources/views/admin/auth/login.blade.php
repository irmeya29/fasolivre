<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin – Fasolivre</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white w-full max-w-md p-8 rounded-xl shadow-lg">
    <h1 class="text-2xl font-bold text-center mb-6">Admin Fasolivre</h1>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="mb-4">
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" required
                   class="w-full mt-1 p-2 border rounded-lg">
        </div>

        <div class="mb-4">
            <label class="text-sm font-medium">Mot de passe</label>
            <input type="password" name="password" required
                   class="w-full mt-1 p-2 border rounded-lg">
        </div>

        <button class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">
            Se connecter
        </button>
    </form>

</div>

</body>
</html>
