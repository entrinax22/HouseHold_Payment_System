<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContributionController extends Controller
{
    public function index()
    {
        return Inertia::render('contributions/ContributionsTable');
    }

    public function userContributions(){
        return Inertia::render('contributions/MyContributions');
    }

    public function create()
    {
        return Inertia::render('contributions/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'           => 'nullable|string', 
            'amount'            => 'required|numeric|min:0',
            'contribution_date' => 'required|date',
            'contribution_type' => 'required|in:community,personal',
            'status'            => 'required|in:active,disabled',
            'description'       => 'nullable|string|max:255',
        ]);

        try {
            // Decrypt the user_id if provided
            if ($request->filled('user_id')) {
                $validated['user_id'] = decrypt($request->user_id);

                if (!User::find($validated['user_id'])) {
                    return response()->json([
                        'result'  => false,
                        'message' => 'Invalid user ID.',
                    ], 422);
                }
            }
            // Save contribution
            $contribution = Contribution::create($validated);

            return response()->json([
                'result'  => true,
                'message' => 'Contribution created successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'Failed to save contribution: ' . $e->getMessage()
            ], 500);
        }
    }

    public function userContributionList()
    {   
        $user_id = Auth::id();

        $contributions = Contribution::with('payments')
            ->where('status', 'active')
            ->where(function ($query) use ($user_id) {
                $query->where('contribution_type', 'community')
                    ->orWhere('user_id', $user_id);
            })
            ->get()
            ->map(function ($contribution) {
                return [
                    'contribution_id'   => encrypt($contribution->contribution_id),
                    'amount'            => $contribution->amount,
                    'contribution_type' => $contribution->contribution_type,
                    'contribution_date' => $contribution->contribution_date,
                    'description'       => $contribution->description,
                    'status'            => $contribution->status,
                    'payment_status'    => $contribution->payment_status,
                ];
            });

        return response()->json([
            'result'  => true,
            'data'    => $contributions,
            'message' => 'User contributions retrieved successfully.'
        ]);
    }
}
