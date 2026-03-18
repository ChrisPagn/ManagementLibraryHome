<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Code PIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Lato', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .library-bg {
            position: fixed; inset: 0;
            background:
                linear-gradient(rgba(10,5,2,0.80), rgba(10,5,2,0.80)),
                url('/images/library-bg-index.jpg') center/cover no-repeat;
            z-index: 0;
        }
        .pin-input {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(201,169,110,0.4);
            color: #f5e6c8;
            letter-spacing: 0.5em;
            text-align: center;
        }
        .pin-input:focus {
            outline: none;
            border-color: #c9a96e;
            background: rgba(201,169,110,0.1);
        }
        .btn-gold {
            background: #c9a96e;
            color: #1a0f0a;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-gold:hover { background: #b8954f; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">

<div class="library-bg"></div>

<div class="relative z-10 text-center px-4 w-full max-w-sm">

    {{-- Avatar du profil --}}
    <div class="w-24 h-24 rounded-full mx-auto mb-4 flex items-center justify-center text-4xl font-bold text-white shadow-lg"
         style="background: linear-gradient(135deg, #c9a96e, #8B6914)">
        @if($profile->avatar)
            <img src="{{ asset('storage/'.$profile->avatar) }}"
                 class="w-full h-full rounded-full object-cover" />
        @else
            {{ strtoupper(substr($profile->name, 0, 1)) }}
        @endif
    </div>

    <h2 class="font-playfair text-2xl text-amber-100 mb-1">
        Bonjour {{ $profile->name }} !
    </h2>

    @if($profile->pin)
        <p class="text-amber-500 text-sm mb-8 tracking-wide">
            Saisis ton code PIN
        </p>

        <form method="POST" action="{{ route('famille.pin.verify', $profile) }}">
            @csrf

            <input type="password"
                   name="pin"
                   maxlength="4"
                   autofocus
                   placeholder="• • • •"
                   class="pin-input w-full px-4 py-4 rounded-lg text-2xl mb-4" />

            @error('pin')
            <p class="text-red-400 text-sm mb-4">{{ $message }}</p>
            @enderror

            <button type="submit" class="btn-gold w-full py-3 rounded-lg text-sm tracking-widest uppercase">
                Entrer
            </button>
        </form>
    @else
        {{-- Pas de PIN → redirection directe --}}
        <p class="text-amber-500 text-sm mb-8">Connexion en cours...</p>
        <form method="POST" action="{{ route('famille.pin.verify', $profile) }}" id="autoForm">
            @csrf
            <input type="hidden" name="pin" value="" />
        </form>
        <script>document.getElementById('autoForm').submit();</script>
    @endif

    {{-- Retour --}}
    <a href="{{ route('famille.index') }}"
       class="block mt-6 text-amber-700 hover:text-amber-500 text-sm transition-colors">
        ← Changer de profil
    </a>

</div>

</body>
</html>