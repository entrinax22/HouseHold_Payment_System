<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Payment;
use App\Models\Contribution;
use Illuminate\Http\Request;
use App\Services\SemaphoreService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\CommunityContributionPayment;

class PaymentController extends Controller
{   
    protected $semaphore;

    public function __construct(SemaphoreService $semaphore)
    {
        $this->semaphore = $semaphore;
    }

    public function index()
    {
        return Inertia::render('payments/PaymentsTable');
    }

    public function create($contribution_id)
    {   
        $contribution_id = decrypt($contribution_id);
        $contribution = Contribution::findOrFail($contribution_id);

        return Inertia::render('payments/Create', [
            'contribution' => [
                'contribution_id' => encrypt($contribution->contribution_id),
                'description' => $contribution->description,
                'amount' => $contribution->amount,
                'payment_status' => $contribution->payment_status,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payer' => 'required|string|max:255',
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:Paypal,Cash',
            'payment_note' => 'nullable|string',
            'contribution_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $contribution_id = decrypt($request->contribution_id);
        $contribution = Contribution::find($contribution_id);
        if (!$contribution) {
            return response()->json([
                'result' => false,
                'message' => 'Contribution not found'
            ], 404);
        }

        $payment = Payment::create([
            'user_id' => Auth::id(),
            'contribution_id' => $contribution_id,
            'amount_paid' => $request->amount_paid,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'payment_note' => $request->payment_note,
            'payment_status' => 'paid', 
        ]);

        // Handle payment status updates differently for community vs personal contributions
        if ($contribution->contribution_type === 'community') {
            // For community contributions, track individual payment status
            CommunityContributionPayment::updateOrCreate(
                [
                    'contribution_id' => $contribution_id,
                    'user_id' => Auth::id(),
                ],
                [
                    'payment_status' => 'paid'
                ]
            );

            // Check if all participants have paid (use participants relation if available)
            $participantCount = $contribution->participants()->count();
            // Fallback to all users if no participants defined
            if ($participantCount === 0) {
                $participantCount = User::count();
            }

            $paidUsers = CommunityContributionPayment::where('contribution_id', $contribution_id)
                ->where('payment_status', 'paid')
                ->count();

            if ($participantCount > 0 && $paidUsers >= $participantCount) {
                $contribution->payment_status = 'paid';
                $contribution->save();
            }
        } else {
            // For personal contributions, update status directly
            $contribution->payment_status = 'paid';
            $contribution->save();
        }

        $user = Auth::user();
        $number = $user->mobile_number ?? '09517995409';
        $message = "Thank you, {$user->name}, for your payment of ₱{$request->amount_paid} via {$request->payment_method}. - Trinidad Smart Homes";

        $response = $this->semaphore->sendSMS($number, $message);

        return response()->json([
            'result' => true,
            'message' => 'Cash Payment Successful!',
            'response' => $response
        ]);
    }

    public function list(Request $request)
    {
        try {
            $search = $request->search;
            $query = Payment::with(['user', 'contribution' => function($q) {
                $q->with('communityPayments');
            }]); 

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('amount_paid', 'like', "%{$search}%")
                    ->orWhere('payment_status', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%");
                });
            }

            $query->orderBy('payment_id', 'desc');

            $payments = $query->paginate(10);

            $data = $payments->getCollection()->transform(function ($item) {
                return [
                    'payment_id'        => encrypt($item->payment_id),
                    'contribution_id'   => encrypt($item->contribution_id),
                    'user_id'           => $item->user ? encrypt($item->user->id) : null,
                    'user_name'         => $item->user ? $item->user->name : null,
                    'is_community'      => $item->contribution ? $item->contribution->contribution_type === 'community' : false,
                    'amount_paid'       => $item->amount_paid,
                    'payment_status'    => $item->payment_status,
                    'payment_method'    => $item->payment_method,
                    'description'       => $item->contribution ? $item->contribution->description : null,
                    'contribution_date' => $item->contribution ? $item->contribution->contribution_date : null,
                    'contribution_type' => $item->contribution ? $item->contribution->contribution_type : null,
                ];
            });

            return response()->json([
                'result'     => true,
                'data'       => $data,
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page'    => $payments->lastPage(),
                    'per_page'     => $payments->perPage(),
                    'total'        => $payments->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'Failed to get payment lists. ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($payment_id)
    {
        try{
            $payment_id = decrypt($payment_id);
            $payment = Payment::with('user', 'contribution')->findOrFail($payment_id);

            $encryptedUserId = User::pluck('id')->mapWithKeys(function ($id) {
                return [encrypt($id) => $id];
            });

            $users = User::whereIn('id', $encryptedUserId->values())->get()->map(function ($user) use ($encryptedUserId) {
                return [
                    'id' => $encryptedUserId->flip()[$user->id],
                    'name' => $user->name,
                ];
            });

            $data = [
                'payment_id'        => encrypt($payment->payment_id),
                'contribution_id'   => encrypt($payment->contribution_id),
                'user_id'           => $payment->user ? $encryptedUserId->flip()[$payment->user->id]: null,
                'user_name'         => $payment->user ? $payment->user->name : 'Community',
                'amount_paid'       => $payment->amount_paid,
                'payment_status'    => $payment->payment_status,
                'payment_method'    => $payment->payment_method,
                'payment_date'      => $payment->payment_date,
                'payment_note'      => $payment->payment_note,
                'description'       => $payment->contribution ? $payment->contribution->description : null,
                'contribution_date' => $payment->contribution ? $payment->contribution->contribution_date : null,
                'contribution_type' => $payment->contribution ? $payment->contribution->contribution_type : null,
            ];

            return response()->json([
                'result' => true,
                'data'   => $data,
                'users' => $users
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'Failed to get payment data. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'payment_id' => 'required|string',
                'amount_paid' => 'required|numeric|min:0',
                'payment_date' => 'required|date',
                'payment_method' => 'required|string|in:paypal,cash',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $payment_id = decrypt($request->payment_id);
            $payment = Payment::findOrFail($payment_id);

            $payment->amount_paid = $request->amount_paid;
            $payment->payment_date = $request->payment_date;
            $payment->payment_method = $request->payment_method;
            $payment->payment_note = $request->description;
            $payment->save();

            // Return encrypted payment data for consistency
            $updatedData = [
                'payment_id' => encrypt($payment->payment_id),
                'contribution_id' => encrypt($payment->contribution_id),
                'amount_paid' => $payment->amount_paid,
                'payment_date' => $payment->payment_date,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
            ];

            return response()->json([
                'result' => true,
                'message' => 'Payment updated successfully!',
                'data' => $updatedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to update payment. ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($payment_id){
        try{
            $paymentId = decrypt($payment_id);
            $payment = Payment::findOrFail($paymentId);
            $payment->delete();

            return response()->json([
                'result' => true,
                'message' => 'Payment deleted successfully!'
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to delete payment. ' . $e->getMessage()
            ], 500);
        }
    }
}
