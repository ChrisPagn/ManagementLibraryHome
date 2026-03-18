<?php
// app/Http/Controllers/FamilleController.php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Item;
use App\Models\ItemSuggestion;
use App\Models\Loan;
use App\Models\Profile;
use App\Models\ItemReview;
use Illuminate\Http\Request;

class FamilleController extends Controller
{
    // ── Sélection du profil ───────────────────────────────────
    public function index()
    {
        $profiles = Profile::all();
        return view('famille.index', compact('profiles'));
    }

    // ── Affiche le formulaire PIN ─────────────────────────────
    public function showPin(Profile $profile)
    {
        return view('famille.pin', compact('profile'));
    }

    // ── Vérifie le PIN ────────────────────────────────────────
    public function verifyPin(Request $request, Profile $profile)
    {
        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        // Profil sans PIN → accès direct
        if (empty($profile->pin) || $profile->checkPin($request->pin)) {
            session(['active_profile_id' => $profile->id]);
            return redirect()->route('famille.home');
        }

        return back()->withErrors(['pin' => 'Code PIN incorrect.']);
    }

    // ── Déconnexion du profil ─────────────────────────────────
    public function logout()
    {
        session()->forget('active_profile_id');
        return redirect()->route('famille.index');
    }

    // ── Accueil membre ────────────────────────────────────────
    public function home()
    {
        $profile = $this->getActiveProfile();

        $activeLoans = Loan::with('item')
                        ->where('profile_id', $profile->id)
                        ->whereNull('returned_at')
                        ->orderBy('due_at')
                        ->get();

        $overdueLoans = $activeLoans->filter(fn($l) => $l->isOverdue());

        // ── Correction : on retire le orWhereHas('loans') ──────────
        $myCollections = Collection::with(['items', 'type'])
                                ->whereHas('items', function ($q) use ($profile) {
                                    $q->where('owner_profile_id', $profile->id);
                                })
                                ->limit(5)
                                ->get();

        $recentItems = Item::where('owner_profile_id', $profile->id)
                        ->latest()
                        ->limit(6)
                        ->get();

        return view('famille.home', compact(
            'profile', 'activeLoans', 'overdueLoans',
            'myCollections', 'recentItems'
        ));
    }

    // ── Médiathèque complète (lecture seule) ──────────────────
    public function mediatheque(Request $request)
    {
        $profile = $this->getActiveProfile();

        $items = Item::with(['type', 'tags', 'owner'])
                     ->when($request->search, fn($q) =>
                         $q->where('title', 'like', '%'.$request->search.'%')
                           ->orWhere('author', 'like', '%'.$request->search.'%')
                     )
                     ->when($request->type, fn($q) =>
                         $q->where('item_type_id', $request->type)
                     )
                     ->orderByRaw("owner_profile_id = ? DESC", [$profile->id])
                     ->orderBy('title')
                     ->paginate(20);

        return view('famille.mediatheque', compact('profile', 'items'));
    }

    // ── Prêts en cours ────────────────────────────────────────
    public function mesPrets()
    {
        $profile = $this->getActiveProfile();

        $loans = Loan::with('item')
                     ->where('profile_id', $profile->id)
                     ->whereNull('returned_at')
                     ->orderBy('due_at')
                     ->get();

        return view('famille.prets', compact('profile', 'loans'));
    }

    // ── Historique ────────────────────────────────────────────
    public function historique()
    {
        $profile = $this->getActiveProfile();

        $loans = Loan::with('item')
                     ->where('profile_id', $profile->id)
                     ->whereNotNull('returned_at')
                     ->orderBy('returned_at', 'desc')
                     ->paginate(20);

        return view('famille.historique', compact('profile', 'loans'));
    }

    // ── Collections suivies ───────────────────────────────────
    public function collections()
    {
        $profile     = $this->getActiveProfile();
        $collections = Collection::with(['items', 'type'])->get();

        return view('famille.collections', compact('profile', 'collections'));
    }

    // ── Suggestion d'item ─────────────────────────────────────
    public function suggestionForm()
    {
        $profile = $this->getActiveProfile();
        return view('famille.suggestion', compact('profile'));
    }

    public function suggestionStore(Request $request)
    {
        $request->validate([
            'title'  => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn'   => 'nullable|string|max:20',
            'note'   => 'nullable|string|max:500',
        ]);

        $profile = $this->getActiveProfile();

        ItemSuggestion::create([
            'profile_id' => $profile->id,
            'title'      => $request->title,
            'author'     => $request->author,
            'isbn'       => $request->isbn,
            'note'       => $request->note,
            'status'     => 'pending',
        ]);

        return redirect()
            ->route('famille.home')
            ->with('success', 'Suggestion envoyée ! L\'admin la traitera bientôt. 📚');
    }

    // ── Helper privé ──────────────────────────────────────────
    private function getActiveProfile(): Profile
    {
        return Profile::findOrFail(session('active_profile_id'));
    }

    /** ── Enregistrement d'un avis sur un item ─────────────────────
     * Permet à un membre de laisser un avis (statut de lecture, note, commentaire)
     * sur un item qu'il a emprunté ou lu.
     */
    public function storeReview(Request $request, Item $item)
    {
        $request->validate([
            'reading_status' => 'nullable|in:to_read,in_progress,completed,abandoned',
            'rating'         => 'nullable|integer|min:1|max:5',
            'comment'        => 'nullable|string|max:1000',
        ]);

        $profile = $this->getActiveProfile();

        // Crée ou met à jour l'avis
        ItemReview::updateOrCreate(
            [
                'item_id'    => $item->id,
                'profile_id' => $profile->id,
            ],
            [
                'reading_status' => $request->reading_status,
                'rating'         => $request->rating,
                'comment'        => $request->comment,
            ]
        );

        return back()->with('success', 'Ton avis a été enregistré ! ✅');
    }
}