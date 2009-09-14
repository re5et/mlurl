<?php

	$this->perm_check(1);

	if(isset($_POST['update_account_info']))
	{
		$q = "SELECT id FROM users WHERE email = '{$this->session->email}'";
		$user_id = $this->db->value($q);
		
		$email = empty($_POST['email_address']) ? $this->session->email : $this->db->escape($_POST['email_address']);
		$this->session->email = $email;
		
		if($_POST['password'] != '')
		{
			if($_POST['password'] == $_POST['password_confirm'])
			{
				$password = sha1($_POST['password']);
				$this->session->password = $password;
			}
			else
			{
				$this->add_msg('Password and password confirm must match.', 'error');
				$this->redirect();
			}
		}
		else
		{
			$password = $this->session->password;
		}
		$q = "UPDATE users SET email = '{$email}', password = '{$password}' WHERE id = '{$user_id}'";
		$this->db->query($q);
		$this->add_msg('Updated your account information.', 'success');
		$this->redirect();
	}

	$this->view('account');

?>