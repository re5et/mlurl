<?php
	
	$this->perm_check(2);
	
	if(isset($_GET['action']) && isset($_GET['mlurl_id']))
	{
		$this->perm_check(9);
		$id = (int) $_GET['mlurl_id'];

		if($_GET['action'] == 'edit')
		{
			$q = "SELECT * FROM urls WHERE id = {$id}";
			$mlurl = $this->db->query($q);
			print_r($mlurl);
		}
		if($_GET['action'] == 'delete')
		{
			$q = "DELETE FROM urls WHERE id = '{$id}'";
			$this->db->query($q);
			$q = "DELETE FROM hits WHERE mlurl_id = '{$id}'";
			$this->db->query($q);
			$this->add_msg('Successfully deleted the mlurl and associated statistics.', 'success');
			$this->redirect($this->url . '/?mlurl&tab=mlurls');
		}		
	}
	else
	{
		$q = "SELECT * FROM urls";
		$this->current_urls = $this->db->query($q);
		$this->view('mlurls');
	}

?>