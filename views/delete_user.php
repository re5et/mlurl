<?php $this->view('header') ?>
<h2>Are you quite sure that you want to delete <?php echo $email ?>?</h2>
<form action="<?php echo $this->url ?>/?mlurl&tab=delete_user" method="post">
	<p class="submit">
		<input type="hidden" name="delete_user" value="<?php echo $email ?>" />
		<input type="submit" name="confirm_user_delete" value="for real, delete <?php echo $email ?>" />
	</p>
</form>
<?php $this->view('footer') ?>
