<?php

	$this->perm_check(10);
	
	if(isset($_POST['update_user']))
	{
		$this->update_user($_POST['email_address'], $_POST['permission'], $_GET['user']);
		$this->redirect();
	}
	elseif(isset($_GET['user']))
	{
		$id = (int) $_GET['user'];
		$q = "SELECT * FROM {$this->db->prefix}users WHERE id = '{$id}'";
		$user = $this->db->row($q);

		if(is_array($user))
		{
			if($user['permission'] < 10)
			{
				$this->view('edit_user', $user);
			}
			else
			{
				$this->add_msg($user['email'] . ' is far too powerful to be editted.', 'error');
				$this->redirect($this->url . '/?mlurl&tab=admin');
			}
		}
	}
	else
	{
		$this->huh();
	}
	
?>
