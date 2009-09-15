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
		if($this->get_perm() < $level)
		{
			$this->add_msg("You can't do that.", 'error');
			$this->redirect($this->url . '/?mlurl');
		}
		
	}
	
	function set_and_load_tab()
	{
		$ok_tabs = array(
			'create_new',
			'edit_mlurl',
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
	
	function create_mlurl($target, $name)
	{
		$this->update_mlurl($target, $name);
	}
	
	function update_mlurl($target, $name = false, $editting = false)
	{
		if($editting)
			$editting = (int) $editting;
		
		$target = $this->weak_url_check($target);
		
		$target = htmlentities($this->db->escape($target));
		
		$name = htmlentities($this->db->escape($name));
		
		$q = "SELECT id FROM urls WHERE target = '{$target}'";
		
		if($name)
		{
			$id = hexdec($name);
			$q .= " OR named = '{$name}' OR id = '{$id}'";
		}
		
		$q .= " LIMIT 1";
		
		$check = $this->db->value($q);
		
		if(!$check && !$editting)
		{
			$q = "INSERT INTO urls VALUES('','{$target}','{$name}')";
			$this->db->query($q);
			$this->add_msg('Added url: ' . $this->get_target($this->db->insert_id) . $this->get_link($this->db->insert_id, $name), 'success');
		}
		elseif($check && !$editting)
		{
			$this->add_msg("This url is either already in the system, or the name specified is already taken: " . $this->make_link($check, $name), 'error');
		}
		elseif($editting > 0)
		{
			$q = "SELECT id FROM urls WHERE named = '{$name}'";
			if($this->db->value($q))
			{
				$this->add_msg("The name '$name' is already taken, sorry.", 'error');
			}
			else
			{
				$q = "UPDATE urls SET target = '{$target}', named = '{$name}' WHERE id = '{$editting}'";
				$this->db->query($q);
				$this->add_msg('Updated mlurl.', 'success');
			}
		}
	}
	
	function create_user()
	{
		
	}
	
	function update_user($email, $permission, $user_id = false)
	{
		$email = $this->db->escape($email);
		$permission = (int) $permission;
		
		$q = "SELECT id FROM users WHERE email = '{$email}'";
		if($user_id){
			$q .= " AND id != '{$user_id}'";
		}
		$email_check = $this->db->value($q);

		if(!$email_check)
		{
			$permission = (int) $permission;
			if($permission > 9)
			{
				$this->add_msg('You have sneakily attempted to make a user as powerful as yourself, this is a mistake so I am stopping you.', 'error');
				$this->redirect();
			}
			
			if(!$user_id)
			{
				$password = substr(sha1(mt_rand()), 0, 8);
				$password_hash = sha1($password);
				$q = "INSERT INTO users VALUES('', '{$email}', '{$password_hash}', '{$permission}')";
				$this->db->query($q);
				$this->add_msg("New user created, email sent to $email $password", 'success');
				$email_text = "You have been granted a mlurl account by <a href=\"$this->url\">$this->url</a>.\n\n  You can login here: $this->url \n\n  With the following password: $password";
				mail($email, "$this->url mlurl account", $email_text);
			}
			else
			{
				$q = "UPDATE users SET email = '{$email}', permission = '$permission' WHERE id = '{$user_id}'";
				$this->db->query($q);
				$this->add_msg(htmlentities($email) . ' has been updated.', 'success');
			}
		}
		else
		{
			$this->add_msg('There is already a user with that email address', 'error');
		}			
	}
	
	function is_active_tab()
	{
		foreach(func_get_args() as $arg)
		{
			if($arg == $this->tab)
			{
				echo ' class="active"';
			}
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
	
	function weak_url_check($url){
		
		if(!strpos($url, '://'))
		{
			$url = 'http://' . $url;
		}
		if(!preg_match('/[\w]+:\/\/[\w]+\./', $url))
		{
			$this->add_msg("That didn't really look anything like a url.", 'error');
			$this->redirect();
		}
		else
		{
			return $url;
		}
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
			$target = $this->get_target($id);
			$link = $this->get_link($id);
			if($target === $link)
			{
				die('This redirect redirects to itself.  This is an interrupt to prevent an internet explosion.');
			}
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
		$link = '<a href="'. $this->url . $ref .'">'. $this->url . $ref .'</a> <span>(goes to: ' . $this->get_target($id) . ')</span>';
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
			$to = $this->url . '/?' . $_SERVER['QUERY_STRING'];
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