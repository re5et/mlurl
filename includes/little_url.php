<?php

error_reporting(E_ALL);

class little_url{
	
	var $db 		=	false;
	var $target_id 	=	false;
	var $requested 	=	false;
	var $added		=	false;
	var $authed		=	false;
	var $session	=	false;
	var $debug		=	true;
	var $url		=	'http://selwyn/.a/mlurl';
	
	function __construct($db, $session)
	{
		$this->session = $session;
		$this->db = $db;
		
		if(isset($_GET['mlurl']))
		{
			if(isset($_GET['logout']))
			{
				$this->logout();
			}
			$this->authed = $this->check_auth();
			if($this->authed)
			{
				if($this->session->msg)
				{
					$this->msg = $this->session->msg;
					$this->session->msg = false;
				}
				$this->set_and_load_tab();
			}
			else
			{
				$this->set_and_load_tab();
			}
		}
		else
		{
			$this->mlurl_redirect();
		}
			
	}
	
	function check_auth()
	{
		$email = $this->session->email;
		$password = $this->session->password;
		
		if($email && $password)
		{
			return $this->login($email, $password);
		}
		else
		{
			return false;
		}
		
	}
	
	function login($email = false, $password = false)
	{
		if($email && $password)
		{
			$email = $this->db->escape($email);
			$password = $this->db->escape($password);
			$q = "SELECT id FROM users WHERE email = '{$email}' AND password = '{$password}'";
			$login_check =  $this->db->value($q);

			if($login_check)
			{
				$this->session->email = $email;
				$this->session->password = $password;
				return true;
			}
			else
			{
				return false;
			}
			
		}
		elseif(isset($_POST['email']) && isset($_POST['password']))
		{
			$this->login($_POST['email'], sha1($_POST['password']));
		}
		else
		{
			$this->set_and_load_tab();
		}
	}
	
	function logout()
	{
		$this->session->destroy();
		$this->add_msg('Logged out successfully', 'success');
		$this->redirect($this->url . '/?mlurl');
	}
	
	function get_perm()
	{
		$q = "SELECT permission FROM users WHERE email = '{$this->session->email}'";
		return (int) $this->db->value($q);
	}
	
	function perm_check($level)
	{
		if($this->get_perm() < $level){
			$this->add_msg("You can't do that.", 'error');
			$this->redirect($this->url . '/?mlurl');
		}
		
	}
	
	function set_and_load_tab()
	{
		$ok_tabs = array(
			'create_new',
			'mlurls',
			'stats',
			'account',
			'admin',
			'login',
			'reset_password',
			'edit_user',
			'delete_user',
			'edit_mlurl'
		);
		
		if(isset($_GET['tab']))
		{
			$this->tab = $_GET['tab'];
		}
		elseif(!isset($_GET['tab']))
		{
			if($this->authed)
			{
				$this->tab = 'create_new';
			}
			else
			{
				$this->tab = 'login';
			}
		}
		
		if(in_array($this->tab, $ok_tabs))
		{
			include("controllers/$this->tab.php");
		}
		else
		{
			$this->huh();
		}

	}
	
	function is_active_tab($tab)
	{
		if($tab == $this->tab)
		{
			echo ' class="active"';
		}
	}
	
	function get_link($id, $name = false)
	{
		$link = $this->url . '/';
		
		$q = "SELECT named FROM urls WHERE id = '{$id}'";
		$name = $this->db->value($q);
		
		$link .= ($name) ? $name : dechex($id);
		return $link;
	}
	
	function get_target($id)
	{
		$q = "SELECT target FROM urls WHERE id = '{$id}' LIMIT 1";
		$target = $this->db->value($q);
		return $target;
	}
	
	function mlurl_redirect()
	{
		$url_segments = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
		
		$requested = $this->db->escape(end($url_segments));
		
		$q = "SELECT id FROM urls WHERE named = '{$requested}' LIMIT 1";
		
		$named_check = $this->db->value($q);
		
		if($named_check)
		{
			$id = $named_check;
		}
		else
		{
			$requested = hexdec($requested);
			$q = "SELECT id FROM urls WHERE id = '$requested'";
			$hex_check = $this->db->value($q);
			$id = ($hex_check) ? $hex_check : false; 
		}
		
		if($id)
		{
			$this->record_hit($id);
			$this->redirect($this->get_target($id));
		}
		else
		{
			$this->redirect($this->url);
		}
		
	}
	
	function record_hit($id)
	{
		$mlurl = $id;
		
		$server = array(
			'REMOTE_ADDR', 'HTTP_USER_AGENT', 'HTTP_USER_AGENT', 'HTTP_REFERER'
		);
		
		foreach($server as $key)
		{
			$$key = (isset($_SERVER[$key])) ? $this->db->escape($_SERVER[$key]) : false;
		}
		
		$q = "INSERT INTO hits VALUES('', '{$id}', '{$REMOTE_ADDR}', '{$HTTP_USER_AGENT}', '{$HTTP_USER_AGENT}', '{$HTTP_REFERER}', '{$HTTP_USER_AGENT}')";
		$this->db->query($q);
		
	}
	
	function make_link($id, $name = false)
	{
		$ref = ($name) ? $name : dechex($id);
		$link = '<a href="'. $this->url . $ref .'">'. $this->url . $ref .'</a><span>(goes to: ' . $this->get_target($id) . ')</span>';
		return $link;
	}
	
	function view($file, $data = false)
	{
		if(is_array($data))
		{
			foreach($data as $k => $v)
			{
				$$k = $v;
			}
		}
			
		include("views/$file.php");
	}
	
	function redirect($to = false)
	{
		if(!$to)
		{
			$to = $this->url . '?' . $_SERVER['QUERY_STRING'];
		}
		header("Location: $to");
		die;
	}
	
	function huh()
	{
		$this->add_msg("Not sure what you were trying to do, but it didn't work", 'error');
		$this->redirect($this->url . '/?mlurl');
	}
	
	function add_msg($msg, $type = false)
	{
		$this->session->msg = array(
			'msg'	=> $msg,
			'type'	=> $type
		);
	}
	
	function show_msgs()
	{
		if(isset($this->msg) && $this->msg)
		{
			echo '<div id="msgs">';
				echo '<p class="msg';
				if($this->msg['type'])
					echo ' ' . $this->msg['type'];
				echo '">' . $this->msg['msg'] . '</p>';
			echo '</div>';
		}
		else
		{
			return false;
		}

	}
	
	function debug()
	{
		if($this->debug)
		{
			echo '<div id="debug">';
				echo '<pre>';
					var_dump($this);
				echo '</pre>';
			echo '</div>';
		}
	}
	
}

?>