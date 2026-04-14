<?php

namespace Tests\Unit;

use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_overdue_helper(): void
    {
        $loan = Loan::factory()->overdue()->create();

        $this->assertTrue($loan->isOverdue());
    }

    public function test_scope_overdue_returns_only_overdue_loans(): void
    {
        Loan::factory()->create(); // not overdue
        Loan::factory()->overdue()->create();

        $overdues = Loan::overdue()->get();

        $this->assertCount(1, $overdues);
        $this->assertTrue($overdues->first()->isOverdue());
    }
}
