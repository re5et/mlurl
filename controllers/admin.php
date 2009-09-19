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

	if(isset($_POST['update_options']))
	{
		foreach(array('allow_registration', 'default_permission', 'guests_can_make_urls') as $option)
		{
			if(isset($_POST['option'][$option]))
			{
				$option_value = $this->db->escape($_POST['option'][$option]);
				$q = "UPDATE {$this->db->prefix}options SET value = '{$option_value}' WHERE name = '{$option}'";
				$this->db->query($q);
			}
		}
		$this->add_msg('Options updated.', 'success');
		$this->redirect();
	}

	$q = "SELECT * FROM {$this->db->prefix}users";
	$data['users'] = $this->db->query($q);
	$q = "SELECT * FROM {$this->db->prefix}options";
	$options = $this->db->query($q);
	
	foreach($options as $option)
	{
		$data['options'][$option['name']] = $option['value'];
	}

	$this->view('admin', $data);

?>