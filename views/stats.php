<?php $this->view('header') ?>
<?php if(isset($mlurl)): ?>
	<h2>
		stats for <a href="<?php echo $mlurl_link ?>">
			<?php echo $mlurl_link ?>
		</a>
		<span id="goes-to">
			goes to: <a href="<?php echo $mlurl_target ?>">
				<?php echo substr($mlurl_target, 0, 80) ?><?php if(strlen($mlurl_target) > 80) echo '...' ?>
			</a>
		</span>
	</h2>
<?php else: ?>
	<h2>states for all mlurls</h2>
<?php endif ?>
<?php if(isset($hits)): ?>
	<h3>total hits: <?php echo count($hits) ?></h3>
	<h3>top referer: <?php $url = array_keys($referers); echo "$url[0] (" . current($referers) . ")" ?></h3>
	<h4>referers:</h4>
	<ul>
		<?php foreach($referers as $k => $v): ?>
			<li>
				<span><?php echo $v ?></span><span><a href="<?php echo $k ?>"><?php echo $k ?></a></span>
			</li>
		<?php endforeach ?>
	</ul>
<?php else: ?>
	<h3>No one has hit this mlurl yet.</h3>
<?php endif ?>
<?php $this->view('footer') ?>