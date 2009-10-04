<?php

	if(isset($_POST['reset_password']) && isset($_POST['email_password_reset']))
	{
		$email = $this->db->escape($_POST['email_password_reset']);
		
		if($email == 'guest')
		{
			$this->huh();
		}

		$q = "SELECT id FROM {$this->db->prefix}users WHERE email = '{$email}'";
		$user_id = $this->db->value($q);

		if($user_id)
		{
			$this->send_auth_token($user_id, $email);
			$this->add_msg('An authorization token has emailed to '. htmlentities($email), 'success');
		}
		else
		{
			$this->add_msg('Cannot find a user by that email address', 'error');
		}
		
		$this->redirect();
	}
	
	$this->view('forgot_password');

?>