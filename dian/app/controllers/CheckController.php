<?php
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckController extends  ApiController{
	
	/* 用户注册 */
	public function postRegister()
	{
		//数据验证
		$data  = Input::get();
		$rules = [
			'phone'=>'required|numeric|regex:/^1\d{10}$/|unique:users,phone',
			'name' =>'required',
			'psw'  =>'required|min:6',
		];
		parent::validator($data,$rules);
		//数据插入数据库
		$user        = new User;
		$user->phone = $data['phone'];
		$user->name  = $data['name'];
		$user->psw   = $data['psw'];
		$status      = $user->save();
		if($status)
		{
			$return = array('status'=>1); //注册成功				
		}else{
			$return = array('status'=>0); //注册失败
		}
		return parent::Json($return);			
	}
	
	/*用户登陆 */
	public function getLogin()
	{
		//数据验证
		$data   = Input::get();
		$phone  = $data['phone'];
		$rules  = ['phone' => 'required|numeric|regex:/^1\d{10}$/|exists:users,phone',
		           'psw'   => "required|exists:users,psw,phone,$phone"
		          ];
		
		$res = parent::Validator($data, $rules);
		//获取用户accesstoken
		$user        = User::where('phone',$data['phone'])->first();
	    $user_id     = $user['id']; //取出用户user_id	  
	    parent::delsession($user_id);//delete session
	    $accesstoken = parent::getAccesstoken($user_id);
	    $setsession  = parent::setSession($user); 
	    $return      = ['access_token' => $accesstoken]; 
	    return parent::Json($return);		
	}
	
	/*用户注销 */
	public function getLogout()
	{

	}
	
	/* 获取餐厅列表 */
	public function getRestaurantlist()
	{
		$list = Restaurant::get();
		foreach($list as $lists)
		{
			$rlist['restaurant'][]   = ['id'=>$lists['id'],
				'name' => $lists['name'],
				'phone'=> $lists['phone']			
			];			
		} 
	   return parent::Json($rlist);		
	}
	
	
}















































