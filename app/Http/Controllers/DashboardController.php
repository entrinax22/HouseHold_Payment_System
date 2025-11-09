<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $contributions = Contribution::with(['payments', 'communityPayments', 'participants'])->get();

            // Get total of all personal contributions
            $personalTotal = $contributions
                ->where('contribution_type', 'personal')
                ->sum('amount');

            // Get total of all community contributions (multiplied by participants)
            $communityTotal = $contributions
                ->where('contribution_type', 'community')
                ->sum(function ($contribution) {
                    $participantCount = $contribution->participants()->count() ?: User::count();
                    return $contribution->amount * $participantCount;
                });

            $totalContributions = $personalTotal + $communityTotal;

            // Only sum confirmed payments
            $totalPayments = Payment::where('payment_status', 'paid')
                ->sum('amount_paid');

            // Count fully paid contributions
            $totalPaidContributions = $contributions->filter(function ($contribution) {
                if ($contribution->contribution_type === 'community') {
                    // For community contributions, check if there are any payments
                    return $contribution->payments()
                        ->where('payment_status', 'paid')
                        ->exists();
                } else {
                    // For personal contributions, total payments must meet or exceed amount
                    $paidAmount = $contribution->payments()
                        ->where('payment_status', 'paid')
                        ->sum('amount_paid');
                    return $paidAmount >= $contribution->amount;
                }
            })->count();

            // Only count payments marked as pending
            $pendingPayments = Payment::where('payment_status', 'pending')
                ->sum('amount_paid');

            $monthlyPayments = Payment::select(
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount_paid) as total')
            )
                ->whereYear('payment_date', Carbon::now()->year)
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $months = collect(range(1, 12))->map(function ($m) use ($monthlyPayments) {
                return [
                    'label' => Carbon::create()->month($m)->shortMonthName,
                    'total' => $monthlyPayments[$m] ?? 0,
                ];
            });

            $recentPayments = Payment::with('user', 'contribution')
                ->latest('payment_date')
                ->take(5)
                ->get()
                ->map(function ($payment) {
                    return [
                        'payer' => $payment->user->name ?? 'Unknown',
                        'amount' => (float) $payment->amount_paid,
                        'method' => $payment->payment_method,
                        'status' => $payment->payment_status,
                        'date' => Carbon::parse($payment->payment_date)->format('M d, Y'),
                    ];
                });

            return response()->json([
                'result' => true,
                'data' => [
                    'totals' => [
                        'total_contributions' => number_format($totalContributions, 2, '.', ''),
                        'total_payments' => number_format($totalPayments, 2, '.', ''),
                        'paid_contributions' => $totalPaidContributions,
                        'pending_payments' => number_format($pendingPayments, 2, '.', ''),
                    ],
                    'chart' => [
                        'labels' => $months->pluck('label'),
                        'data' => $months->pluck('total'),
                    ],
                    'recent' => $recentPayments,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error fetching dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
