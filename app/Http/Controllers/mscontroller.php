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
            ->where(DB::raw('BINARY `user_password`'), '=', $user_password)
            ->pluck('name')->first();
        return response()->json(['response'=>'OK','name'=>$result]);
    }

    function onUploadImage(Request $request)
    {
        $string_image=$request->input('images');
        $title=$request->input('title');
        $price = $request->input('price');
        $user_name=$request->input('user_name');
        $image = str_replace('data:image/png;base64,', '', $string_image);
        $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $string_image));
        $image_path='C:/xampp/htdocs/loginapp/uploads/'.$user_name.'.'.$title.'.jpg';
        $image_path_absolute='http://192.168.43.17:80/loginapp/uploads/'.$user_name.'.'.$title.'.jpg';
        \File::put($image_path, $fileData);
        $result=DB::table('products')->insertOrIgnore(['description'=>$title,'price'=>$price,'imagepath'=>$image_path_absolute,'user_name'=>$user_name]);
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
        $result=DB::table('products')
            ->select('products.id','products.description','products.price','products.imagepath','products.user_name','login_info.currency')
            ->join('login_info','products.user_name','=','login_info.user_name')
            ->where('login_info.user_name','=',$user_name)
            ->get();

        return json_encode($result);
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




}
