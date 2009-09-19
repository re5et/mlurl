			<?php if($this->tab !== 'installer'): ?>
				<?php $perm = $this->get_perm() ?>
				<ul id="tabs">
					<?php if($this->authed): ?>
						<li><a <?php $this->is_active_tab('create_new') ?>href="<?php echo $this->url ?>/?mlurl&tab=create_new">create new</a></li>
						<?php if($perm >= 3): ?>
							<li><a <?php $this->is_active_tab('stats') ?>href="<?php echo $this->url ?>/?mlurl&tab=stats">stats</a></li>
						<?php endif ?>
						<?php if($perm >= 3): ?>
							<li><a <?php $this->is_active_tab('mlurls', 'edit_mlurl') ?>href="<?php echo $this->url ?>/?mlurl&tab=mlurls">mlurls</a></li>
						<?php endif ?>
						<?php if($perm > 1): ?>
							<li><a <?php $this->is_active_tab('account') ?>href="<?php echo $this->url ?>/?mlurl&tab=account">account</a>
						<?php endif ?>
						<?php if($perm >= 10): ?>
							<li><a <?php $this->is_active_tab('admin', 'delete_user', 'edit_user') ?>href="<?php echo $this->url ?>/?mlurl&tab=admin">admin</a>
						<?php endif ?>
						<li><a id="logout" href="<?php echo $this->url ?>/?mlurl&logout">logout</a></li>
					<?php else: ?>
						<li><a <?php $this->is_active_tab('login') ?>href="<?php echo $this->url ?>/?mlurl&tab=login">login</a></li>
						<?php if($this->get_option('allow_registration')): ?>
							<li><a <?php $this->is_active_tab('register') ?>href="<?php echo $this->url ?>/?mlurl&tab=register">register</a></li>
						<?php endif ?>
						<li><a <?php $this->is_active_tab('reset_password') ?>href="<?php echo $this->url ?>/?mlurl&tab=reset_password">reset password</a></li>
					<?php endif ?>
				</ul>
			<?php endif ?>