<?php

	if(isset($_POST['reset_password']) && isset($_POST['email_password_reset']))
	{
		$email = $this->db->escape($_POST['email_password_reset']);
		
		if($email == 'guest')
			$this->huh();
		
		$q = "SELECT id FROM {$this->db->prefix}users WHERE email = '{$email}'";
		$user_id = $this->db->value($q);
		
		if($user_id)
		{
			$new_pass = substr(sha1(mt_rand()), 0, 8);
			$pass_hash = sha1($this->salt . $new_pass);
			$q = "UPDATE {$this->db->prefix}users SET password = '{$pass_hash}' WHERE id = '$user_id'";
			$this->db->query($q);
			$subject = $this->url . " mlurl password reset";
			$contents = "Your mlurl password has been reset to: " . $new_pass . "\n\nhope you were the one who requested it...\n\nYou can login here: " . $this->url . '?mlurl&tab=login';
			$this->mail($subject, $contents, $email);
			$this->add_msg('Your password has been reset and emailed to '. htmlentities($email), 'success');
		}
		else
		{
			$this->add_msg('Cannot find a user by that email address', 'error');
		}
		
		$this->redirect();
	}
	
	$this->view('reset_password');

?>