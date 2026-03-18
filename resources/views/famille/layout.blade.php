<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Ma Bibliothèque')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Lato', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }

        .library-sidebar {
            background: linear-gradient(180deg, #1a0f0a 0%, #2d1810 100%);
        }

        .nav-link {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .nav-link:hover, .nav-link.active {
            border-left-color: #c9a96e;
            background: rgba(201, 169, 110, 0.1);
            color: #c9a96e;
        }

        .card-library {
            background: #fff;
            border: 1px solid #e8dcc8;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(26, 15, 10, 0.08);
        }

        .btn-primary {
            background: #c9a96e;
            color: #1a0f0a;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary:hover { background: #b8954f; }

        .badge-available { background: #d1fae5; color: #065f46; }
        .badge-borrowed  { background: #fef3c7; color: #92400e; }
        .badge-overdue   { background: #fee2e2; color: #991b1b; }

        /* Scrollbar personnalisée */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f5f0e8; }
        ::-webkit-scrollbar-thumb { background: #c9a96e; border-radius: 3px; }
    </style>
</head>
<body class="bg-amber-50 min-h-screen">

<div class="flex min-h-screen">

    {{-- ── Sidebar ── --}}
    <aside class="library-sidebar w-64 min-h-screen flex flex-col fixed left-0 top-0 z-40">

        {{-- Logo --}}
        <div class="p-6 border-b border-amber-900/30">
            <h1 class="font-playfair text-xl text-amber-200 leading-tight">
                📚 Ma Bibliothèque
            </h1>
            @if(isset($activeProfile))
            <div class="mt-3 flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr($activeProfile->name, 0, 1)) }}
                </div>
                <span class="text-amber-300 text-sm">{{ $activeProfile->name }}</span>
            </div>
            @endif
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('famille.home') }}"
               class="nav-link flex items-center gap-3 px-3 py-2 rounded text-amber-200 text-sm {{ request()->routeIs('famille.home') ? 'active' : '' }}">
                🏠 <span>Accueil</span>
            </a>
            <a href="{{ route('famille.mediatheque') }}"
               class="nav-link flex items-center gap-3 px-3 py-2 rounded text-amber-200 text-sm {{ request()->routeIs('famille.mediatheque') ? 'active' : '' }}">
                📚 <span>Médiathèque</span>
            </a>
            <a href="{{ route('famille.prets') }}"
               class="nav-link flex items-center gap-3 px-3 py-2 rounded text-amber-200 text-sm {{ request()->routeIs('famille.prets') ? 'active' : '' }}">
                📤 <span>Mes prêts</span>
            </a>
            <a href="{{ route('famille.historique') }}"
               class="nav-link flex items-center gap-3 px-3 py-2 rounded text-amber-200 text-sm {{ request()->routeIs('famille.historique') ? 'active' : '' }}">
                📖 <span>Historique</span>
            </a>
            <a href="{{ route('famille.collections') }}"
               class="nav-link flex items-center gap-3 px-3 py-2 rounded text-amber-200 text-sm {{ request()->routeIs('famille.collections') ? 'active' : '' }}">
                🗂️ <span>Collections</span>
            </a>
            <a href="{{ route('famille.wishlist') }}"
                class="nav-link flex items-center gap-3 px-3 py-2 rounded text-amber-200 text-sm
                    {{ request()->routeIs('famille.wishlist') ? 'active' : '' }}">
                💝 <span>Ma Wishlist</span>
            </a>

            <div class="pt-4 border-t border-amber-900/30 mt-4">
                <a href="{{ route('famille.suggestion.form') }}"
                   class="nav-link flex items-center gap-3 px-3 py-2 rounded text-amber-200 text-sm">
                    ✨ <span>Suggérer un item</span>
                </a>
            </div>
        </nav>

        {{-- Déconnexion --}}
        <div class="p-4 border-t border-amber-900/30">
            <form method="POST" action="{{ route('famille.logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left flex items-center gap-3 px-3 py-2 text-amber-400 hover:text-amber-200 text-sm transition-colors">
                    🚪 <span>Changer de profil</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Contenu principal ── --}}
    <main class="flex-1 ml-64 p-8">

        {{-- Flash message --}}
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
            {{ session('success') }}
        </div>
        @endif

        @yield('content')
    </main>

</div>

</body>
</html>