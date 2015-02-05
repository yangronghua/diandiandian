<?php

use Illuminate\Support\Facades\View;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
//Route::resource('restaurants','CheckController');
Route::get('/restaurants/{id}/foods','HomeController@getfoods'); //获取菜单列表 
Route::post('/restaurants/{id}/order','HomeController@postOrder'); //提交点菜订单
Route::get('/orders','HomeController@getorders');//获取订单列表
Route::controller('/','CheckController');
Route::get('/', function()
{
	return View::make('hello');
});


