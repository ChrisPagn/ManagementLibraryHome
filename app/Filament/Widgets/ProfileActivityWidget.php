<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ProfileActivityWidget extends Widget
{
    protected static ?int $sort = 5;
    protected static string $view = 'filament.widgets.profile-activity-widget';
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $profiles = \App\Models\Profile::withCount([
            'loans',
            'loans as active_loans_count' => fn($q) => $q->whereNull('returned_at'),
            'reviews',
        ])->get()->map(function ($profile) {
            return [
                'name'         => $profile->name,
                'role'         => $profile->role,
                'avatar'       => $profile->avatar,
                'loans_total'  => $profile->loans_count,
                'loans_active' => $profile->active_loans_count,
                'reviews'      => $profile->reviews_count,
                'suggestions'  => \App\Models\ItemSuggestion::where('profile_id', $profile->id)->count(),
                'wishlist'     => \App\Models\Wishlist::where('profile_id', $profile->id)
                                      ->where('is_acquired', false)->count(),
            ];
        });

        return ['profiles' => $profiles];
    }
}