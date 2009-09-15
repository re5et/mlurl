<?php

	$this->perm_check(9);
	if(isset($_POST['update_mlurl']))
	{
		$this->update_mlurl($_POST['url_to_update'], $_POST['name_to_update'], $_GET['mlurl_id']);
		$this->redirect();
	}
	elseif(isset($_GET['mlurl_id']))
	{
		$id = (int) $_GET['mlurl_id'];
		$q = "SELECT * FROM urls WHERE id = '{$id}'";
		$mlurl = $this->db->row($q);
		if(is_array($mlurl)){
			$data = array(
				'mlurl_id'	=>	$mlurl['id'],
				'link'		=>	$this->get_link($mlurl['id']),
				'target'	=>	$mlurl['target'],
				'name'		=>	$mlurl['named']
			);
			$this->view('edit_mlurl', $data);
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