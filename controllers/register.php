<?php

	if($this->get_option('allow_registration'))
	{
		if(isset($_POST['register']))
		{
			if(empty($_POST['register_email']))
			{
				$this->add_msg('Please include your email address.', 'error');
				$this->redirect();
			}
			$email = $this->db->escape($_POST['register_email']);
			$permission = $this->get_option('default_permission');
			$this->update_user($email, $permission);
			$this->redirect();
		}
	
		$this->view('register');
	}
	else
	{
		$this->huh();
	}



?>