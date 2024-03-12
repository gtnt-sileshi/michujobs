<?php

namespace App\Http\Controllers;

use Chapa\Chapa\Facades\Chapa as Chapa;
use App\Http\Middleware\isEmployer;
use App\Http\Middleware\donotAllowUserToMakePayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\PurchaseMail;
use Exception;

class ChapaController extends Controller
{
    const MONTHLY_AMOUNT = 100;
    const YEARLY_AMOUNT = 1000;
    const CURRENCY = 'ETB';

    protected $reference;

    public function __construct()
    {
        $this->reference = Chapa::generateReference();
        $this->middleware(['auth', isEmployer::class]);
        $this->middleware(['auth', donotAllowUserToMakePayment::class])->except('subscribe');
    }

    public function subscribe()
    {
        return view('subscription.index');
    }


    public function initialize(Request $request)
    {

        $plans = [
            'monthly' => [
                'name' => 'monthly',
                'description' => 'monthly payment',
                'amount' => self::MONTHLY_AMOUNT,
                'currency' => self::CURRENCY,
                'quantity' => 1,
            ],
            'yearly' => [
                'name' => 'yearly',
                'description' => 'yearly payment',
                'amount' => self::YEARLY_AMOUNT,
                'currency' => self::CURRENCY,
                'quantity' => 1,
            ],
        ];


        $reference = $this->reference;

        try {

            $selectPlan = null;
            if ($request->is('pay/monthly')) {
                $selectPlan = $plans['monthly'];
                $billingEnds = now()->addMonth()->startOfDay()->toDateString();
            } elseif ($request->is('pay/yearly')) {
                $selectPlan = $plans['yearly'];
                $billingEnds = now()->addYear()->startOfDay()->toDateString();
            }
            if ($selectPlan) {
                $successURL = URL::signedRoute('payment.success', [
                    'plan' => $selectPlan['name'],
                    'billing_ends' => $billingEnds,
                ]);
                $session = Chapa::initializePayment([
                    'amount' => $selectPlan['amount'],
                    'email' => auth()->user()->email,
                    'tx_ref' => $reference,
                    'currency' => $selectPlan['currency'],
                    'callback_url' => route('callback', [$reference]),
                    'first_name' => auth()->user()->name,
                    "customization" => [
                        "title" => 'Chapa Laravel',
                        "description" => "I amma testing this"
                    ],
                    'return_url' => $successURL,
                    'cancel_url' => route('payment.cancel'),
                ]);

                return redirect($session['data']['checkout_url']);
            }
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }



    public function paymentSuccess(Request $request)
    {

        $billingEnds = $request->billing_ends;
        $plan =  $request['amp;plan'];
        User::where('id', auth()->user()->id)->update([
            'plan' => $plan,
            'billing_ends' => $billingEnds,
            'status' => 'paid'
        ]);

        try {
            Mail::to(auth()->user())->queue(new PurchaseMail($plan, $billingEnds));
        } catch (Exception $e) {
            return response()->json($e);
        }
        return redirect()->route('dashboard')->with('success', 'Payment was successfully processed');
    }

    public function cancel()
    {
        return redirect()->route('dashboard')->with('error', 'Payment was unsuccessful!');
    }


    // public function callback($reference)
    // {

    //     $data = Chapa::verifyTransaction($reference);
    //     dd($data);

    //     //if payment is successful
    //     if ($data['status'] ==  'success') {


    //         dd($data);
    //     } else {
    //         //oopsie something ain't right.
    //     }
    // }
}
