<div class="comment<?php print ($comment->new) ? ' comment-new' : ''; print ' '. $status ?> clear-block">
	<?php print $picture ?>
	<div class="submitted">
		<?php print $submitted ?>
	</div>
	<div class="content">
		<?php print $content ?>
		<?php if ($signature): ?>
		<div class="user-signature clear-block">
			<?php print $signature ?>
		</div>
		<?php endif; ?>
	</div>
	<?php print $links ?>
</div>