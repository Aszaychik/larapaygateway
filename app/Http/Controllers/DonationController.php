<?php

namespace App\Http\Controllers;

use App\Donation;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
    Config::$serverKey = config('services.midtrans.serverKey');
    Config::$isProduction = config('services.midtrans.isProduction');
    Config::$isSanitized = config('services.midtrans.isSanitized');
    Config::$is3ds = config('services.midtrans.is3ds');
    }

    public function index()
    {
        $donations = Donation::orderBy('id', 'desc')->paginate(8);
        return view('welcome', compact('donations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('donation');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \DB::transaction(function () use($request){
            $donation = Donation::create([
                'donation_code' => 'ASZ-' . uniqid(),
                'name' => $request->name,
                'email' => $request->email,
                'donation_type' => $request->donation_type,
                'amount' => floatval($request->amount),
                'note' => $request->note,
            ]);

            $payload = [
                'transaction_details' => [
                    'order_id'      => $donation->donation_code,
                    'gross_amount'  => $donation->amount,
                ],
                'customer_details' => [
                    'name'      => $donation->name,
                    'email'     => $donation->email,
                    // 'phone'         => '08888888888',
                    // 'address'       => '',
                ],
                'item_details' => [
                    [
                        'id'       => $donation->donation_type,
                        'price'    => $donation->amount,
                        'quantity' => 1,
                        'name'     => ucwords(str_replace('_', ' ', $donation->donation_type))
                    ]
                ]
            ];

            $snapToken = Snap::getSnapToken($payload);
            $donation->snap_token = $snapToken;
            $donation->save();

            $this->response['snap_token'] = $snapToken;
        });

        return response()->json($this->response);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function show(Donation $donation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function edit(Donation $donation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Donation $donation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Donation $donation)
    {
        //
    }

    public function notification()
    {
        $notif = new Notification();
        \DB::transaction(function() use($notif) {

          $transactionStatus = $notif->transaction_status;
          $paymentType = $notif->payment_type;
          $orderId = $notif->order_id;
          $fraud = $notif->fraud_status;
          $donation = Donation::where('donation_code', $orderId)->first();

          if ($transactionStatus == 'capture') {
            if ($paymentType == 'credit_card') {

              if($fraud == 'challenge') {
                $donation->setStatusPending();
              } else {
                $donation->setStatusSuccess();
              }

            }
          } elseif ($transactionStatus == 'settlement') {

            $donation->setStatusSuccess();

          } elseif($transactionStatus == 'pending'){

              $donation->setStatusPending();

          } elseif ($transactionStatus == 'deny') {

              $donation->setStatusFailed();

          } elseif ($transactionStatus == 'expire') {

              $donation->setStatusExpired();

          } elseif ($transactionStatus == 'cancel') {

              $donation->setStatusFailed();

          }

        });

        return;
    }
}
