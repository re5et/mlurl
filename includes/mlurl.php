<?php

error_reporting(E_ALL);

class mlurl{
	
	var $db 		=	false;
	var $authed		=	false;
	var $session	=	false;
	var $debug		=	false;
	var $msg 		=	false;
	var $tab 		=	false;
	var $url		=	false;
	var $salt		=	false;
	
	function __construct($config, $installing = false)
	{

		if(is_array($config) && !$installing)
		{
			$this->db = new db($config['database']);
			$this->session = new fake_session($config['session']);
			$this->url = $config['site_url'];
			$this->redirect_url = $config['redirect_url'];
			$this->salt = $config['password_salt'];
		}
		else
		{
			$this->session = new fake_session(array(
				'name'	=>	'mlurl_install'
			));
			return null;
		}
		if(isset($_GET['mlurl']))
		{
			if(is_array($this->session->msg))
			{
				$this->msg = $this->session->msg;
				$this->session->msg = false;
			}
			if($installing)
			{
				return null;
			}
			if(isset($_GET['logout']))
			{
				$this->logout();
			}
			
			$this->authed = $this->check_auth();
			
			$this->set_and_load_tab();
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
		
		if($email == 'guest' && $this->get_option('guests_can_make_urls'))
		{
			return true;
		}
		
		if(isset($_GET['auth_token']))
		{
			$this->auth_token_login();
		}
		
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
			$q = "SELECT id FROM {$this->db->prefix}users WHERE email = '{$email}' AND password = '{$password}'";
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
			$this->login($_POST['email'], sha1($this->salt . $_POST['password']));
		}
		else
		{
			$this->set_and_load_tab();
		}
	}
	
	function auth_token_login()
	{
		$token = $this->db->escape($_GET['auth_token']);
		$q = "SELECT * FROM {$this->db->prefix}auth_tokens WHERE auth_token = '{$token}'";
		$results = $this->db->row($q);
		$user = $results['user_id'];
		if($user > 0)
		{
			$q = "SELECT * FROM {$this->db->prefix}users WHERE id = '{$user}'";
			$user_info = $this->db->row($q);
			$this->session->email = $user_info['email'];
			$this->session->password = $user_info['password'];

			$q = "DELETE FROM {$this->db->prefix}auth_tokens WHERE auth_token = '{$token}'";
			$this->db->query($q);

			$this->add_msg('You are logged in using an authorization token, you should update your password.', 'success');
			$this->redirect($this->url . '/?mlurl&tab=account');
		}
		else
		{
			$this->add_msg('Invalid authorization token, did you already use it up?');
			return false;
		}
	}
	
	function logout()
	{
		$this->session->dump();
		$this->add_msg('Logged out successfully', 'success');
		$this->redirect($this->url . '/?mlurl');
	}
	
	function get_perm()
	{
		if($this->session->email == 'guest' && $this->get_option('guests_can_make_urls'))
		{
			return 1;
		}
		$q = "SELECT permission FROM {$this->db->prefix}users WHERE email = '{$this->session->email}'";
		return (int) $this->db->value($q);
	}
	
	function perm_check($level)
	{
		if($this->get_perm() < $level || !$this->authed)
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
			'edit_user',
			'delete_user',
			'login',
			'forgot_password',
			'register',
			'installer'
		);
		
		if(!$this->tab)
		{
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
	
	function update_mlurl($target, $name = false, $editting = false)
	{
		if($editting)
		{
			$editting = (int) $editting;
		}
		
		$target = $this->weak_url_check($target);
		
		$target = $this->db->escape($target);
		
		$name = str_replace(' ', '_', $this->db->escape($name));
		
		$check = false;

		if($name)
		{
			preg_match('/[\d\w\s-]+/', $name, $matches);
			if($matches[0] != $name)
			{
				$this->add_msg('The name you provided has invalid characters.  Please limit this to letters, numbers, hyphens and underscores.', 'error');
				return null;
			}
			if(preg_match('/^[a-f0-9]+$/i', $name))
			{
				$id = hexdec($name);
			}
			else
			{
				$id = 0;
			}
			$q = "SELECT id FROM {$this->db->prefix}urls WHERE named = '{$name}' OR id = '{$id}' LIMIT 1";
			$check = $this->db->value($q);
		}
		
		if(!$check && !$editting)
		{
			$insert = "INSERT INTO {$this->db->prefix}urls VALUES('','{$target}','{$name}')";
			$this->db->query($insert);
			$url_ok = false;
			while(!$url_ok)
			{
				$hex_check = dechex($this->db->insert_id);
				$q = "SELECT id FROM {$this->db->prefix}urls WHERE named = '{$hex_check}'";
				if($this->db->query($q))
				{
					$q = "DELETE FROM {$this->db->prefix}urls WHERE id = '{$this->db->insert_id}'";
					$this->db->query($q);
					$this->db->query($insert);
				}
				else
				{
					$url_ok = true;
				}
			}
			$this->add_msg('Added mlurl: ' . $this->make_link($this->db->insert_id), 'success');
		}
		elseif($check && !$editting)
		{
			$this->add_msg("This url is either already in the system, or the name specified is already taken: " . $this->make_link($check, $name), 'error');
		}
		elseif($editting > 0)
		{
			if($name != '')
			{
				$q = "SELECT id FROM {$this->db->prefix}urls WHERE id != '{$editting}' AND named = '{$name}'";
				if($this->db->value($q))
				{
					$this->add_msg("The name '$name' is already taken, sorry.", 'error');
					return null;
				}
			}

			$q = "UPDATE {$this->db->prefix}urls SET target = '{$target}', named = '{$name}' WHERE id = '{$editting}'";
			$this->db->query($q);
			$this->add_msg('Updated mlurl.', 'success');
		}
		if(isset($_GET['api']))
		{
			$this->api_output($this->db->insert_id);
		}
	}
	
	function api_output($id)
	{		
		$output = array();
		if($id && $this->session->msg['type'] == 'success')
		{
			$output['status'] = 'success';
			$output['mlurl'] = $this->get_link($id);
			$output['id'] = $id;
		}
		else
		{
			$output['status'] = 'failed';
		}
		$output['msg'] = $this->session->msg['msg'];
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: text/json');		
		die(json_encode($output));
	}
	
	function update_user($email, $permission, $user_id = false)
	{
		$email = $this->db->escape($email);
		$permission = (int) $permission;
		
		$q = "SELECT id FROM {$this->db->prefix}users WHERE email = '{$email}'";
		if($user_id){
			$q .= " AND id != '{$user_id}'";
		}
		$email_check = $this->db->value($q);

		if(!$email_check)
		{
			if(!$user_id)
			{
				extract($this->get_new_password());
				$q = "INSERT INTO {$this->db->prefix}users VALUES('', '{$email}', '{$hash}', '{$permission}')";
				$this->db->query($q);
				$this->add_msg("New user created, email sent to $email", 'success');
				$email_text = "You have been granted a mlurl account by <a href=\"{$this->url}?mlurl\">{$this->url}?mlurl</a>.\n\n  You can login here: {$this->url}?mlurl \n\n  With the following password: $password";
				$this->mail("$this->url mlurl account", $email_text, $email);
			}
			else
			{
				$q = "UPDATE {$this->db->prefix}users SET email = '{$email}', permission = '$permission' WHERE id = '{$user_id}'";
				$this->db->query($q);
				$this->add_msg(htmlentities($email) . ' has been updated.', 'success');
			}
		}
		else
		{
			$this->add_msg('There is already a user with that email address', 'error');
		}			
	}
	
	function get_new_password($length = 8)
	{
		$data['password'] = substr(sha1(mt_rand()), 0, $length);
		$data['hash'] = sha1($this->salt . $data['password']);
		return $data;
	}
	
	function reset_password($user_id, $password = false, $send_email = false)
	{
		if(!$password)
		{
			extract($this->get_new_password());
		}
		else
		{
			$hash = sha1($this->salt . $password);
		}
		$q = "UPDATE {$this->db->prefix}users SET password = '{$hash}' WHERE id = '{$user_id}'";
		$this->db->query($q);
		if($send_email)
		{
			$msg  = 'Your password has been reset to: ' . $password;
			$msg .= "\n\nYou can login here: " . $this->url . '?mlurl&tab=login';
			$subject = "mlurl password reset";
			$mailto = false;
			if(!$this->session->email)
			{
				$mailto = $_POST['email_password_reset'];
			}
			$this->mail($subject, $msg, $mailto);
		}
	}
	
	function send_auth_token($id, $email)
	{
		$token = sha1(mt_rand());
		$q = "INSERT INTO {$this->db->prefix}auth_tokens VALUES('', '{$id}', '{$token}')";
		$this->db->query($q);
		
		$subject = "you forgot your mlurl password.";
		$msg = "Click here, it will be fine: " . $this->url . '/?mlurl&auth_token=' . $token;
		$this->mail($subject, $msg, $email); 
	}
	
	function get_option($name)
	{
		$name = $this->db->escape($name);
		$q = "SELECT value FROM {$this->db->prefix}options WHERE name = '{$name}'";
		return $this->db->value($q);
	}
	
	function set_option($name, $value)
	{
		$name = $this->db->escape($name);
		$value = $this->db->escape($value);
		if($this->get_option($name))
		{
			$q = "UPDATE {$this->db->prefix}options SET value = '{$value}' WHERE name = '{$name}'";
			$this->db->query($q);
		}
		else
		{
			$q = "INSERT INTO {$this->db->prefix}options VALUES('','{$name}','{$value}')";
			$this->db->query($q);			
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
		$link = $this->redirect_url . '/';
		
		$q = "SELECT named FROM {$this->db->prefix}urls WHERE id = '{$id}'";
		$name = $this->db->value($q);
		
		$link .= ($name) ? $name : dechex($id);
		return $link;
	}
	
	function get_target($id)
	{
		$q = "SELECT target FROM {$this->db->prefix}urls WHERE id = '{$id}' LIMIT 1";
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
		
		$q = "SELECT id FROM {$this->db->prefix}urls WHERE named = '{$requested}' LIMIT 1";
		
		$named_check = $this->db->value($q);
		
		if($named_check)
		{
			$id = $named_check;
		}
		else
		{
			$requested = hexdec($requested);
			$q = "SELECT id FROM {$this->db->prefix}urls WHERE id = '$requested'";
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
			$this->redirect($target);
		}
		else
		{
			$this->redirect($this->url . '?mlurl');
		}
		
	}
	
	function record_hit($id)
	{
		$mlurl = $id;
		
		if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $this->url) === 0)
		{
			return null; // don't record a hit that came from within the mlurl admin 
		}
		
		$server = array(
			'REMOTE_ADDR', 'HTTP_USER_AGENT', 'HTTP_USER_AGENT', 'HTTP_REFERER'
		);
		
		foreach($server as $key)
		{
			$$key = (isset($_SERVER[$key])) ? $this->db->escape($_SERVER[$key]) : false;
		}
		
		$q = "INSERT INTO {$this->db->prefix}hits VALUES('', '{$id}', '{$REMOTE_ADDR}', '{$HTTP_USER_AGENT}', '{$HTTP_USER_AGENT}', '{$HTTP_REFERER}', '{$HTTP_USER_AGENT}')";
		$this->db->query($q);
		
	}
	
	function make_link($id)
	{
		$link = $this->get_link($id);
		$target = htmlentities($this->get_target($id));
		return '<a href="'. $link .'">'. $link .'</a><br/><span>( goes to: ' . $target . ' )</span>';
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
	
	function mail($subject, $msg, $email = false)
	{
		if(!$email)
		{
			$email = $this->session->email;
		}
		$url = split('/', $this->url);
		$url = $url[2];
		$from = "From: mlurl admin <dontreply@$url>";
		mail($email, $subject, $msg, $from);
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
		if(is_array($this->msg))
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
