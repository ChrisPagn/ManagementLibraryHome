<?php
// app/Console/Commands/SendLoanReminders.php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Mail\LoanReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLoanReminders extends Command
{
    protected $signature   = 'loans:reminders';
    protected $description = 'Envoie des rappels pour les prêts qui arrivent à échéance';

    public function handle(): void
    {
        // Prêts qui arrivent à échéance dans 3 jours
        $soonDue = Loan::with(['item', 'profile'])
                       ->whereNull('returned_at')
                       ->whereNotNull('due_at')
                       ->whereBetween('due_at', [now(), now()->addDays(3)])
                       ->get();

        // Prêts en retard
        $overdue = Loan::with(['item', 'profile'])
                       ->whereNull('returned_at')
                       ->whereNotNull('due_at')
                       ->where('due_at', '<', now())
                       ->get();

        $total = $soonDue->count() + $overdue->count();

        if ($total === 0) {
            $this->info('Aucun rappel à envoyer.');
            return;
        }

        // Log les rappels (en prod on enverrait des emails/notifications)
        foreach ($soonDue as $loan) {
            Log::info("RAPPEL: « {$loan->item->title} » doit être rendu dans 3 jours "
                    . "par {$loan->profile->name}");
            $this->info("Rappel J-3 : {$loan->item->title} → {$loan->profile->name}");
        }

        foreach ($overdue as $loan) {
            Log::warning("EN RETARD: « {$loan->item->title} » était dû le "
                       . "{$loan->due_at->format('d/m/Y')} "
                       . "— {$loan->profile->name}");
            $this->warn("En retard : {$loan->item->title} → {$loan->profile->name}");
        }

        $this->info("✅ {$total} rappel(s) traité(s).");
    }
}