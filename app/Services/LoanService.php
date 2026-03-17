<?php
// app/Services/LoanService.php

namespace App\Services;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Profile;
use Illuminate\Support\Carbon;

class LoanService
{
    /**
     * Crée un prêt et met à jour le statut de l'item.
     */
    public function createLoan(Item $item, Profile $profile, array $data = []): Loan
    {
        // Vérifie que l'item est disponible
        if (! $item->isAvailable()) {
            throw new \RuntimeException(
                "L'item \"{$item->title}\" n'est pas disponible."
            );
        }

        $loan = Loan::create([
            'item_id'       => $item->id,
            'profile_id'    => $profile->id,
            'loaned_at'     => $data['loaned_at'] ?? now(),
            'due_at'        => $data['due_at'] ?? null,
            'borrower_name' => $data['borrower_name'] ?? null,
            'note'          => $data['note'] ?? null,
        ]);

        // Met à jour le statut de l'item
        $item->update(['status' => 'borrowed']);

        return $loan;
    }

    /**
     * Clôture un prêt et remet l'item disponible.
     */
    public function returnLoan(Loan $loan): Loan
    {
        if ($loan->isReturned()) {
            throw new \RuntimeException('Ce prêt est déjà clôturé.');
        }

        $loan->markAsReturned();

        // Remet l'item disponible
        $loan->item->update(['status' => 'available']);

        return $loan;
    }

    /**
     * Retourne tous les prêts en cours (non retournés).
     */
    public function getActiveLoans()
    {
        return Loan::with(['item', 'profile'])
                   ->whereNull('returned_at')
                   ->orderBy('loaned_at', 'desc')
                   ->get();
    }

    /**
     * Retourne les prêts en retard.
     */
    public function getOverdueLoans()
    {
        return Loan::with(['item', 'profile'])
                   ->whereNull('returned_at')
                   ->whereNotNull('due_at')
                   ->where('due_at', '<', now())
                   ->orderBy('due_at', 'asc')
                   ->get();
    }

    /**
     * Retourne l'historique des prêts d'un profil.
     */
    public function getLoanHistoryForProfile(Profile $profile)
    {
        return Loan::with('item')
                   ->where('profile_id', $profile->id)
                   ->orderBy('loaned_at', 'desc')
                   ->get();
    }
}