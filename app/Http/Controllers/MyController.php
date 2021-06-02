<?php

namespace App\Http\Controllers;

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

        $pusher=new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $p=$pusher->trigger(['my-channel'],'my-event',[$name,$contact,$address]);
        if ($p==true) {
            $result=DB::table('client_ordered_table')
                ->insertOrIgnore(['phn/gmail'=>$phn_email,'product_id'=>$product_id,'product_name'=>$product_name,"product_price"=>$product_price,
                    "number_of_product"=>$product_number,"issue_date"=>new DateTime(),
                    "client_name"=>$name,"contact_no"=>$contact,'address'=>$address]);
            if ($result) {
                return  "OK";
            } else {
                return  "failed";
            }
            return  'OK';
        }

    }

}
