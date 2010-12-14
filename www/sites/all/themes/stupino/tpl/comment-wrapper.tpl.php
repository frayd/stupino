<?php
// $Id: comment-wrapper.tpl.php,v 1.1 2010/08/31 13:54:07 stocker Exp $

/**
 * @file comment-wrapper.tpl.php
 * Default theme implementation to wrap comments.
 */
?>
<div id="comments">
  <h3 class="comments-header"><?php print t('Comments'); ?></h3>
  <?php print $content; ?>
</div>
