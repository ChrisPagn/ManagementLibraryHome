@extends('famille.layout')

@section('title', 'Bonjour ' . $profile->name)

@section('content')

{{-- En-tête de bienvenue --}}
<div class="mb-8">
    <h2 class="font-playfair text-3xl text-amber-900">
        Bonjour {{ $profile->name }} ! 👋
    </h2>
    <p class="text-amber-700 mt-1">
        {{ now()->translatedFormat('l d F Y') }}
    </p>
</div>

{{-- Alertes prêts en retard --}}
@if($overdueLoans->isNotEmpty())
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
    <p class="text-red-800 font-semibold">
        ⚠️ {{ $overdueLoans->count() }} prêt(s) en retard !
    </p>
    @foreach($overdueLoans as $loan)
    <p class="text-red-600 text-sm mt-1">
        • {{ $loan->item->title }}
        — prévu le {{ $loan->due_at->format('d/m/Y') }}
    </p>
    @endforeach
</div>
@endif

{{-- Stats rapides --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="card-library p-4 text-center">
        <p class="text-3xl font-bold text-amber-700">{{ $activeLoans->count() }}</p>
        <p class="text-sm text-gray-500 mt-1">Prêts en cours</p>
    </div>
    <div class="card-library p-4 text-center">
        <p class="text-3xl font-bold text-amber-700">{{ $myCollections->count() }}</p>
        <p class="text-sm text-gray-500 mt-1">Collections suivies</p>
    </div>
    <div class="card-library p-4 text-center">
        <p class="text-3xl font-bold text-amber-700">{{ $recentItems->count() }}</p>
        <p class="text-sm text-gray-500 mt-1">Mes items</p>
    </div>
</div>

{{-- Prêts en cours --}}
@if($activeLoans->isNotEmpty())
<div class="card-library p-6 mb-6">
    <h3 class="font-playfair text-xl text-amber-900 mb-4">📤 Mes prêts en cours</h3>
    <div class="space-y-3">
        @foreach($activeLoans as $loan)
        <div class="flex items-center justify-between py-2 border-b border-amber-50 last:border-0">
            <div>
                <p class="font-semibold text-gray-800">{{ $loan->item->title }}</p>
                <p class="text-sm text-gray-500">{{ $loan->item->author }}</p>
            </div>
            <div class="text-right">
                @if($loan->due_at)
                    <span class="{{ $loan->isOverdue() ? 'badge-overdue' : 'badge-borrowed' }} text-xs px-2 py-1 rounded-full">
                        {{ $loan->isOverdue() ? '⚠️ En retard' : 'Retour ' . $loan->due_at->format('d/m') }}
                    </span>
                @else
                    <span class="badge-borrowed text-xs px-2 py-1 rounded-full">En cours</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <a href="{{ route('famille.prets') }}" class="block mt-4 text-amber-600 text-sm hover:text-amber-800">
        Voir tous mes prêts →
    </a>
</div>
@endif

{{-- Collections suivies --}}
@if($myCollections->isNotEmpty())
<div class="card-library p-6 mb-6">
    <h3 class="font-playfair text-xl text-amber-900 mb-4">🗂️ Mes collections</h3>
    <div class="space-y-4">
        @foreach($myCollections as $collection)
        @php
            $pct = $collection->completionPercentage();
        @endphp
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span class="font-semibold text-gray-800">{{ $collection->name }}</span>
                <span class="text-gray-500">
                    {{ $collection->items->count() }}
                    @if($collection->total_volumes)
                        / {{ $collection->total_volumes }}
                    @endif
                    tomes
                </span>
            </div>
            @if($collection->total_volumes)
            <div class="w-full bg-amber-100 rounded-full h-2">
                <div class="h-2 rounded-full transition-all"
                     style="width: {{ $pct }}%;
                            background: {{ $pct === 100 ? '#10B981' : '#c9a96e' }}">
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Accès rapide --}}
<div class="flex flex-wrap gap-3">
    <a href="{{ route('famille.mediatheque') }}" class="btn-primary px-5 py-2 rounded-lg text-sm">
        📚 Parcourir la médiathèque
    </a>
    <a href="{{ route('famille.suggestion.form') }}"
       class="px-5 py-2 rounded-lg text-sm border border-amber-300 text-amber-700 hover:bg-amber-50 transition-colors">
        ✨ Suggérer un item
    </a>
</div>

@endsection
