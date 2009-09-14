<?php

class fake_session{
	
	var $name 		=	'myCookie';
	var $expires	=	86400; // one day
	var $path		=	'/';
	var $domain		=	'';
	var $secure		=	false;
	var $httponly	=	false;
	
	var $data		=	array();
	
	function __construct($options = array())
	{
		if(!empty($options))
			$this->set_options($options);
			
		if($this->check_for_cookie())
			$this->data = $this->load();
		else
			$this->set_cookie();
	}
	
	function set_options($options)
	{
		foreach($options as $key => $option)
		{
			if(property_exists($this, $key))
			{
				$this->{$key} = $option; 
			}
		}
	}
	
	function check_for_cookie()
	{
		return isset($_COOKIE[$this->name]);
	}
	
	function set_cookie($value = '')
	{
		setcookie(
			$this->name,
			$value,
			time() + $this->expires,
			$this->path,
			$this->domain,
			$this->secure,
			$this->httponly
		);
	}
	
	function load()
	{
		return unserialize(base64_decode($_COOKIE[$this->name]));
	}
	
	function save()
	{
		$this->set_cookie(base64_encode(serialize($this->data)));
	}

	function __set($key, $value)
	{
		$this->data[$key] = $value;
		$this->save();
	}

	function __get($key)
	{
		if(array_key_exists($key, $this->data))
		{
			return $this->data[$key];
		}
		else
		{
			return false;
		}
	}
	
	function destroy()
	{
		$this->data = array();
		$this->save();
	}
	
}

?>