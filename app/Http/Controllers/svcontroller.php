<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class svcontroller extends Controller
{

    function location_fetching(Request $request)
    {
        $region = $request->input('region').'%';

        $result = DB::table('login_info')
            ->where('region', 'like', $region)
            ->groupBy(['country', 'district', 'subdistrict', 'region'])
            ->get(["country","district","subdistrict","region"]);
        return json_encode($result);
    }


    function temp_location(Request $request)
    {
        $country = $request->input('country');
        $district = $request->input('district');
        $subdistrict = $request->input('subdistrict');
        $region = $request->input('region');

        $result = DB::table('products')
            ->join('login_info', 'products.user_name', '=', 'login_info.user_name')
            ->where('login_info.country', '=', $country)
            ->where('login_info.district', '=', $district)
            ->where('login_info.subdistrict', '=', $subdistrict)
            ->where('login_info.region', '=', $region)
            ->where('deletion_status','=',0)
            ->inRandomOrder()->limit(8)
            ->get();

        return json_encode($result);

    }

    function fetching_product_by_name_specific_region(Request $request)
    {

        $country = $request->input('country');
        $district = $request->input('district');
        $subdistrict = $request->input('subdistrict');
        $region = $request->input('region');

        $pro_name = $request->input('product_name');
        $pro_name = '%' . $pro_name . '%';

        $result = DB::table('products')
            ->join('login_info', 'products.user_name', '=', 'login_info.user_name')
            ->where('login_info.country', '=', $country)
            ->where('login_info.district', '=', $district)
            ->where('login_info.subdistrict', '=', $subdistrict)
            ->where('login_info.region', '=', $region)
            ->where('products.description', 'like', $pro_name)
            ->where('deletion_status','=',0)
            ->inRandomOrder()->limit(8)
            ->get();

        return json_encode($result);

    }

//    function data_fetching(Request $request)
//    {
//        $user_name = $request->input('user_name');
//
//        $result = DB::table('products')
//            ->join('login_info', 'products.user_name', '=', 'login_info.user_name')
//            ->where('login_info.user_name', '=', $user_name)
//            ->select(['products.id', 'products.description', 'products.price', 'products.imagepath', 'login_info.currency', 'login_info.Location', 'login_info.name'])
//            ->get();
//
//        return $result;
//
//    }

    function shop_name_fetching(Request $request)
    {
        $country = $request->input('country');
        $district = $request->input('district');
        $subdistrict = $request->input('subdistrict');
        $region = $request->input('region');
        $shop_name = $request->input('shop_name') . '%';

        $result = DB::table('login_info')
            ->where('region', '=', $region)
            ->where('country', '=', $country)
            ->where('district', '=', $district)
            ->where('subdistrict', '=', $subdistrict)
            ->where('name', 'like', $shop_name)
            ->select('name', 'user_name', 'Location')
            ->get();

        return json_encode($result);
    }

    function user_signUp(Request $request)
    {

        $name=$request->input('name');
        $phn_gmail = $request->input('phn_gmail');
        $password = $request->input('password');

        if (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $phn_gmail)) {
            $result=DB::table('client_login_info')
                ->insertOrIgnore(['name'=>$name,'phn/gmail'=>$phn_gmail,'password'=>$password]);
            if ($result==true) {
                return  response()->json(['response'=>'success']);
            } else {
                return response()->json(["response"=>'exist']);
            }
        } else if (preg_match("%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i",
                $phn_gmail) && strlen($phn_gmail) >= 10) {
            $result=DB::table('client_login_info')
                ->insertOrIgnore(['name'=>$name,'phn/gmail'=>$phn_gmail,'password'=>$password]);
            if ($result==true) {
                return  response()->json(['response'=>'success']);
            } else {
                return response()->json(['response'=>'exist']);
            }
        } else {
            return response()->json(['response'=>'invalid_phn_or_gmail']);
        }

    }

    function  user_login(Request $request){
        $phn_gmail = $request->input('phn_gmail');
        $password=$request->input('password');

        $result = DB::table('client_login_info')
            ->where(DB::raw('BINARY `phn/gmail`'), '=', $phn_gmail)
            ->where(DB::raw('BINARY `password`'),'=',$password)
            ->pluck('name')->first();
        if ($result) {
            return response()->json(['response'=>'success', 'name'=>$result]);
        } else {
            return  response()->json(["response"=>"failed"]);
        }

    }

    function  onProduct_Orders(Request  $request){
        $client_phn_gmail = $request->input("phn_gmail");
        $status_code = $request->input("status_code");

        if ($status_code==1) {
            $result=DB::table("client_ordered_table")
            ->join("products","products.id","=",
                "client_ordered_table.product_id")
                ->where("client_ordered_table.phn/gmail","=",$client_phn_gmail)
                ->where("client_ordered_table.deletion_status",'=',0)
                ->whereIn('order_status',[1,0,2])->get();
                return $result;


        } elseif ($status_code==4) {
            $result=DB::table("client_ordered_table")
                ->join("products","products.id","=",
                    "client_ordered_table.product_id")
                ->where("client_ordered_table.deletion_status",'=',0)
                ->where("client_ordered_table.phn/gmail","=",$client_phn_gmail)
                ->whereIn('order_status',[3,4])
                //->Where('order_status','=',4)
                ->get();
            return $result;
        } else {
            return  'o';
        }
    }

    function  onShop_Details(Request  $request){
        $user_name = $request->input("user_name");

        $result = DB::table("login_info")
            ->where("user_name","=",$user_name)
            ->get();
        return json_encode($result);
    }

    function  onDelete_garbage_Items(Request  $request){
        $phn_gmail = $request->input("phn_gmail");
        $product_ids = $request->input("product_id");
        $issue_dates = $request->input("issue_date");

        $result=DB::table("client_ordered_table")
            ->where("phn/gmail","=",$phn_gmail)
            ->whereIn('product_id',$product_ids)
            ->whereIn('issue_date',$issue_dates)
            ->update(["deletion_status"=>1]);

        if ($result==true) {
            return response()->json(["response"=>"deleted"]);
        } else {
            return  response()->json(["response"=>"delete_failed"]);
        }
    }





}
