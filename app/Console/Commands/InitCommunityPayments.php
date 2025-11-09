<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contribution;
use App\Models\Payment;
use App\Models\CommunityContributionPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InitCommunityPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --add-all-users : also create unpaid rows for all users for each community contribution (use with caution)
     */
    protected $signature = 'contributions:init-community-payments {--add-all-users : create unpaid rows for all users for each community contribution} {--force : skip confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Initialize missing community_contribution_payments records from existing payments and optionally add unpaid rows for all users.';

    public function handle()
    {
        $addAllUsers = $this->option('add-all-users');
        $force = $this->option('force');

        if ($addAllUsers && !$force) {
            if (! $this->confirm('This will create unpaid community payment records for ALL users for each community contribution. Continue?')) {
                $this->info('Aborted.');
                return 0;
            }
        }

        $this->info('Scanning community contributions...');

        $contributions = Contribution::where('contribution_type', 'community')->get();
        $created = 0;
        $skipped = 0;
        DB::beginTransaction();
        try {
            foreach ($contributions as $contribution) {
                // 1) Ensure community payment rows exist for any existing payments
                $payments = Payment::where('contribution_id', $contribution->contribution_id)->get();
                foreach ($payments as $payment) {
                    $exists = CommunityContributionPayment::where('contribution_id', $contribution->contribution_id)
                        ->where('user_id', $payment->user_id)
                        ->exists();

                    if (! $exists) {
                        CommunityContributionPayment::create([
                            'contribution_id' => $contribution->contribution_id,
                            'user_id' => $payment->user_id,
                            'payment_status' => $payment->payment_status ?? 'paid'
                        ]);
                        $created++;
                    } else {
                        $skipped++;
                    }
                }

                // 2) Optionally create unpaid rows for participants (if requested). Falls back to all users only if --add-all-users is used and no participants exist.
                if ($addAllUsers) {
                    $participants = $contribution->participants()->exists() ? $contribution->participants : User::all();
                    foreach ($participants as $user) {
                        $exists = CommunityContributionPayment::where('contribution_id', $contribution->contribution_id)
                            ->where('user_id', $user->id)
                            ->exists();

                        if (! $exists) {
                            CommunityContributionPayment::create([
                                'contribution_id' => $contribution->contribution_id,
                                'user_id' => $user->id,
                                'payment_status' => 'unpaid'
                            ]);
                            $created++;
                        } else {
                            $skipped++;
                        }
                    }
                }
            }

            DB::commit();
            $this->info(sprintf('Done. Created: %d, Skipped (already existed): %d', $created, $skipped));
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
