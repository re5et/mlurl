<?php

	$this->perm_check(1);

	if(isset($_REQUEST['url_to_shorten']))
	{
		$name = (isset($_REQUEST['name_it'])) ? $_REQUEST['name_it'] : false;
		$this->update_mlurl($_REQUEST['url_to_shorten'], $name);
		
		if(!isset($_REQUEST['api']))
		{
			$this->redirect($this->url . '/?mlurl');
		}
	}
	else
	{
		$this->view('create_new');
	}

?>