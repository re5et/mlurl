<?php $this->view('header') ?>
<h2>
	stats for <a href="<?php echo $link ?>">
		<?php echo $link ?>
	</a>
	<span id="goes-to">
		goes to: <a href="<?php echo $target ?>">
			<?php echo substr($target, 0, 80) ?><?php if(strlen($target) > 80) echo '...' ?>
		</a>
	</span>
</h2>
<form action="<?php echo $this->url ?>/?mlurl&tab=edit_mlurl&mlurl_id=<?php echo $mlurl_id ?>" method="post">
	<p>
		<input type="text" name="url_to_update" value="<?php echo $target ?>" size="50" />
	</p>
	<p>
		<label for="name_it">optionally name it:</label>
		<input type="text" name="name_to_update" id="name_it" value="<?php echo $name ?>"/>
		<input type="submit" name="update_mlurl" value="update mlurl &raquo;" />
	</p>
</form>
<?php $this->view('footer') ?>