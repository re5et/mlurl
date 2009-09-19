<?php $this->view('header') ?>
<h2>Register an account</h2>
<form action="<?php echo $this->url ?>/?mlurl&tab=register" method="post">
	<p>
		<label for="register_email">email address:</label>
		<input type="text" name="register_email" id="register_email" /><input type="submit" name="register" value="register account &raquo;" />
	</p>
</form>
<?php $this->view('footer') ?>