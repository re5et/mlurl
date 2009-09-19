<?php

	if(isset($_POST['install']))
	{
		$db = new db(array(
			'db_host'	=>	$_POST['database_host'],
			'db_user'	=>	$_POST['database_user'],
			'db_pass'	=>	$_POST['database_pass'],
			'db_name'	=>	$_POST['database_name'],
			'prefix' 	=>	$_POST['database_prfx']
		));
		
		if(!$db->link)
		{
			$this->add_msg('Database connection failed.  Please check your settings and try again.', 'error');
		}
		else
		{
			if(!empty($_POST['admin_email']))
			{
				$config_output  = "<?php\n";
				$config_output .= "\t //general settings\n";
				$config_output .= "\t" . '$config' . "['site_url'] = '" . addcslashes($_POST['site_url'], "'") . "';\n";
				$config_output .= "\n\t //database config\n";
				$config_output .= "\t" . '$config' . "['database']['db_host'] = '" . addcslashes($db->db_host, "'") . "';\n";
				$config_output .= "\t" . '$config' . "['database']['db_user'] = '" . addcslashes($db->db_user, "'") . "';\n";
				$config_output .= "\t" . '$config' . "['database']['db_pass'] = '" . addcslashes($db->db_pass, "'") . "';\n";
				$config_output .= "\t" . '$config' . "['database']['db_name'] = '" . addcslashes($db->db_name, "'") . "';\n";
				$config_output .= "\t" . '$config' . "['database']['prefix'] = '" . addcslashes($db->prefix, "'") . "';\n";
				$config_output .= "\n\t //session config\n";
				$config_output .= "\t" . '$config' . "['session']['name'] = '" . 'mlurl_'. sha1($_SERVER['SCRIPT_FILENAME']) . "';\n";
				$config_output .= '?>';
				
				$f = "mlurl-config.php";
				$fh = fopen($f, 'w');
				if(!$fh)
				{
					$this->add_msg('Could not write to this directory.  Please create a file called "mlurl-config.php" here and place the following in it:', 'erorr');
				}
				else
				{
					fwrite($fh, $config_output);
					fclose($fh);
					
					$this->db = $db;
					
					$querys[] = "CREATE TABLE {$this->db->prefix}users (id INT UNSIGNED AUTO_INCREMENT, email TINYTEXT NOT NULL, password VARCHAR(40) NOT NULL, permission TINYINT UNSIGNED NOT NULL, PRIMARY KEY(id))";
					$querys[] = "CREATE TABLE {$this->db->prefix}options (id INT UNSIGNED AUTO_INCREMENT, name TINYTEXT NOT NULL, value TEXT NOT NULL, PRIMARY KEY(id))";
					$querys[] = "CREATE TABLE {$this->db->prefix}urls (id INT UNSIGNED AUTO_INCREMENT, target TEXT NOT NULL, named TINYTEXT NOT NULL, PRIMARY KEY(id))";
					$querys[] = "CREATE TABLE {$this->db->prefix}hits (id INT UNSIGNED AUTO_INCREMENT, mlurl_id INT UNSIGNED, ip_address VARCHAR(15) NOT NULL, browser TINYTEXT NOT NULL, browser_version TINYTEXT NOT NULL, referer TEXT NOT NULL, operating_system TEXT NOT NULL, PRIMARY KEY(id))";
	
					foreach($querys as $query)
					{
						$this->db->query($query);
					}
					
					$this->set_option('allow_registration', false);
					$this->set_option('default_permission', 2);
					$this->set_option('guests_can_make_urls', false);
					$this->set_option('mlurl_version', 1);
	
					$this->update_user($_POST['admin_email'], 10);
					$this->session->delete();
					die('The install has completed, check your email, then <a href="./?mlurl">login</a>.');
				}
			}
			else
			{
				$this->add_msg('Please include an admin email address.', 'error');
			}
		}
		
		$this->redirect();
	}
	
	$this->view('installer');

?>