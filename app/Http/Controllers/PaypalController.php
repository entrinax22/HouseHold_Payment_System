<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PaypalController extends Controller
{
    public function createPayment(Request $request)
    {
        $paypal = new PayPalClient;
        $paypal->setApiCredentials(config('paypal'));
        $token = $paypal->getAccessToken();
        $paypal->setAccessToken($token);

        $order = $paypal->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "PHP",
                        "value" => $request->amount_paid,
                    ]
                ]
            ],
            "application_context" => [
                "cancel_url" => route('paypal.cancel'),
                "return_url" => route('paypal.success', [
                    'contribution_id' => $request->contribution_id
                ])
            ]
        ]);

        if (isset($order['id']) && $order['id'] != null) {
            foreach ($order['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return response()->json([
                        'approval_url' => $link['href']
                    ]);
                }
            }
        }

        return response()->json(['error' => 'Unable to create PayPal order'], 500);
    }


    public function success(Request $request)
    {
        $paypal = new PayPalClient;
        $paypal->setApiCredentials(config('paypal'));
        $token = $paypal->getAccessToken();
        $paypal->setAccessToken($token);

        $result = $paypal->capturePaymentOrder($request->token);

        if (isset($result['status']) && $result['status'] === 'COMPLETED') {
            // Save to DB
            $contribution_id = decrypt($request->contribution_id);
            Payment::create([
                'contribution_id' => $contribution_id, 
                'user_id' => Auth::id(),
                'amount_paid' => $result['purchase_units'][0]['payments']['captures'][0]['amount']['value'],
                'payment_status' => 'paid',
                'payment_date' => now(),
                'payment_method' => 'paypal',
                'payment_note' => $result['id'],
            ]);
            
            $contribution = Contribution::find($contribution_id);
            if ($contribution) {
                $contribution->payment_status = 'paid';
                $contribution->save();
            }

            return redirect()->route('home')->with('success', 'Payment successful!');
        }

        return redirect()->route('paypal.cancel')->with('error', 'Payment failed.');
    }

    public function cancel()
    {
        return redirect()->route('dashboard')->with('error', 'You have canceled the payment.');
    }
}
