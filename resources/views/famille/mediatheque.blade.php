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

            {{-- ✅ div.p-3 avec le bouton Mon avis au bon endroit --}}
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

                {{-- ✅ Bouton ICI — sur chaque card --}}
                <button onclick="openReviewModal({{ $item->id }}, '{{ addslashes($item->title) }}')"
                        class="mt-2 w-full text-xs py-1 rounded border border-amber-200
                               text-amber-600 hover:bg-amber-50 transition-colors">
                    ✍️ Mon avis
                </button>
            </div>

        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $items->withQueryString()->links() }}
    </div>

@endif
{{-- ✅ @endif ICI — le modal est EN DEHORS du @else --}}

{{-- ✅ Modal ICI — après @endif, avant @endsection --}}
<div id="review-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background: rgba(0,0,0,0.5)">
    <div class="card-library p-6 w-full max-w-md mx-4">

        <div class="flex justify-between items-center mb-4">
            <h3 class="font-playfair text-xl text-amber-900" id="modal-title">Mon avis</h3>
            <button onclick="closeReviewModal()"
                    class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
        </div>

        <form id="review-form" method="POST" action="">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Statut</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([
                        'to_read'     => '📚 À lire',
                        'in_progress' => '📖 En cours',
                        'completed'   => '✅ Terminé',
                        'abandoned'   => '🚫 Abandonné',
                    ] as $value => $label)
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-amber-200
                                  cursor-pointer hover:bg-amber-50 transition-colors">
                        <input type="radio" name="reading_status"
                               value="{{ $value }}"
                               class="status-radio" />
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Note</label>
                <div class="flex gap-2" id="stars-container">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button"
                            onclick="setRating({{ $i }})"
                            data-star="{{ $i }}"
                            class="star-btn text-3xl text-amber-300 hover:text-amber-500
                                   transition-colors cursor-pointer">
                        ★
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-input" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Commentaire</label>
                <textarea name="comment"
                          id="modal-comment"
                          rows="3"
                          class="w-full px-4 py-2 rounded-lg border border-amber-200 text-sm
                                 focus:outline-none focus:border-amber-400"
                          placeholder="Qu'as-tu pensé de cet item ?"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="btn-primary px-6 py-2 rounded-lg text-sm flex-1">
                    Enregistrer
                </button>
                <button type="button"
                        onclick="closeReviewModal()"
                        class="px-6 py-2 rounded-lg border border-amber-200
                               text-amber-700 text-sm hover:bg-amber-50 transition-colors">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentRating = 0;

    function openReviewModal(itemId, title) {
        document.getElementById('modal-title').textContent = title;
        document.getElementById('review-form').action = '/famille/review/' + itemId;
        document.getElementById('review-modal').classList.remove('hidden');
        document.getElementById('review-modal').classList.add('flex');
        currentRating = 0;
        updateStars(0);
        document.getElementById('rating-input').value = '';
        document.getElementById('modal-comment').value = '';
        document.querySelectorAll('.status-radio').forEach(r => r.checked = false);
    }

    function closeReviewModal() {
        document.getElementById('review-modal').classList.add('hidden');
        document.getElementById('review-modal').classList.remove('flex');
    }

    function setRating(value) {
        currentRating = value;
        document.getElementById('rating-input').value = value;
        updateStars(value);
    }

    function updateStars(value) {
        document.querySelectorAll('.star-btn').forEach(btn => {
            const star = parseInt(btn.dataset.star);
            btn.classList.toggle('text-amber-500', star <= value);
            btn.classList.toggle('text-amber-300', star > value);
        });
    }

    document.getElementById('review-modal').addEventListener('click', function(e) {
        if (e.target === this) closeReviewModal();
    });
</script>

@endsection