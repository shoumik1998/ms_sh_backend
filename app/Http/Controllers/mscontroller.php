<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\CommonMark\Inline\Element\Image;
use PhpParser\Node\Scalar\String_;
use Pusher\Pusher;


class mscontroller extends Controller
{
    function onRegister(Request $request)
    {
        $country = $request->input('country');
        $district = $request->input('district');
        $subdistrict = $request->input('subdistrict');
        $region = $request->input('region');
        $location = $request->input('location');
        $shop_name = $request->input('shop_name');
        $user_name = $request->input('user_name');
        $user_password = $request->input('password');
        $currency = $request->input('currency');
        $cell_number = $request->input('cell_number');
        $selector = $request->input('selector_code');

        if (!preg_match("/^[.@0-9a-zA-Z]+$/", $user_name) || strlen($user_name) < 3 || strlen($user_name) > 30 || strlen($user_password) > 10 || !preg_match("/^[a-zA-Z ]*$/", $currency)) {
            if (strlen($user_name) < 3) {
                return "Invalid_low";
            } else if (strlen($user_name) > 30) {
                return "Invalid_high";
            } else if (strlen($user_password) > 10) {
                return "Invalid_password";
            } else if (!preg_match("/^[a-zA-Z ]*$/", $currency)) {
                return "Invalid_currency";
            } else if (!preg_match("/^[.,0-9a-zA-Z]+$/", $location)) {
                return "Invalid_location";
            } else {
                return "Invalid";

            }


        } else {
            if ($selector == 1) {
                DB::table('login_info')
                    ->where('user_name', '=', $user_name)
                    ->update(['name' => $shop_name, 'user_password' => $user_password,
                        'country' => $country, 'district' => $district, 'subdistrict' => $subdistrict,
                        'region' => $region, 'Location' => $location, 'currency' => $currency, 'cell_number' => $cell_number]);
                return response()->json(['response'=>'OK']);
            } else {
                $result = DB::table('login_info')
                    ->insertOrIgnore(['name' => $shop_name, 'user_name' => $user_name, 'user_password' => $user_password,
                        'country' => $country, 'district' => $district, 'subdistrict' => $subdistrict,
                        'region' => $region, 'Location' => $location, 'currency' => $currency, 'cell_number' => $cell_number]);

                if ($result == true) {
                    return response()->json(['response'=>'OK']);
                } else {
                    return response()->json(['response'=>'exists']);
                }
            }

        }
    }

    function onLogIn(Request $request)
    {

        $user_name = $request->input('user_name');
        $user_password = $request->input('user_password');


        $result = DB::table('login_info')
            ->where(DB::raw('BINARY `user_name`'), '=', $user_name)
            ->where(DB::raw('BINARY `user_password`'), '=', $user_password);

            $name=$result->pluck('name')->first();
            $currency=$result->pluck('currency')->first();


        return response()->json(['response'=>'OK','name'=>$name,'currency'=>$currency]);
    }

    function onUploadImage(Request $request)
    {
        $string_image=$request->input('images');
        $title=$request->input('title');
        $price = $request->input('price');
        $user_name=$request->input('user_name');
        $orderable_status = $request->input("orderable_status");
        $image = str_replace('data:image/png;base64,', '', $string_image);
        $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $string_image));
        $image_path='C:/xampp/htdocs/loginapp/uploads/'.$user_name.'.'.$title.'.jpg';
        $image_path_absolute='http://192.168.43.17:80/loginapp/uploads/'.$user_name.'.'.$title.'.jpg';
        \File::put($image_path, $fileData);
        $result=DB::table('products')->insertOrIgnore(['description'=>$title,'price'=>$price,'orderable_status'=>$orderable_status,'imagepath'=>$image_path_absolute,'user_name'=>$user_name]);
        if($result==true){
            return  response()->json(["response"=>"uploaded"]);
        }else{
            return  response()->json(["response"=>"failed"]);
        }


        //Storage::disk('local')->put( $imageName, base64_decode($string_image));
//
//        $tmpFilePath = sys_get_temp_dir() . '/' . $title.'.png';
//        file_put_contents($tmpFilePath, $fileData);
//
//        $tmpFile=new File($tmpFilePath);
//
//        $file=new UploadedFile(
//            $tmpFile->getPathname(),
//        $tmpFile->getFilename(),
//        $tmpFile->getMimeType(),0,true);
//
//        $file->storeAs('images',$title.".png");

        return \response()->json(['response'=>'uploaded']);
    }

    function  onFetchImage(){
        $f=\File::get('E:/uploads/home_screen.png');
        $file=base64_encode($f);
        return $file;
    }

    function onData_Fetching(Request $request)
    {

        $user_name = $request->input('user_name');
        $deletion_status = $request->input("deletion_status");
        if ($deletion_status==1) {
            $result=DB::table('products')
                ->select('products.id','products.description','products.price','products.imagepath','products.user_name','login_info.currency','products.description','products.orderable_status','login_info.Location','login_info.name')
                ->join('login_info','products.user_name','=','login_info.user_name')
                ->where('login_info.user_name','=',$user_name)
                ->where('deletion_status','=',1)
                ->get();

            return json_encode($result);
        }else{
            $result=DB::table('products')
                //->select('products.id','products.description','products.price','products.imagepath','products.user_name','login_info.currency','products.description','products.orderable_status','login_info.Location','login_info.name')
                ->join('login_info','products.user_name','=','login_info.user_name')
                //->join("client_ordered_table","client_ordered_table.product_id","=","products.id")
                ->where('login_info.user_name','=',$user_name)
                ->where("products.deletion_status","=",0)
                ->where('')
                ->get();
            return json_encode($result);
        }

    }

    function onDelete_Products(Request  $request){
        $ids=$request->input('ids');
        $idss[]=$ids;

        $cols=DB::table('products')
            ->whereIn('id',$ids);

        $user_name=$cols->select('user_name')->pluck('user_name')->first();
        $title=$cols->select('description')->pluck('description');
        $data=array();
        foreach ($title as $t){
            $data[]="C:/xampp/htdocs/loginapp/uploads/".$user_name.".".$t.".jpg";
      }

        $result=DB::table('products')->whereIn('id',$ids)->delete();

        File::delete($data);
        return response()->json(["response"=>"ok",]);
    }

    function  onDelete_products_temp(Request  $request){
        $ids = $request->input('ids');
        $result=DB::table("products")
            ->join("client_ordered_table","products.id","=",
                "client_ordered_table.product_id")
            ->whereIn('products.id',$ids);

//            $delet_result=$result->whereNotIn("client_ordered_table.order_status",[1,2,4])
//            ->delete();
            $not_delete_result=$result->whereIn('client_ordered_table.order_status',[1,2,4])->get('products.id');
        if (true) {
            return  \response()->json(["response"=>'deleted',$not_delete_result]);
        }
        return $result;
    }

    function  onNot_Want_Products_Orders(Request $request){
        $ids = $request->input("ids");
        $user_name = $request->input("user_name");
        if ($user_name==null && $ids!=null) {
            $result=DB::table("products")
                ->whereIn('id',$ids)
                ->update(["deletion_status"=>1]);
            return \response()->json(["response"=>"ok"]);
        }elseif ($user_name!=null && $ids==null){


        }




    }

    function  onDetails_Fetching(Request  $request){
        $user_name=$request->input('user_name');
        $password=$request->input('user_password');

        $result=DB::table('login_info')
            ->where(DB::raw('BINARY `user_name`'),'=',$user_name)
            ->where(DB::raw('BINARY `user_password`'),'=',$password);

        $country=$result->pluck('country')->first();
        $district=$result->pluck('district')->first();
        $subdistrict=$result->pluck('subdistrict')->first();
        $region=$result->pluck('region')->first();
        $location=$result->pluck('Location')->first();
        $cell_number=$result->pluck('cell_number')->first();
        $currency=$result->pluck('currency')->first();
        $name=$result->pluck('name')->first();
        return response()->json(["name"=>$name,"user_name"=>$user_name,"user_password"=>$password,
            "country"=>$country,"district"=>$district,"subdistrict"=>$subdistrict,"region"=>$region,
            "Location"=>$location,"currency"=>$currency,"cell_number"=>$cell_number,"response"=>"OK"]);

    }

    function  onDelete_Account_Forever(Request $request){
        $user_name=$request->input('user_name');

        $result=DB::table('products')
            ->where('user_name','=',$user_name);

        $delete=$result->delete();
        $title=$result->pluck('description');
        $titles=array();
        foreach ($title as $t){
            $titles[]="C:/xampp/htdocs/loginapp/uploads/".$user_name.".".$t.".jpg";
        }
        File::delete($titles);
        DB::table('login_info')
            ->where("user_name",'=',$user_name)->delete();
        return response()->json(["response"=>"deleted"]);
    }


    function  onUpdate_Products(Request  $request){
        $id = $request->input("id");
        $user_name = $request->input("user_name");
        $product_name = $request->input("product_name");
        $product_price = $request->input("product_price");


      $result= DB::table('products')->where('id','!=' ,$id)->where('user_name','=',$user_name)
           ->where('description','=',$product_name)->pluck('description');

        if ($result->isEmpty()) {
            DB::table("products")->where('id','=',$id)->where('user_name','=',$user_name)->update(['description'=>$product_name,'price'=>$product_price]);
            return response()->json(['response' => 'updated']);
        }else {
            return response()->json(['response' => 'exist']);
        }

    }

    function  onOrders(Request  $request)
    {
        $user_name = $request->input("user_name");

        $result = DB::table('client_ordered_table')
            ->join('products', 'products.id', '=', 'client_ordered_table.product_id')
            ->where('products.user_name', '=', $user_name)
            ->get();

        return json_encode($result);
    }

    function  onProduct_order_realtime(Request  $request)
    {

        $product_id = $request->input("product_id");
        $phn_email = $request->input("phn_email");
        $issue_date = $request->input("issue_date");
        $status_code = $request->input("order_status_code");

        if ($status_code!=null) {

        }

        $result = DB::table("client_ordered_table")
            ->join("products", "products.id", "=",
                "client_ordered_table.product_id")
            ->where("client_ordered_table.phn/gmail", "=", $phn_email)
            ->where("issue_date", "=", $issue_date)
            ->where("client_ordered_table.product_id", "=", $product_id)
            ->get();
        return json_encode($result);
    }

        function onOrder_Receive(Request $request){
        $product_id = $request->input("product_id");
        $client_phn_gmail = $request->input("phn_gmail");
        $status_code = $request->input("status_code");
        $delivering_date = $request->input("delivering_date");
        $issue_date = $request->input("issue_date");
        $user_name = $request->input("user_name");


        $pusher=new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

            if ($status_code==4) {
                $push_deliver=$pusher->trigger([$user_name], 'delivering-event', $delivering_date);
                $push_issue=$pusher->trigger([$user_name], 'issue-event', $issue_date);
                $push_id=$pusher->trigger([$user_name],'id-event',$product_id);
                $push_phn_gmail=$pusher->trigger([$user_name],'phn-gmail-event',$client_phn_gmail);
                $push_status=$pusher->trigger([$user_name], 'status-event', $status_code);
            }else{
                $push_deliver=$pusher->trigger([$client_phn_gmail], 'delivering-event', $delivering_date);
                $push_issue=$pusher->trigger([$client_phn_gmail], 'issue-event', $issue_date);
                $push_id=$pusher->trigger([$client_phn_gmail],'id-event',$product_id);
                $push_phn_gmail=$pusher->trigger([$client_phn_gmail],'phn-gmail-event',$client_phn_gmail);
                $push_status=$pusher->trigger([$client_phn_gmail], 'status-event', $status_code);
            }


        if ($push_id==true && $push_deliver==true && $push_phn_gmail==true && $push_status==true && $push_issue==true) {
            if ($status_code==1) {
                $receive_result=DB::table("client_ordered_table")
                    ->where("phn/gmail","=",$client_phn_gmail)
                    ->where("product_id","=",$product_id)
                    ->where("issue_date","=",$issue_date)
                    ->update(["order_status"=>$status_code,"delivering_date"=>$delivering_date]);
                if ($receive_result==true) {
                    return response()->json(["response"=>"received"]);
                }
            } else {
                $rest_resutl=DB::table("client_ordered_table")
                    ->where("phn/gmail","=",$client_phn_gmail)
                    ->where("product_id","=",$product_id)
                    ->where("issue_date","=",$issue_date)
                    ->update(["order_status"=>$status_code]);
                if ($rest_resutl==true && $status_code==2) {
                    return  response()->json(["response"=>"deliver"]);
                } elseif ($rest_resutl==true && $status_code==3) {
                    return  response()->json(["response"=>"rejected"]);
                } elseif ($rest_resutl==true && $status_code==4) {
                    return  response()->json(["response"=>"delivered"]);

                }else{
                    return  response()->json(["response"=>"failed"]);
                }
            }


        }




    }




}
