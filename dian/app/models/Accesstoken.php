<?php

class Accesstoken extends \Eloquent
{
	protected $table = 'accesstokens';
	
	protected $fillable = ['user_id'];
}