<?php $this->view('header') ?>
<h2>editting <?php echo $email ?></h2>
<form action="<?php echo $this->url ?>/?mlurl&tab=edit_user&user=<?php echo $id ?>" method="post">
	<p>
		<label for="email_address">email address:</label>
		<input type="text" name="email_address" id="email_address" value="<?php echo $email ?>" />
	</p>
	<p>
		<label for="permission">permission:</label>
		<select name="permission" id="permission">
			<option value="9"<?php if($permission >= 9) echo ' selected="selected"' ?>>administrate</option>
			<option value="2"<?php if($permission == 2) echo ' selected="selected"' ?>>make links + view stats</option>
			<option value="1"<?php if($permission == 1) echo ' selected="selected"' ?>>make links</option>
		</select>
	</p>
	<p class="submit">
		<input type="submit" name="update_user" value="update user &raquo;" /> or <a id="reset-password" href="<?php echo $this->url ?>/?mlurl&tab=edit_user&user=<?php echo $id ?>&reset_password">reset password</a>
	</p>
</form>
<?php $this->view('footer') ?>