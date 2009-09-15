<?php $this->view('header') ?>
<h2>URL to shorten:</h2>
<form action="<?php echo $this->url ?>/?mlurl" method="post">
	<p>
		<input type="text" name="url_to_shorten" value="http://" size="50" />
	</p>
	<p>
		<label for="name_it">optionally name it:</label>
		<input type="text" name="name_it" id="name_it"/>
		<input type="submit" value="shorten &raquo;" />
	</p>
</form>
<?php $this->view('footer') ?>