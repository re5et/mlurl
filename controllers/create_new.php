<?php

	$this->perm_check(1);

	if(isset($_POST['url_to_shorten']))
	{
		$this->create_mlurl($_POST['url_to_shorten'], $_POST['name_it']);

		$this->redirect();
	}
	else
	{
		$this->view('create_new');
	}

?>