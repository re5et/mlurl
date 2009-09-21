<?php if(!isset($this)) die; ?>
<?php $this->view('header') ?>
<h2>mlurl installer</h2>
<?php if(isset($writable)): ?>
	<div id="msgs">
		<p class="msg error">
			<?php echo dirname($_SERVER['SCRIPT_FILENAME']) ?> is not writable.  You <em>can</em> complete the install, but you will have to create a few files on your own, or you can make this writable and <em>then</em> continue.
		</p>
	</div>
<?php endif ?>
<form action="<?php echo $this->url ?>/?mlurl" method="post">
	<p>
		<label for="site_url">site url:</label>
		<input type="text" name="site_url" id="site_url" value="<?php echo $this->url ?>"/>
	</p>
	<p>
		<label for="database_user">database username:</label>
		<input type="text" name="database_user" id="database_user" />
	</p>
	<p>
		<label for="database_pass">database password:</label>
		<input type="text" name="database_pass" id="database_pass" />
	</p>
	<p>
		<label for="database_name">database name:</label>
		<input type="text" name="database_name" id="database_name" />
	</p>
	<p>
		<label for="database_host">database host:</label>
		<input type="text" name="database_host" id="database_host" value="localhost" />
	</p>
	<p>
		<label for="database_prfx">database prefix:</label>
		<input type="text" name="database_prfx" id="database_prfx" value="mlurl_" />
	</p>	
	<p>
		<label for="admin_email">admin email:</label>
		<input type="text" name="admin_email" id="admin_email" />
	</p>
	<p class="submit">
		<input type="submit" name="install" value="install &raquo;" />
	</p>
</form>
<?php $this->view('footer') ?>
