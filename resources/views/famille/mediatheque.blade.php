@extends('famille.layout')

@section('title', 'Médiathèque')

@section('content')

<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <h2 class="font-playfair text-3xl text-amber-900">📚 Médiathèque</h2>

    <form method="GET" action="{{ route('famille.mediatheque') }}"
          class="flex gap-2 flex-wrap">
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Rechercher un titre, auteur..."
               class="px-4 py-2 rounded-lg border border-amber-200 bg-white text-sm focus:outline-none focus:border-amber-400 w-64" />

        <select name="type"
                class="px-4 py-2 rounded-lg border border-amber-200 bg-white text-sm focus:outline-none focus:border-amber-400">
            <option value="">Tous les types</option>
            @foreach(\App\Models\ItemType::all() as $type)
                <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn-primary px-4 py-2 rounded-lg text-sm">
            Filtrer
        </button>

        @if(request('search') || request('type'))
        <a href="{{ route('famille.mediatheque') }}"
           class="px-4 py-2 rounded-lg border border-amber-200 text-amber-700 text-sm hover:bg-amber-50 transition-colors">
            ✕ Réinitialiser
        </a>
        @endif
    </form>
</div>

@if($items->isEmpty())
    <div class="card-library p-12 text-center">
        <p class="text-4xl mb-4">📭</p>
        <p class="text-gray-500">Aucun item trouvé.</p>
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
        @foreach($items as $item)
        <div class="card-library overflow-hidden group">

            <div class="aspect-[2/3] bg-amber-50 relative overflow-hidden">
                @if($item->cover)
                    <img src="{{ Str::startsWith($item->cover, 'http')
                                    ? $item->cover
                                    : asset('storage/'.$item->cover) }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         alt="{{ $item->title }}" />
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center"
                         style="background: linear-gradient(135deg, #c9a96e22, #c9a96e44)">
                        <span class="text-3xl mb-2">📖</span>
                        <p class="text-xs text-amber-800 font-semibold leading-tight">
                            {{ Str::limit($item->title, 40) }}
                        </p>
                    </div>
                @endif

                <div class="absolute top-2 right-2">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $item->status === 'available' ? 'badge-available' : 'badge-borrowed' }}">
                        {{ $item->status === 'available' ? 'Dispo' : 'Emprunté' }}
                    </span>
                </div>

                @if($item->owner_profile_id === $activeProfile->id)
                <div class="absolute top-2 left-2">
                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500 text-white font-medium">
                        ★ Mien
                    </span>
                </div>
                @endif
            </div>

            <div class="p-3">
                <p class="font-semibold text-gray-800 text-sm leading-tight truncate">
                    {{ $item->title }}
                </p>
                @if($item->author)
                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $item->author }}</p>
                @endif
                <span class="inline-block mt-2 text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                    {{ $item->type->name }}
                </span>
            </div>

        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $items->withQueryString()->links() }}
    </div>
@endif

@endsection