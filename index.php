<?php

include('includes/fake_session.php');
include('includes/little_url.php');
include('includes/db.php');

$lil_url = new little_url(new db(), new fake_session(array(
	'name'		=>	'two_clurl',
	'domain'	=>	''
)));

?>