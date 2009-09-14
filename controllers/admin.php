<?php

	$this->perm_check(10);

	if(isset($_POST['create_new_user']))
	{
		if(isset($_POST['email_address']) && !empty($_POST['email_address']) && isset($_POST['permission']))
		{
			$email = $this->db->escape($_POST['email_address']);
			
			$q = "SELECT id FROM users WHERE email = '{$email}'";
			$email_check = $this->db->value($q);

			if(!$email_check)
			{
				$permission = (int) $_POST['permission'];
				if($permission > 9)
					$permission = 9;
				$password = substr(sha1(mt_rand()), 0, 8);
				$password_hash = sha1($password);
				
				$q = "INSERT INTO users VALUES('', '{$email}', '{$password_hash}', '{$permission}')";
				$this->db->query($q);
				
				$email_text = "You have been granted a mlurl account by <a href=\"$this->url\">$this->url</a>.\n\n  You can login here: $this->url \n\n  With the following password: $password";
	
				mail($email, "$this->url mlurl account", $email_text);
				
				$this->add_msg("New user created, email sent to $email $password", 'success');
				
			}
			else
			{
				$this->add_msg('User not created, there is already a user with that email address', 'error');
			}
			
		}
		else
		{
			$this->add_msg('Make sure you are including an email address, and a permission level.', 'error');
		}
		
		$this->redirect();
	}

	$q = "SELECT * FROM users";
	$data['users'] = $this->db->query($q);

	$this->view('admin', $data);

?>