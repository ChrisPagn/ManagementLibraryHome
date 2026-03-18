@extends('famille.layout')

@section('title', 'Ma Wishlist')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h2 class="font-playfair text-3xl text-amber-900">💝 Ma Wishlist</h2>
    <button onclick="document.getElementById('add-form').classList.toggle('hidden')"
            class="btn-primary px-4 py-2 rounded-lg text-sm">
        + Ajouter un souhait
    </button>
</div>

{{-- Formulaire d'ajout --}}
<div id="add-form" class="card-library p-6 mb-6 hidden">
    <h3 class="font-playfair text-lg text-amber-900 mb-4">Nouveau souhait</h3>
    <form method="POST" action="{{ route('famille.wishlist.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Titre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-2 rounded-lg border border-amber-200 text-sm
                              focus:outline-none focus:border-amber-400"
                       placeholder="Ex: Harry Potter T7" />
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Auteur</label>
                <input type="text" name="author" value="{{ old('author') }}"
                       class="w-full px-4 py-2 rounded-lg border border-amber-200 text-sm
                              focus:outline-none focus:border-amber-400" />
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">ISBN</label>
                <input type="text" name="isbn" value="{{ old('isbn') }}"
                       class="w-full px-4 py-2 rounded-lg border border-amber-200 text-sm
                              focus:outline-none focus:border-amber-400" />
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Priorité</label>
                <select name="priority"
                        class="w-full px-4 py-2 rounded-lg border border-amber-200 text-sm
                               focus:outline-none focus:border-amber-400">
                    <option value="high">🔴 Haute</option>
                    <option value="medium" selected>🟡 Moyenne</option>
                    <option value="low">🟢 Basse</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Note</label>
                <input type="text" name="note" value="{{ old('note') }}"
                       class="w-full px-4 py-2 rounded-lg border border-amber-200 text-sm
                              focus:outline-none focus:border-amber-400"
                       placeholder="Pourquoi tu veux cet item ?" />
            </div>
        </div>

        <div class="flex gap-3 mt-4">
            <button type="submit" class="btn-primary px-6 py-2 rounded-lg text-sm">
                Ajouter à ma wishlist
            </button>
            <button type="button"
                    onclick="document.getElementById('add-form').classList.add('hidden')"
                    class="px-6 py-2 rounded-lg border border-amber-200 text-amber-700
                           text-sm hover:bg-amber-50 transition-colors">
                Annuler
            </button>
        </div>
    </form>
</div>

{{-- Liste des souhaits --}}
@if($myWishlist->isEmpty())
    <div class="card-library p-12 text-center mb-6">
        <p class="text-5xl mb-4">💝</p>
        <p class="text-gray-500">Ta wishlist est vide !</p>
        <p class="text-gray-400 text-sm mt-1">Ajoute des items que tu aimerais avoir.</p>
    </div>
@else
    <div class="space-y-3 mb-8">
        @foreach($myWishlist as $wish)
        <div class="card-library p-4 flex items-center gap-4">

            {{-- Priorité indicator --}}
            <div class="w-2 h-12 rounded-full flex-shrink-0"
                 style="background: {{ match($wish->priority) {
                     'high'   => '#EF4444',
                     'medium' => '#F59E0B',
                     'low'    => '#10B981',
                 } }}">
            </div>

            {{-- Infos --}}
            <div class="flex-1">
                <p class="font-semibold text-gray-800">{{ $wish->title }}</p>
                <div class="flex items-center gap-3 mt-0.5">
                    @if($wish->author)
                    <p class="text-xs text-gray-500">{{ $wish->author }}</p>
                    @endif
                    @if($wish->type)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                        {{ $wish->type->name }}
                    </span>
                    @endif
                </div>
                @if($wish->note)
                <p class="text-xs text-gray-400 mt-1 italic">{{ $wish->note }}</p>
                @endif
            </div>

            {{-- Priorité label --}}
            <div class="text-right flex-shrink-0">
                <span class="text-xs font-medium
                    {{ $wish->priority === 'high'   ? 'text-red-500'    : '' }}
                    {{ $wish->priority === 'medium' ? 'text-amber-500'  : '' }}
                    {{ $wish->priority === 'low'    ? 'text-green-500'  : '' }}">
                    {{ $wish->priorityLabel() }}
                </span>
            </div>
        </div>
        @endforeach
    </div>
@endif

{{-- Items récemment acquis --}}
@if($acquiredItems->isNotEmpty())
<div class="card-library p-6">
    <h3 class="font-playfair text-lg text-amber-900 mb-4">✅ Récemment acquis</h3>
    <div class="space-y-2">
        @foreach($acquiredItems as $wish)
        <div class="flex items-center justify-between py-2 border-b border-amber-50 last:border-0">
            <p class="text-sm text-gray-600 line-through">{{ $wish->title }}</p>
            <span class="text-xs text-gray-400">
                {{ $wish->acquired_at?->format('d/m/Y') }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection