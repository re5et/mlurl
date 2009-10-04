<?php $this->view('header') ?>
<h2>Forgotten your password?</h2>
<form action="<?php echo $this->url ?>/?mlurl&tab=forgot_password" method="post">
	<p>
		<label for="email_password_reset">email address:</label>
		<input type="text" name="email_password_reset" id="email_password_reset" />
		<input type="submit" name="reset_password" value="help me &raquo;" />
	</p>
</form>
<?php $this->view('footer') ?>