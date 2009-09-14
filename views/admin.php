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
			<option value="2">make links + view stats</option>
			<option value="1">make links</option>
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
			<span>
				<a href="<?php echo $this->url ?>/?mlurl&tab=edit_user&user=<?php echo urlencode($user['email']) ?>">edit</a>
			</span>
			<span>
				<a href="<?php echo $this->url ?>/?mlurl&tab=delete_user&user=<?php echo urlencode($user['email']) ?>">delete</a>
			</span>			
			<span>
				<?php echo $user['email'] ?>
			</span>
		</li>
	<?php endforeach ?>
</ul>
<h2>mlurl options</h2>
<?php $this->view('footer') ?>