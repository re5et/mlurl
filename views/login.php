<?php $this->view('header') ?>
<?php if($this->session->login_error): ?>
<h2>Your login information is incorrect.</h2>
<?php endif ?>
<form action="<?php echo $this->url ?>/?mlurl" method="post">
	<p>
		<label for="email">email address:</label>
		<input type="text" id="email" name="email">
	</p>
	<p>
		<label for="password">password:</label>
		<input type="password" id="password" name="password">
	</p>
	<p>
		<input type="submit" name="login" value="Login &raquo;"></button>
	</p>
</form>
<?php $this->view('footer') ?>