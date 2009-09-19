<?php $this->view('header') ?>
<h2>Update your account</h2>
<form action="<?php echo $this->url ?>/?mlurl&tab=account" method="post">
	<p>
		<label for="email_address">email address:</label>
		<input type="text" name="email_address" id="email_address" value="<?php echo $this->session->email ?>"/>
	</p>
	<p>
		<label for="password">(optional) new password:</label>
		<input type="password" name="password" id="password" />
	</p>
	<p>
		<label for="password_confirm">new password confirm:</label>
		<input type="password" name="password_confirm" id="password_confirm" />
	</p>
	<p class="submit">
		<input type="submit" name="update_account_info" value="update account information &raquo;" />
	</p>	
</form>
<?php $this->view('footer') ?>