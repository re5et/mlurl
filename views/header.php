<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>mlurl &raquo; <?php echo str_replace('_', ' ', $this->tab) ?></title>
		<link rel="stylesheet" href="<?php echo $this->url ?>/assets/css/base.css" type="text/css" media="screen" charset="utf-8" />
	</head>
	<body>
		<div id="container">
			<div id="header">
				<h1 id="title">mlurl</h1>
				<h2 id="subtitle">my little urls</h2>
				<?php $this->view('tabs') ?>
			</div>
			<div id="body">
				<?php $this->show_msgs() ?>
