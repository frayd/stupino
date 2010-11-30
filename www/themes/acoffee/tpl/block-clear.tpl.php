<?php
// $Id: block-clear.tpl.php,v 1.1 2010/08/31 13:54:07 stocker Exp $

/**
 * @file block-contentleftbox.tpl.php
 * Theme implementation to display a block.
 */
?>
<div id="block-<?php print $block->module .'-'. $block->delta; ?>" class="block block-<?php print $block->module ?> block-clear">
<?php if ($block->subject): ?>
  <h2><?php print $block->subject ?></h2>
<?php endif;?>

  <div class="content">
    <?php print $block->content ?>
  </div>
</div>
