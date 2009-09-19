<?php
	
	if(!file_exists('mlurl-config.php'))
	{
		$mlurl = new mlurl(false, true);
		$mlurl->url = 'http://' . $_SERVER['HTTP_HOST'] . str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
		$mlurl->tab = 'installer';
		$mlurl->set_and_load_tab();
		die;		
	}
	
?>