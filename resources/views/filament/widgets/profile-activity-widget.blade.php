<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                👥 Activité des membres
            </h3>
            <a href="/admin/profiles"
               class="text-sm text-primary-600 hover:text-primary-500 transition-colors">
                Gérer les profils →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="text-left py-2 px-3 text-xs text-gray-500 uppercase tracking-wider">Membre</th>
                        <th class="text-center py-2 px-3 text-xs text-gray-500 uppercase tracking-wider">Prêts actifs</th>
                        <th class="text-center py-2 px-3 text-xs text-gray-500 uppercase tracking-wider">Total prêts</th>
                        <th class="text-center py-2 px-3 text-xs text-gray-500 uppercase tracking-wider">Avis</th>
                        <th class="text-center py-2 px-3 text-xs text-gray-500 uppercase tracking-wider">Suggestions</th>
                        <th class="text-center py-2 px-3 text-xs text-gray-500 uppercase tracking-wider">Wishlist</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach($profiles as $profile)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">

                        <td class="py-3 px-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex-shrink-0 overflow-hidden
                                            bg-amber-500 flex items-center justify-center
                                            text-white text-sm font-bold">
                                    @if($profile['avatar'])
                                        <img src="{{ asset('storage/'.$profile['avatar']) }}"
                                             class="w-full h-full object-cover" />
                                    @else
                                        {{ strtoupper(substr($profile['name'], 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $profile['name'] }}
                                    </p>
                                    <span class="text-xs px-1.5 py-0.5 rounded
                                        {{ $profile['role'] === 'admin'
                                            ? 'bg-amber-100 text-amber-700'
                                            : 'bg-gray-100 text-gray-500' }}">
                                        {{ $profile['role'] === 'admin' ? '👑 Admin' : '👤 Membre' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="py-3 px-3 text-center">
                            @if($profile['loans_active'] > 0)
                                <span class="inline-flex items-center justify-center w-7 h-7
                                             rounded-full bg-amber-100 text-amber-700 text-xs font-bold">
                                    {{ $profile['loans_active'] }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        <td class="py-3 px-3 text-center text-gray-600 dark:text-gray-400">
                            {{ $profile['loans_total'] ?: '—' }}
                        </td>

                        <td class="py-3 px-3 text-center text-gray-600 dark:text-gray-400">
                            {{ $profile['reviews'] ?: '—' }}
                        </td>

                        <td class="py-3 px-3 text-center">
                            @if($profile['suggestions'] > 0)
                                <span class="inline-flex items-center justify-center w-7 h-7
                                             rounded-full bg-blue-100 text-blue-700 text-xs font-bold">
                                    {{ $profile['suggestions'] }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        <td class="py-3 px-3 text-center">
                            @if($profile['wishlist'] > 0)
                                <span class="inline-flex items-center justify-center w-7 h-7
                                             rounded-full bg-pink-100 text-pink-700 text-xs font-bold">
                                    {{ $profile['wishlist'] }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>