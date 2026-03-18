@extends('famille.layout')

@section('title', 'Mes prêts')

@section('content')

<h2 class="font-playfair text-3xl text-amber-900 mb-6">📤 Mes prêts en cours</h2>

@if($loans->isEmpty())
    <div class="card-library p-12 text-center">
        <p class="text-5xl mb-4">✅</p>
        <p class="text-gray-600 font-semibold">Aucun prêt en cours !</p>
        <p class="text-gray-400 text-sm mt-1">Tout est bien rangé dans la bibliothèque.</p>
        <a href="{{ route('famille.mediatheque') }}"
           class="inline-block mt-4 btn-primary px-5 py-2 rounded-lg text-sm">
            Parcourir la médiathèque
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach($loans as $loan)
        <div class="card-library p-5 flex items-center gap-5
                    {{ $loan->isOverdue() ? 'border-red-200 bg-red-50/50' : '' }}">

            <div class="w-14 h-20 rounded flex-shrink-0 overflow-hidden bg-amber-100">
                @if($loan->item->cover)
                    <img src="{{ Str::startsWith($loan->item->cover, 'http')
                                    ? $loan->item->cover
                                    : asset('storage/'.$loan->item->cover) }}"
                         class="w-full h-full object-cover" />
                @else
                    <div class="w-full h-full flex items-center justify-center text-2xl">📖</div>
                @endif
            </div>

            <div class="flex-1">
                <p class="font-semibold text-gray-800">{{ $loan->item->title }}</p>
                <p class="text-sm text-gray-500">{{ $loan->item->author }}</p>
                <p class="text-xs text-gray-400 mt-1">
                    Emprunté le {{ $loan->loaned_at->format('d/m/Y') }}
                </p>
            </div>

            <div class="text-right flex-shrink-0">
                @if($loan->isOverdue())
                    <span class="badge-overdue text-xs px-3 py-1 rounded-full font-medium">
                        ⚠️ En retard
                    </span>
                    <p class="text-xs text-red-500 mt-1">
                        Prévu le {{ $loan->due_at->format('d/m/Y') }}
                    </p>
                @elseif($loan->due_at)
                    <span class="badge-borrowed text-xs px-3 py-1 rounded-full font-medium">
                        📅 {{ $loan->due_at->format('d/m/Y') }}
                    </span>
                    <p class="text-xs text-gray-400 mt-1">Date de retour</p>
                @else
                    <span class="badge-borrowed text-xs px-3 py-1 rounded-full font-medium">
                        En cours
                    </span>
                @endif
            </div>

        </div>
        @endforeach
    </div>
@endif

@endsection