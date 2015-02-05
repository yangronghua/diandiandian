<?php

class Restaurant extends Eloquent
{
	public function foods() {
		return $this->hasMany('food', 'r_id');
	}
}