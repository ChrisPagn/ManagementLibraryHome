<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Qui êtes-vous ?</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Lato', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .library-bg {
            position: fixed; inset: 0;
            background:
                linear-gradient(rgba(10,5,2,0.75), rgba(10,5,2,0.75)),
                url('/images/library-bg-index.jpg') center/cover no-repeat;
            z-index: 0;
        }
        .profile-card {
            transition: all 0.3s ease;
            border: 2px solid rgba(201,169,110,0.2);
        }
        .profile-card:hover {
            transform: translateY(-6px);
            border-color: #c9a96e;
            box-shadow: 0 12px 32px rgba(201,169,110,0.2);
        }
        .avatar {
            background: linear-gradient(135deg, #c9a96e, #8B6914);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">

<div class="library-bg"></div>

<div class="relative z-10 text-center px-4">

    <h1 class="font-playfair text-4xl text-amber-100 mb-2">
        Bibliothèque Familiale
    </h1>
    <p class="text-amber-400 text-sm tracking-widest uppercase mb-12">
        Qui lit ce soir ?
    </p>

    {{-- Grille des profils --}}
    <div class="flex flex-wrap justify-center gap-6 max-w-2xl mx-auto">
        @foreach($profiles as $profile)
        <a href="{{ route('famille.pin.show', $profile) }}"
           class="profile-card bg-black/40 backdrop-blur-sm rounded-xl p-6 w-36 cursor-pointer text-center">

            {{-- Avatar --}}
            <div class="avatar w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center text-3xl font-bold text-white shadow-lg">
                @if($profile->avatar)
                    <img src="{{ asset('storage/'.$profile->avatar) }}"
                         class="w-full h-full rounded-full object-cover" />
                @else
                    {{ strtoupper(substr($profile->name, 0, 1)) }}
                @endif
            </div>

            <p class="font-playfair text-amber-100 text-lg">{{ $profile->name }}</p>

            @if($profile->role === 'admin')
            <span class="text-xs text-amber-500 mt-1 block">Admin</span>
            @endif

        </a>
        @endforeach
    </div>

    {{-- Lien admin --}}
    <div class="mt-12">
        <a href="/admin" class="text-amber-700 hover:text-amber-500 text-xs tracking-widest uppercase transition-colors">
            Accès administration →
        </a>
    </div>

</div>

</body>
</html>