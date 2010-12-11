<?php
// $Id: forum-icon.tpl.php,v 1.1 2010/08/31 13:54:07 stocker Exp $

/**
 * @file forum-icon.tpl.php
 * Display an appropriate icon for a forum post.
 */
?>
<?php if ($new_posts): ?>
  <a name="new">
<?php endif; ?>

<?php
  if ($icon == 'hot' || $icon == 'hot-new'){
    print theme('image', drupal_get_path('theme', 'acoffee') . "/img/forum-$icon.gif");
  }
  else {
    print theme('image', drupal_get_path('theme', 'acoffee') . "/img/forum-$icon.png");
  }
?>

<?php if ($new_posts): ?>
  </a>
<?php endif; ?>
