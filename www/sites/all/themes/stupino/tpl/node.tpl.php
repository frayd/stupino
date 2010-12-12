<?php
// $Id: node.tpl.php,v 1.1 2010/08/31 13:54:07 stocker Exp $

/**
 * @file node.tpl.php
 *
 * Theme implementation to display a node.
 */
?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?> clear-block">
<?php if ($node->type != 'page'): ?>
<div class="node-create">
  <?php if ($node_create_date) { print $node_create_date; } ?>
</div>
<?php endif; ?>
<div class="node-main">
  <?php print $picture ?>

  <?php if (!$page): ?>
    <h2><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
  <?php endif; ?>

  <div class="meta">
  <?php if ($submitted): ?>
    <span class="submitted"><?php print $submitted ?></span>
  <?php endif; ?>
  </div>

  <div class="content">
    <?php print $content ?>
  </div>
  <?php print $links; ?>
  <?php if ($terms): ?>
    <div class="terms"><?php print $terms ?></div>
  <?php endif;?>
</div>
</div>