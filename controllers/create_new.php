<?php

	$this->perm_check(1);

	if(isset($_POST['url_to_shorten']))
	{
		$url = $this->db->escape($_POST['url_to_shorten']);
		$named = (isset($_POST['name_it']) && !empty($_POST['name_it'])) ? $this->db->escape(urlencode($_POST['name_it'])) : false;
		
		$q = "SELECT id FROM urls WHERE target = '{$url}'";
		if($named)
		{
			$id = hexdec($named);
			$q .= " OR named = '{$named}' OR id = '{$id}'";
		}
		$q .= " LIMIT 1";
		
		$check = $this->db->value($q);
		
		if(!$check)
		{
			$q = "INSERT INTO urls VALUES('','{$url}','{$named}')";
			$this->db->query($q);
			$this->add_msg('Added url: ' . $this->get_target($this->db->insert_id) . $this->get_link($this->db->insert_id, $named), 'success');
		}
		else
		{
			$this->add_msg("This url is either already in the system, or the name specified is already taken: " . $this->make_link($check, $named), 'error');
		}
		
		$this->redirect();
	}
	else
	{
		$this->view('create_and_edit');
	}

?>