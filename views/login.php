<?php $this->view('header') ?>
<?php if($this->session->login_error): ?>
<h2>Your login information is incorrect.</h2>
<?php endif ?>
<form action="<?php echo $this->url ?>/?mlurl&tab=login" method="post">
	<p>
		<label for="email">email address:</label>
		<input type="text" id="email" name="email">
	</p>
	<p>
		<label for="password">password:</label>
		<input type="password" id="password" name="password">
	</p>
	<p class="submit">
		<input type="submit" name="login" value="Login &raquo;" />
		<?php if($this->get_option('guests_can_make_urls')): ?>
			or <input type="submit" name="login_as_guest" value="Login as guest &raquo;" />
		<?php endif ?>
	</p>
</form>
<?php $this->view('footer') ?>