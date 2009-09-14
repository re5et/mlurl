<?php

	if(isset($_POST['login']))
	{
		$login_check = $this->login($_POST['email'], sha1($_POST['password']));
		if($login_check)
		{
			$this->add_msg('logged in succesfully.', 'success');
		}
		else
		{
			$this->add_msg('login failed.', 'error');
		}
		
		$this->redirect();
	}
	
	$this->view('login');

?>