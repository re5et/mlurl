<?php $this->view('header') ?>
<h2>create new user</h2>
<form action="<?php echo $this->url ?>/?mlurl&tab=admin" method="post">
	<p>
		<label for="email_address">email address:</label>
		<input type="text" name="email_address" id="email_address" />
	</p>
	<p>
		<label for="permission">permission:</label>
		<select name="permission" id="permission">
			<option value="9">administrate</option>
			<option value="3">make links + view stats</option>
			<option value="2">make links</option>
		</select>
	</p>
	<p class="submit">
		<input type="submit" name="create_new_user" value="create new user &raquo;" />
	</p>
</form>
<h2>current users</h2>
<ul>
	<?php foreach($users as $user): ?>
		<li>
			<?php if($user['permission'] < 10): ?>
				<span>
					<a href="<?php echo $this->url ?>/?mlurl&tab=edit_user&user=<?php echo $user['id'] ?>">edit</a>
				</span>
				<span>
					<a href="<?php echo $this->url ?>/?mlurl&tab=delete_user&user=<?php echo $user['id'] ?>">delete</a>
				</span>
			<?php else: ?>
				<span>admin</span>
			<?php endif ?>			
			<span>
				<?php echo $user['email'] ?>
			</span>
		</li>
	<?php endforeach ?>
</ul>
<h2>mlurl options</h2>
<form action="<?php echo $this->url ?>/?mlurl&tab=admin" method="post">
	<ul>
		<li>
			<label for="guests_can_make_urls">allow guests to make links</label>
			<select name="option[guests_can_make_urls]" id="guests_can_make_urls">
				<option value="1"<?php if($options['guests_can_make_urls']) echo ' selected="selected"'?>>yes</option>
				<option value="0"<?php if(!$options['guests_can_make_urls']) echo ' selected="selected"'?>>no</option>
			</select>
		</li>	
		<li>
			<label for="allow_registration">allow registration</label>
			<select name="option[allow_registration]" id="allow_registration">
				<option value="1"<?php if($options['allow_registration']) echo ' selected="selected"' ?>>yes</option>
				<option value="0"<?php if(!$options['allow_registration']) echo ' selected="selected"' ?>>no</option>
			</select>
		</li>
		<li>
			<label for="default_permission">default permission</label>
			<select name="option[default_permission]" id="default_permission">
				<option value="2"<?php if($options['default_permission'] == 2) echo ' selected="selected"' ?>>make links</option>
				<option value="3"<?php if($options['default_permission'] == 3) echo ' selected="selected"' ?>>make links + view stats</option>
				<option value="9"<?php if($options['default_permission'] == 9) echo ' selected="selected"' ?>>administrate</option>
			</select>		
		</li>
		<?php if(0): ?>
		<li>redirection interstitial: disable/enable(w/?preview)/force</li>
		<?php endif ?>
	</ul>
	<p class="submit">
		<input type="submit" name="update_options" value="update options &raquo;" />
	</p>
</form>
<?php $this->view('footer') ?>