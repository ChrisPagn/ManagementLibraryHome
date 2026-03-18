@extends('famille.layout')

@section('title', 'Suggérer un item')

@section('content')

<div class="max-w-lg">

    <h2 class="font-playfair text-3xl text-amber-900 mb-2">✨ Suggérer un item</h2>
    <p class="text-gray-500 text-sm mb-6">
        Ta suggestion sera envoyée à l'administrateur pour validation.
    </p>

    <div class="card-library p-6">
        <form method="POST" action="{{ route('famille.suggestion.store') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full px-4 py-2 rounded-lg border border-amber-200 text-sm focus:outline-none focus:border-amber-400"
                           placeholder="Ex: Harry Potter et la Chambre des Secrets" />
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Auteur</label>
                    <input type="text" name="author" value="{{ old('author') }}"
                           class="w-full px-4 py-2 rounded-lg border border-amber-200 text-sm focus:outline-none focus:border-amber-400"
                           placeholder="Ex: J.K. Rowling" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn') }}"
                           class="w-full px-4 py-2 rounded-lg border border-amber-200 text-sm focus:outline-none focus:border-amber-400"
                           placeholder="Ex: 9782070541270" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Pourquoi tu veux cet item ?
                    </label>
                    <textarea name="note" rows="3"
                              class="w-full px-4 py-2 rounded-lg border border-amber-200 text-sm focus:outline-none focus:border-amber-400"
                              placeholder="Ex: J'ai adoré le 1er tome et je voudrais lire la suite !">{{ old('note') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn-primary px-6 py-2 rounded-lg text-sm">
                    ✨ Envoyer la suggestion
                </button>
                <a href="{{ route('famille.home') }}"
                   class="px-6 py-2 rounded-lg border border-amber-200 text-amber-700 text-sm hover:bg-amber-50 transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>

</div>

@endsection