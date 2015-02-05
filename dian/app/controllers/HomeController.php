<?php

use Illuminate\Support\Facades\DB;
class HomeController extends Apicontroller {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	public function showWelcome()
	{
		return View::make('hello');
	}
	
	/* 获取菜单列表 */
	public function getfoods($id)
	{
		$resaurant = Restaurant::find($id);
		/*
		 
		$food = Food::where('r_id',$id)->get();
		
		foreach ($food as $foods)
		{
			$f_list[] = [
				'id'   => $foods['id'],
				'name' => $foods['name'],
				'price'=> $foods['price'],
			    'r_id' => $foods['r_id']
			]; 
		}*/
		
		return Parent::Json($resaurant->foods->toArray());
		
	}
	
	/* 提交点菜订单 */
	public function postOrder()
	{
		$data  = Input::get();
		$rules = [
			'access_token' => 'required',
			'order'        =>'required'
		];
		//数据验证
		parent::Validator($data, $rules);
		//验证accesstoken
		$res = parent::checkAccesstoken($data['access_token']);
		//获取用户id
		$user  = parent::getUser($data['access_token']);	
		$o_list = json_decode($data['order'],true);	
		//保存订单	
		foreach ($o_list as $lists){
			$order = new Order;
			$order->user_id = $user['id'];
			$order->food_id = $lists['id'];
			$order->price   = Food::where('id',$lists['id'])->pluck('price')*$lists['count'];
			$order->count   = $lists['count'];
			$order->save();
			$status = 1;  //不能判断保存了几条数据
		}
		$return = ['status'=>$status];
		return parent::Json($return);
	}
	
	/* 获取订单列表 */
	public function getorders()
	{
		$data  = Input::get();
		$rules = ['access_token' => 'required'];
		//数据验证
		parent::Validator($data, $rules);
		//验证accesstoken
		parent::checkAccesstoken($data['access_token']);
		//获取当天订单
		$date = date('Y-m-d');
	    $data = Order::whereBetween('created_at',["$date.00:00:00","$date.23:59:59"])->get();  //获取当天的数据
		//$data = DB::select("SELECT * FROM orders WHERE created_at LIKE $date.%");语法错误
		$return['sum'] = Order::whereBetween('created_at',["$date.00:00:00","$date.23:59:59"])->sum('price');
		foreach ($data as $datas)
		{
			$return['order'][]=[
				'user_id'   => $datas['user_id'],
				'user_name' => User::where('id',$datas['user_id'])->pluck('name'),
				'user_price'=> $datas['price'],
				'user_order'=>[
				    'id'   => $datas['food_id'],
					'name' => Food::where('id',$datas['food_id'])->pluck('name'),
					'count'=> $datas['count']
				]
			];
		}
		return parent::Json($return);
	}
	
}
























