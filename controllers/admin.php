<?php

	$this->perm_check(10);

	if(isset($_POST['create_new_user']))
	{
		if(isset($_POST['email_address']) && !empty($_POST['email_address']) && isset($_POST['permission']))
		{
			$this->update_user($_POST['email_address'], $_POST['permission']);
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