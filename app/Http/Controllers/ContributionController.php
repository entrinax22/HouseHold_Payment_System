<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Contribution;
use Illuminate\Http\Request;
use App\Services\SemaphoreService;
use Illuminate\Support\Facades\Auth;

class ContributionController extends Controller
{
    protected $semaphore;

    public function __construct(SemaphoreService $semaphore){
        $this->semaphore = $semaphore;
    }

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
            $contribution = Contribution::create($validated);

            if ($contribution->contribution_type === 'personal' && $contribution->user_id) {
                $user = User::find($contribution->user_id);
    
                if ($user && $user->mobile_number) {
                    $number = $user->mobile_number;
                    $message = "Hello {$user->name}, your personal contribution of ₱{$contribution->amount} "
                        . "has been recorded. Please proceed with your payment at your earliest convenience. "
                        . "Thank you for your support!";
    
                    $this->semaphore->sendSMS($number, $message);
                }
            }
            
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

    public function list(Request $request)
    {
        try {
            $search = $request->search;
            $query = Contribution::with('user'); 

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('amount', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $query->orderBy('contribution_id', 'desc');

            $contributions = $query->paginate(10);

            $data = $contributions->getCollection()->transform(function ($item) {
                return [
                    'contribution_id'   => encrypt($item->contribution_id),
                    'user_id'           => encrypt($item->user_id),
                    'user_name'         => $item->user ? $item->user->name : 'Community',
                    'amount'            => $item->amount,
                    'contribution_date' => $item->contribution_date,
                    'contribution_type' => $item->contribution_type,
                    'payment_status'    => $item->payment_status,
                    'description'       => $item->description,
                    'status'            => $item->status,
                ];
            });

            return response()->json([
                'result'     => true,
                'data'       => $data,
                'pagination' => [
                    'current_page' => $contributions->currentPage(),
                    'last_page'    => $contributions->lastPage(),
                    'per_page'     => $contributions->perPage(),
                    'total'        => $contributions->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'Failed to get contribution lists. ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($contribution_id)
    {
        try {
            $decryptedId = decrypt($contribution_id);
            $contribution = Contribution::findOrFail($decryptedId);

            $encryptedUsersId = User::pluck('id')->mapWithKeys(function ($id){
                return [$id => encrypt($id)];
            });
            
            $users = User::orderBy('id')->get()->map(function ($user) use ($encryptedUsersId) {
                return [
                    'user_id' => $encryptedUsersId[$user->id],
                    'name'    => $user->name,
                    'email'   => $user->email,
                ];
            });

            return response()->json([
                'result' => true,
                'data'   => [
                    'contribution' => [
                        'contribution_id'   => encrypt($contribution->contribution_id),
                        'user_id'           => $contribution->user_id ? $encryptedUsersId[$contribution->user_id] : 'Community',
                        'amount'            => $contribution->amount,
                        'contribution_date' => $contribution->contribution_date,
                        'contribution_type' => $contribution->contribution_type,
                        'status'            => $contribution->status,
                        'description'       => $contribution->description,
                    ],
                    'users' => $users,
                ],
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load contribution for editing: ' . $e->getMessage());
        }
    }

    public function update(Request $request){
        try{
            $validated = $request->validate([
                'contribution_id'   => 'required|string',
                'user_id'           => 'nullable|string', 
                'amount'            => 'required|numeric|min:0',
                'contribution_date' => 'required|date',
                'contribution_type' => 'required|in:community,personal',
                'status'            => 'required|in:active,disabled',
                'description'       => 'nullable|string|max:255',
            ]);

            // Decrypt the contribution_id
            $decryptedContributionId = decrypt($validated['contribution_id']);
            $contribution = Contribution::findOrFail($decryptedContributionId);

            // Decrypt the user_id if provided
            if ($request->filled('user_id')) {
                $validated['user_id'] = decrypt($request->user_id);

                if (!User::find($validated['user_id'])) {
                    return response()->json([
                        'result'  => false,
                        'message' => 'Invalid user ID.',
                    ], 422);
                }
            } else {
                $validated['user_id'] = null; // Set to null if not provided
            }

            // Update contribution
            $contribution->update($validated);

            if ($contribution->contribution_type === 'personal' && $contribution->user_id) {
                $user = User::find($contribution->user_id);

                if ($user && $user->mobile_number) {
                    $number = $user->mobile_number;
                    $message = "Hello {$user->name}, your personal contribution of ₱{$contribution->amount} "
                    . "has been updated. Please proceed with your payment at your earliest convenience. "
                    . "Thank you for your support!";

                    $this->semaphore->sendSMS($number, $message);
                }
            }

            return response()->json([
                'result'  => true,
                'message' => 'Contribution updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'Failed to update contribution: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($contribution_id)
    {
        try {
            $decryptedId = decrypt($contribution_id);
            $contribution = Contribution::findOrFail($decryptedId);
            $contribution->delete();

            return response()->json([
                'result'  => true,
                'message' => 'Contribution deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'Failed to delete contribution: ' . $e->getMessage()
            ], 500);
        }
    }
}
