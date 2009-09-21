<?php

class fake_session{
	
	var $name 		=	'myCookie';
	var $expires	=	86400; // one day
	var $path		=	'/';
	var $domain		=	'';
	var $secure		=	false;
	var $httponly	=	false;
	var $mcrypt_key	=	'someUNIQUEstringSETbyYOU'; // set to false to disable mcrypt encryption
	var $mcrypt_iv	=	false;
	
	var $data		=	array();
	
	function __construct($options = array())
	{
		if(!empty($options))
		{
			$this->set_options($options);
		}
		
		if(function_exists('mcrypt_encrypt') && $this->mcrypt_key)
		{
			$this->mcrypt_key = md5($this->mcrypt_key);
			$this->mcrypt_iv = mcrypt_create_iv(32);
		}
		
		if($this->check_for_cookie())
		{
			$this->data = $this->load();
		}
		else
		{
			$this->save();
			$this->set_cookie();
		}
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
		$data = base64_decode($_COOKIE[$this->name]);
		if(function_exists('mcrypt_encrypt') && $this->mcrypt_key)
		{
			$data = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->mcrypt_key, $data, MCRYPT_MODE_ECB, $this->mcrypt_iv);
		}		
		return unserialize($data);
	}
	
	function save()
	{
		$data = serialize($this->data);
		if(function_exists('mcrypt_encrypt') && $this->mcrypt_key)
		{
			$data = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->mcrypt_key, $data, MCRYPT_MODE_ECB, $this->mcrypt_iv);
		}
		$this->set_cookie(base64_encode($data));
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
	
	function delete()
	{
		setcookie($this->name, "", time() - 3600);
	}
	
	function dump()
	{
		$this->data = array();
		$this->save();
	}
	
	function mcrypt_test()
	{
		if(function_exists('mcrypt_encrypt'))
		{
			die('You have Mcrypt.  The data stored in your cookie will be encrypted.
			Make sure you use a unique string for your $mcrypt_key variable.');
		}
		else
		{
			die('You do not have Mcrypt.  The data stored in your cookie will not be encrypted.  
			You should not use this store anything that you wish to be kept a secret.  
			You can learn more about Mcrypt and how to install it here: http://php.net/mcrypt');
		}
	}
	
}

?>