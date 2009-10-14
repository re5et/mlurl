<?php

	$this->perm_check(3);

	$q = "SELECT * FROM {$this->db->prefix}hits";

	$data = array();

	if(isset($_GET['mlurl_id']))
	{
		$id = (int) $_GET['mlurl_id'];
		$q .= " WHERE mlurl_id = '{$id}'";
		$data = array(
			'mlurl'			=>	$id,
			'mlurl_link'	=>	$this->get_link($id),
			'mlurl_target'	=>	htmlentities($this->get_target($id))
		);
	}
	
	$results = $this->db->query($q);
	
	if(is_array($results))
	{
	
		$stats = array();
		$stats['referer'] = array();
		
		foreach($results as $result)
		{
			if(!array_key_exists($result['referer'], $stats['referer']))
				$stats['referer'][$result['referer']] = 1;
			else
				$stats['referer'][$result['referer']]++;
		}
		
		arsort($stats['referer']);
		
		$data['hits'] = $results;
		$data['referers'] = $stats['referer'];
		
	}
	
	$this->view('stats', $data);

?>
