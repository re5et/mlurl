<?php

	if($this->authed)
	{
		$this->redirect($this->url . '/?mlurl');
	}

	if(isset($_POST['login_as_guest']) && $this->get_option('guests_can_make_urls'))
	{
		$this->session->email = 'guest';
		$this->session->password = '';
		$this->redirect();
	}

	if(isset($_POST['login']) && isset($_POST['email']) && isset($_POST['password']))
	{
		if(empty($_POST['email']) || empty($_POST['password']))
		{
			$this->add_msg('Please include both your email and password.', 'error');
		}
		else
		{
			$login_check = $this->login($_POST['email'], sha1($this->salt . $_POST['password']));
			if($login_check)
			{
				$this->add_msg('logged in succesfully.', 'success');
			}
			else
			{
				$this->add_msg('login failed.', 'error');
			}
		}

		
		$this->redirect();
	}
	
	$this->view('login');

?>