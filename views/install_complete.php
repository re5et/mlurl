<?php $this->view('header') ?>
<h2>mlurl install complete</h2>
<h4>Check your email for login information.  <a href="<?php echo $this->url ?>/?mlurl&amp;tab=login">Click here to login</a>.</h4>
<?php if(isset($file_write_fail)): ?>
	<div id="msgs">
		<p class="msg error">
			Your config file was not saved because the location could not be written to.  Please create a file called 'mlurl-config.php' in the base of your mlurl install, and put the following in it:
		</p>
		<pre>
<?php echo $config_output ?></pre>
		<p class="msg error">
			Your .htaccess file was not saved because the location could not be written to.  Please create a file called '.htaccess' in the base of your mlurl install, and put the following in it:
		</p>
		<pre>
<?php echo $htaccess_output ?></pre>
	</div>
<?php endif ?>
<?php $this->view('footer') ?>
