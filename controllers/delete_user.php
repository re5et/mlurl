<?php

	$this->perm_check(10);
	
	if(isset($_GET['user']))
	{
		$user_email = $this->db->escape($_GET['user']);
		$q = "SELECT id FROM users WHERE email = '{$user_email}'";
		$user_check = $this->db->query($q);
		if($user_check)
		{
			$data = array(
				'email'	=> htmlentities($user_email)
			);
			$this->view('delete_user', $data);
		}
		else
		{
			$this->add_msg('Could not find that user, so nothing happened.', 'error');
			$this->redirect($this->url . '/?mlurl&tab=admin');
		}
	}
	elseif(isset($_POST['confirm_user_delete']))
	{
		if(isset($_POST['delete_user']))
		{
			$user_email = $this->db->escape($_POST['delete_user']);
			$q = "SELECT permission FROM users WHERE email = '{$user_email}'";
			if($this->db->value($q) > 9)
			{
				$this->add_msg('this user is undeletable, too awesome.', 'error');
			}
			else
			{
				$q = "DELETE FROM users WHERE email = '{$user_email}'";
				$this->db->query($q);
				$this->add_msg("$user_email has been deleted", 'success');
			}
			$this->redirect($this->url . '/?mlurl&tab=admin');
		}
		else
		{
			$this->huh();
		}
	}
	else
	{
		$this->huh();
	}


?>