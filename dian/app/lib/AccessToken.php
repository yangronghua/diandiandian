<?php
namespace lib;
use models\Accesstoken;
/** 
 * AccessToken 
 *
 */
class AccessToken
{
	public $accesstoken = array();
	
	/* 生成accesstoken */
	public function getAccesstoken($user_id)
	{
		$accesstoken = md5(uniqid());
		self::saveAccesstoken($user_id);
		return self::$access_token;
	}
	
	/* 保存accesstoken */
	public function saveAccesstoken($user_id)
	{
		$access_token			   = new Accesstoken;
		$access_token->accesstoken = self::$accesstoken;
		$access_token->user_id     = $user_id;
		$accesstoken->save();		
	} 
	
}