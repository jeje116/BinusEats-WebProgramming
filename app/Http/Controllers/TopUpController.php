<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\TopUp;
use App\Models\TopUpPaymentMethod;
use App\Models\TopUpTransaction;
use App\Models\Money;
use App\Models\Notification;
use App\Models\MidtransTransaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TopUpController extends Controller
{
    public function activeEmoney($id, $emoney_name_data, $order_id = null){

        $this->checkPendingStatusMidtrans($id);

        $user = User::find($id);
        $emoney = TopUp::where("name", $emoney_name_data)->get();
        $money = Money::where("emoney_id", $emoney[0]->id)->get();
        $method = TopUpPaymentMethod::all();
        $transaction = TopUpTransaction::where("user_id", $id)->get()->sortByDesc('time');
        $transaction_emoney = TopUp::all();

        $midtrans_transactions = MidtransTransaction::latest()->where('pending_status', 'no')->get();
        $snap_token = null;
        
        if ($midtrans_transactions->count()) {
            $midtrans_transactions = MidtransTransaction::latest()->where('pending_status', 'no')->first();
            $snap_token = $midtrans_transactions->snap_token;
        }

        foreach($transaction as $tr){
            $tr->transaction_date= date('d F Y', strtotime($tr->time));
            $tr->transaction_time = date('H:i', strtotime($tr->time));
            $tr->transaction_day = date('l', strtotime($tr->time));
        }

        foreach($money as $m){
            $m->formattedPrice = number_format($m->totalAmount, 0, ',', '.');
        }

        $unread_status = Notification::where("user_id", $id)->where("clicked_status", 1)->get();
        $unread_notif_count = count($unread_status);

        return view('user_page.main_content.topup', [
            'page_title' => 'Topup | BinusEats',
            'active' => $emoney[0]->id,
            'user' => $user,
            'emoney' => $emoney,
            'money' => $money,
            'method' => $method,
            'transaction' => $transaction,
            'tr_emone' => $transaction_emoney,
            'active_number' => 0,
            'unread_notif_count' => $unread_notif_count,
            'snap_token' => $snap_token,
            'order_id' => $order_id
        ])->with('id', $id);
    }

    public function midtransProcess(Request $request) {

        $user = User::find($request->user_id);

        $emoney = TopUp::find($request->emoney_id);

        $order_id = Str::orderedUuid()->toString();

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
        
        $params = array(
            'transaction_details' => array(
                'order_id' => $order_id,
                'gross_amount' => $request->amount,
            ),
            'customer_details' => array(
                'first_name' => $user->name,
                'email' => $user->email
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        
        MidtransTransaction::create([
            'user_id' => $request->user_id,
            'emoney_id' => $request->emoney_id,
            'order_id' => $order_id,
            'snap_token' => $snapToken,
            'pending_status' => 'no'
        ]);

        return $this->activeEmoney($request->user_id, $emoney->name, $order_id);
    }

    private function updateUser($user_id, $emoney_id, $amount) {
        $money = Money::where('user_id', $user_id)->get();
        foreach($money as $m){
            if($m['emoney_id']==$emoney_id){
                $m->totalAmount += $amount;
                $m->save();
                break;
            }
        }
    }

    function addIntoTopUpTransaction($user_id, $emoney_id, $amount) {
        TopUpTransaction::insert([
            'user_id' => $user_id,
            'emoney_id' => $emoney_id,
            'amount' => $amount,
            'method' => "Top Up",
            'time' => now()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
        ]);
    }
    
    function finishMidtrans(Request $request, $user_id) {

        $server_key = config('midtrans.server_key');

        $midtrans_transactions = MidtransTransaction::where('order_id', $request->order_id)->first();

        $response = Http::withBasicAuth($server_key, '')
            ->get('https://api.sandbox.midtrans.com/v2/'.$request->order_id.'/status');

        $temp = $response->json();

        $emoney = TopUp::find( $midtrans_transactions->emoney_id);
        
        if ($temp['transaction_status'] == 'capture' || $temp['transaction_status'] == 'settlement') {
            $this->updateUser($user_id, $midtrans_transactions->emoney_id, $temp['gross_amount']);
            $this->addIntoTopUpTransaction($user_id, $midtrans_transactions->emoney_id, $temp['gross_amount']);
            $midtrans_transactions->delete();
        }
        elseif ($temp['transaction_status'] == 'pending') {
            $midtrans_transactions->update([
                'pending_status' => 'pending'
            ]);
        }
        else {
            $midtrans_transactions->delete();
        }

        return redirect('/'.$user_id.'/topup/'.$emoney->name);
    }
    
    function checkPendingStatusMidtrans($user_id) {

        $server_key = config('midtrans.server_key');

        $midtrans_transactions = MidtransTransaction::latest()->get();
        foreach ($midtrans_transactions as $mt) {
            try {
                $response = Http::withBasicAuth($server_key, '')
                ->get('https://api.sandbox.midtrans.com/v2/'. $mt->order_id .'/status');
    
                $temp = $response->json();
                if ($temp['transaction_status'] == 'capture' || $temp['transaction_status'] == 'settlement') {
                    $this->updateUser($user_id, $mt->emoney_id, $temp['gross_amount']);
                    $this->addIntoTopUpTransaction($user_id, $mt->emoney_id, $temp['gross_amount']);
                    $mt->delete();
                }
                elseif ($temp['transaction_status'] == 'pending') {

                }
                else {
                    $mt->delete();
                }
            } catch (\Exception $e) {
                return;
            }
        }
    }
    
}
