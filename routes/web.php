<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sb', function () {
    return view('scoreBoard');
});
Route::get('/update', function () {
    return view('scoreUpdate');
});

//Routes for Shops Here
Route::post('/lf', 'svcontroller@location_fetching');
Route::post('/fetch_pro_after_location_search', 'svcontroller@temp_location');
Route::post('/fetching_pro_by_name_specific_region', 'svcontroller@fetching_product_by_name_specific_region');
Route::post('/data_fetching', 'svcontroller@data_fetching');
Route::post('/shop_name_fetching', 'svcontroller@shop_name_fetching');
Route::post("/ordered_products", "svcontroller@onProduct_Orders");
Route::post("/shop_details", "svcontroller@onShop_Details");
Route::post("/set_delivering_status", "mscontroller@onOrder_Receive");
Route::post("/delete_garbage_items", "svcontroller@onDelete_garbage_Items");



Route::post("/data_fetching",'mscontroller@onData_Fetching');
Route::post("/delete_products_temp",'mscontroller@onDelete_products_temp');
Route::post("/test",'mscontroller@onTest');


//Routes for My Shop
Route::post("/ordered_products_real", "mscontroller@onProduct_order_realtime");
Route::post('/register','mscontroller@onRegister');
Route::post('/login','mscontroller@onLogIn');
Route::post('/upload','mscontroller@onUploadImage');
Route::get('/fe','mscontroller@onFetchImage');
Route::post('/delete_products','mscontroller@onDelete_Products');
Route::post('/details_fetching','mscontroller@onDetails_Fetching');
Route::post('/account_delete','mscontroller@onDelete_Account_Forever');
Route::post('update_products','mscontroller@onUpdate_Products');
Route::post('/d','mscontroller@d');
Route::post('/order','mscontroller@onOrders');
Route::post('/order_receive','mscontroller@onOrder_Receive');
Route::post('/date','mscontroller@onDateFetch');

//Shops Here
Route::post('pusher', 'MyController@sendRealTime');
Route::post('/user_signup','svcontroller@user_signUp');
Route::post("/user_login","svcontroller@user_login");
Route::post("/rand","mscontroller@onRandom");

