{{-- resources/views/filament/widgets/quick-navigation-widget.blade.php --}}

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-wrap items-center justify-between gap-4">

            {{-- Titre de bienvenue --}}
            <div>
                <h2 class="text-xl font-playfair font-semibold text-gray-900 dark:text-white">
                    📚 Bienvenue dans votre Bibliothèque
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Accès rapide aux sections principales
                </p>
            </div>

            {{-- Boutons de navigation rapide --}}
            <div class="flex flex-wrap gap-3">

                <a href="/admin/items"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                          bg-primary-600 hover:bg-primary-500 text-white
                          text-sm font-medium transition-colors">
                    <x-heroicon-o-book-open class="w-4 h-4" />
                    Médiathèque
                </a>

                <a href="/admin/collections"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                          bg-amber-600 hover:bg-amber-500 text-white
                          text-sm font-medium transition-colors">
                    <x-heroicon-o-rectangle-stack class="w-4 h-4" />
                    Collections
                </a>

                <a href="/admin/loans"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                          bg-emerald-600 hover:bg-emerald-500 text-white
                          text-sm font-medium transition-colors">
                    <x-heroicon-o-arrow-right-circle class="w-4 h-4" />
                    Prêts
                </a>

                <a href="/admin/items/create"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                          border border-gray-300 dark:border-gray-600
                          hover:bg-gray-50 dark:hover:bg-gray-800
                          text-gray-700 dark:text-gray-300
                          text-sm font-medium transition-colors">
                    <x-heroicon-o-plus-circle class="w-4 h-4" />
                    Ajouter un item
                </a>

                {{-- ← Nouveau bouton Vue famille --}}
               {{-- ← Nouveau bouton Vue famille --}}
                <a href="/famille"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                          border border-gray-300 dark:border-gray-600
                          hover:bg-gray-50 dark:hover:bg-gray-800
                          text-gray-700 dark:text-gray-300
                          text-sm font-medium transition-colors">
                    <x-heroicon-o-home class="w-4 h-4" />
                    Vue famille => vers le site public.
                </a>

            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>