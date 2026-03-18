@extends('famille.layout')

@section('title', 'Collections')

@section('content')

<h2 class="font-playfair text-3xl text-amber-900 mb-6">🗂️ Collections & Séries</h2>

@if($collections->isEmpty())
    <div class="card-library p-12 text-center">
        <p class="text-5xl mb-4">📭</p>
        <p class="text-gray-500">Aucune collection pour le moment.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($collections as $collection)
        @php $pct = $collection->completionPercentage(); @endphp
        <div class="card-library p-5">

            <div class="flex items-start gap-4">
                {{-- Couverture --}}
                <div class="w-16 h-20 rounded overflow-hidden flex-shrink-0 bg-amber-100">
                    @if($collection->cover)
                        <img src="{{ asset('storage/'.$collection->cover) }}"
                             class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full flex items-center justify-center text-2xl">🗂️</div>
                    @endif
                </div>

                {{-- Infos --}}
                <div class="flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-playfair font-semibold text-gray-800">
                                {{ $collection->name }}
                            </p>
                            @if($collection->author)
                            <p class="text-xs text-gray-500">{{ $collection->author }}</p>
                            @endif
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-700 flex-shrink-0">
                            {{ $collection->type->name }}
                        </span>
                    </div>

                    {{-- Progression --}}
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>
                                {{ $collection->items->count() }}
                                @if($collection->total_volumes)
                                    / {{ $collection->total_volumes }} tomes
                                @else
                                    tomes possédés
                                @endif
                            </span>
                            @if($collection->total_volumes)
                                <span class="font-semibold
                                    {{ $pct === 100 ? 'text-green-600' : 'text-amber-600' }}">
                                    {{ $pct }}%
                                </span>
                            @endif
                        </div>

                        @if($collection->total_volumes)
                        <div class="w-full bg-amber-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all duration-500"
                                 style="width: {{ $pct }}%;
                                        background: {{ $pct === 100 ? '#10B981' : '#c9a96e' }}">
                            </div>
                        </div>
                        @endif

                        {{-- Tomes manquants --}}
                        @php $missing = $collection->missingVolumeNumbers(); @endphp
                        @if(! empty($missing))
                        <p class="text-xs text-red-500 mt-1">
                            ❌ Manquants : {{ implode(', ', array_slice($missing, 0, 5)) }}
                            @if(count($missing) > 5) + {{ count($missing) - 5 }} autres @endif
                        </p>
                        @endif

                        @if($collection->is_complete && $pct === 100)
                        <p class="text-xs text-green-600 mt-1 font-semibold">
                            ✅ Collection complète !
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection