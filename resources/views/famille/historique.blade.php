@extends('famille.layout')

@section('title', 'Historique')

@section('content')

<h2 class="font-playfair text-3xl text-amber-900 mb-6">📖 Mon historique de lecture</h2>

@if($loans->isEmpty())
    <div class="card-library p-12 text-center">
        <p class="text-5xl mb-4">📭</p>
        <p class="text-gray-500">Aucun historique pour le moment.</p>
    </div>
@else
    <div class="card-library overflow-hidden">
        <table class="w-full">
            <thead class="bg-amber-50 border-b border-amber-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs text-amber-700 uppercase tracking-wider">Item</th>
                    <th class="text-left px-6 py-3 text-xs text-amber-700 uppercase tracking-wider">Type</th>
                    <th class="text-left px-6 py-3 text-xs text-amber-700 uppercase tracking-wider">Emprunté le</th>
                    <th class="text-left px-6 py-3 text-xs text-amber-700 uppercase tracking-wider">Rendu le</th>
                    <th class="text-left px-6 py-3 text-xs text-amber-700 uppercase tracking-wider">Durée</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-amber-50">
                @foreach($loans as $loan)
                <tr class="hover:bg-amber-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800 text-sm">{{ $loan->item->title }}</p>
                        <p class="text-xs text-gray-400">{{ $loan->item->author }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-700">
                            {{ $loan->item->type->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $loan->loaned_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $loan->returned_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $loan->loaned_at->diffInDays($loan->returned_at) }} jours
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $loans->links() }}
    </div>
@endif

@endsection