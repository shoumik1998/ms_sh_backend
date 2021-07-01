<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;
use DateTime;

class MyController extends Controller
{
    public function sendRealTime(Request  $request)
    {
        $name = $request->input('name');
        $contact = $request->input('contact');
        $address=$request->input('address');
        $phn_email = $request->input("phn_email");
        $product_number = $request->input("product_number");
        $product_id = $request->input('product_id');
        $product_name = $request->input('product_name');
        $product_price=$request->input("product_price");
        $user_name = $request->input("user_name");
        $issue_date=$request->input("issue_date");

        $pusher=new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $phn_email_push=$pusher->trigger([$user_name],'phn_email-event',$phn_email);
        $product_id_push=$pusher->trigger([$user_name],'product_id-event',$product_id);
        $issue_date_push=$pusher->trigger([$user_name],'issue_date-event',$issue_date);
        if ($phn_email_push==true && $product_id_push==true && $issue_date_push==true) {
            $result=DB::table('client_ordered_table')
                ->insertOrIgnore(['phn/gmail'=>$phn_email,'product_id'=>$product_id,'product_name'=>$product_name,"product_price"=>$product_price,
                    "number_of_product"=>$product_number,"issue_date"=>$issue_date,
                    "client_name"=>$name,"contact_no"=>$contact,'address'=>$address]);
            if ($result) {

                return  response()->json(["response"=>"ok"]);
            } else {
                return  response()->json(["response"=>"failed"]);
            }
        }
        return  response()->json(["response"=>"ok"]);
    }

}
