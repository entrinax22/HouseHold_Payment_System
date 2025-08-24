<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Payment;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
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
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'contribution_id' => $contribution_id,
            'amount_paid' => $request->amount_paid,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'payment_note' => $request->payment_note,
            'payment_status' => 'paid', 
        ]);

        $contribution = Contribution::find($contribution_id);
        if ($contribution) {
            $contribution->payment_status = 'paid';
            $contribution->save();
        }

        return response()->json([
            'result' => true,
            'message' => 'Cash Payment Successful!'
        ]);
    }

    public function list(Request $request)
    {
        try {
            $search = $request->search;
            $query = Payment::with('user', 'contribution'); 

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
                    'user_name'         => $item->user ? $item->user->name : 'Community',
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

}
