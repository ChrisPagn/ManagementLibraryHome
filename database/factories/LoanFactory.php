<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $loanedAt = Carbon::now()->subDays(3);

        return [
            'item_id' => Item::factory(),
            'profile_id' => Profile::factory(),
            'loaned_at' => $loanedAt,
            'due_at' => $loanedAt->copy()->addDays(14),
            'returned_at' => null,
            'borrower_name' => null,
            'note' => $this->faker->optional()->sentence(),
        ];
    }

    public function returned(): self
    {
        return $this->state(fn () => ['returned_at' => Carbon::now()]);
    }

    public function overdue(): self
    {
        return $this->state(function () {
            $loaned = Carbon::now()->subDays(30);
            return [
                'loaned_at' => $loaned,
                'due_at' => $loaned->copy()->addDays(7),
                'returned_at' => null,
            ];
        });
    }
}
