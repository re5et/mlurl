<?php

	if(isset($_POST['reset_password']) && isset($_POST['email_password_reset']))
	{
		$email = $this->db->escape($_POST['email_password_reset']);
		
		$q = "SELECT id FROM users WHERE email = '{$email}'";
		$user_id = $this->db->value($q);
		
		if($user_id)
		{
			$new_pass = substr(sha1(mt_rand()), 0, 8);
			$pass_hash = sha1($new_pass);
			$q = "UPDATE users SET password = '{$pass_hash}' WHERE id = '$user_id'";
			$this->db->query($q);
			echo $new_pass;
			die;
			$this->add_msg("Found user $user_id", 'success');
		}
		else
		{
			$this->add_msg('Cannot find a user by that email address', 'error');
		}
		
		$this->redirect();
	}
	
	$this->view('reset_password');

?>