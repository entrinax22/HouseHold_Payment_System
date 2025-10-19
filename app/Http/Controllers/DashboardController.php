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
            $totalContributions = Contribution::sum('amount');
            $totalPayments = Payment::sum('amount_paid');
            $totalPaidContributions = Contribution::all()->filter->isPaid()->count();
            $pendingPayments = Payment::where('payment_status', 'pending')->sum('amount_paid');

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
