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

//Routes for Shop Visitor
Route::post('/lf', 'svcontroller@location_fetching');
Route::post('/fetch_pro_after_location_search', 'svcontroller@temp_location');
Route::post('/fetching_pro_by_name_specific_region', 'svcontroller@fetching_product_by_name_specific_region');
Route::post('/data_fetching', 'svcontroller@data_fetching');
Route::post('/shop_name_fetching', 'svcontroller@shop_name_fetching');

//Routes for My Shop
Route::post('/register','mscontroller@onRegister');
Route::post('/login','mscontroller@onLogIn');
Route::post('/upload','mscontroller@onUploadImage');
Route::get('/fe','mscontroller@onFetchImage');
Route::post("/data_fetching",'mscontroller@onData_Fetching');
Route::post('/delete_products','mscontroller@onDelete_Products');
Route::post('/details_fetching','mscontroller@onDetails_Fetching');
Route::post('/account_delete','mscontroller@onDelete_Account_Forever');
Route::post('update_products','mscontroller@onUpdate_Products');
Route::post('/d','mscontroller@d');
Route::post('/order','mscontroller@onOrders');
//out of context
Route::post('pusher', 'MyController@sendRealTime');
Route::post('/user_signup','svcontroller@user_signUp');
Route::post("/user_login","svcontroller@user_login");

