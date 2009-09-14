<?php $this->view('header') ?>
<?php if(is_array($this->current_urls)): ?>
<h2>current mlurls:</h2>
	<table>
		<thead>
			<tr>
				<th class="medium">link</th>
				<th class="wide">goes to</th>
				<th class="skinny">stats</th>
				<?php if($this->get_perm() >= 9): ?>
					<th class="skinny">edit</th>
					<th class="skinny">delete</th>
				<?php endif ?>
			</tr>
		</thead>
		<?php foreach($this->current_urls as $url): ?>
			<?php
				$link 	=	$this->get_link($url['id']);
				$target	=	$this->get_target($url['id']);
			?>
			<tr>
				<td><a href="<?php echo $link ?>"><?php echo $link ?></a></td>
				<td><a href="<?php echo $target ?>"><?php echo $target ?></a></td>
				<td><a href="<?php echo $this->url ?>/?mlurl&tab=stats&mlurl_id=<?php echo $url['id'] ?>">stats</a></td>
				<?php if($this->get_perm() >= 9): ?>
					<td><a href="<?php echo $this->url ?>/?mlurl&tab=mlurls&action=edit&mlurl_id=<?php echo $url['id'] ?>">edit</a></td>
					<td><a href="<?php echo $this->url ?>/?mlurl&tab=mlurls&action=delete&mlurl_id=<?php echo $url['id'] ?>">delete</a></td>
				<?php endif ?>
			</tr>
		<?php endforeach ?>
	</table>
<?php else: ?>
	<h2>There are currently no mlurls.</h2>
<?php endif ?>
<?php $this->view('footer') ?>