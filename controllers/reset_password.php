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
			$this->reset_password($user_id);
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