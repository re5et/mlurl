<?php

foreach(array('fake_session', 'mlurl', 'db', 'installer') as $include)
{
	include("includes/$include.php");
}
if(file_exists('mlurl-config.php'))
{
	include('mlurl-config.php');
	new mlurl($config);
}
else
{
	die('Something is wrong, and for that I am sorry =(');
}

?>