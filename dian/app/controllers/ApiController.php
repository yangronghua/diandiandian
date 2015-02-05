<?php
use Illuminate\Routing\Matching\ValidatorInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redis;
use Whoops\Example\Exception;
//use models\Accesstoken;

class Apicontroller extends BaseController
{
	public static $accesstoken = "";
	private $redis;
	private $exptime = 31536000; 
	private $redisparam = ['id'=>'' ,'phone'=>'','name'=>''];
	//连接redis
	public function __construct()
	{
		$this->redis = Redis::connection();
	}
	
	//验证数据
	public function Validator($data,$rules)
	{
		$validator = Validator::make($data,$rules);
		if($validator->fails())
		{
			foreach ($validator->messages()->all() as $message) {
				App::abort(105, $message);
			}
		}
	}
	
	//验证Accesstoken
	public function ValidateAccesstoken($access_token)
	{
		$data  = $access_token;
		$rules = ['access_token' => 'required|exists:accesstokens,accesstoken'];
		self::Validator($data, $rules);
	}
	
	/* 数据渲染 */
	/*  public function filter($data)
	{
		if($data == "NONE"){
			echo 111;
		}else {
			return self::Json($data);
		}
	}  */
	public function Json($data)
	{		
		$return = [
		  "response" => '100',
		  "message"  => '请求成功！',
	 	  "data"     => $data
		];	
		header('content-type:application/json');	
		return Response::Json($return) ; 
		
	}
		
	/* 生成accesstoken */
	public function getAccesstoken($user_id)
	{
		self::$accesstoken = md5('user'.$user_id.time());
		self::saveAccesstoken($user_id);
		return self::$accesstoken;
	}
	
	/* 保存用户token */
	public function saveAccesstoken($user_id)
	{
		/* $access_token = Accesstoken::firstOrCreate(['user_id' => $user_id]);
		$access_token->accesstoken = self::$accesstoken;
		$access_token->save(); */
		$this->redis->set($user_id,self::$accesstoken);
		$this->redis->expire($user_id,$this->exptime);		
		//$access_token			     = new Accesstoken;
		//$access_token->accesstoken = self::$accesstoken;
		//$access_token->user_id     = $user_id;
		//$rs = $access_token->save();
	}
	
	/* 根据uid获取token */
	public function getTokenById($user_id)
	{
		$access_token = $this->redis->get($user_id);
		return $access_token;
	}	
	
	/* 根据uid保存用户session */
	public function setSession($param = [])
	{
		$saveparam['id']    = $param['id'];
		$saveparam['name']  = $param['name'];
		$saveparam['phone'] = $param['phone'];
		if(!empty($saveparam)){
			$this->redis->setnx(self::$accesstoken,serialize($saveparam));
			$this->redis->expire(self::$accesstoken,$this->exptime);
		}
	}
	
	/* 删除用户session */
	public function delsession($user_id)
	{
		$accesstoken = $this->getTokenById($user_id);
		if(!empty($accesstoken)){
			$this->redis->del($accesstoken);
		}		
	}
	
	//通过accesstoken获取用户信息
	public function getUser($access_token)
	{
		/* 	$id   = Accesstoken::where('accesstoken',$access_token)->pluck('user_id');
			$user = User::where('id',$id)->first(); */
		$user = $this->redis->get($access_token);
		return unserialize($user);
	}
	
	/* 验证accesstoken */
	public function checkAccesstoken($access_token)
	{
		$user = $this->getUser($access_token);	
		if(empty($user) && $accesstoken !== $access_token){
			throw new \Exception('accesstoken不正确');
		}	
		$accesstoken = $this->redis->get($user['id']);		
	}

}