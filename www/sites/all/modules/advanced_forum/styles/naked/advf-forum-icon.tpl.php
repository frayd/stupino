<?php
// $Id: advf-forum-icon.tpl.php,v 1.1.2.6 2009/01/27 23:31:11 michellec Exp $

/**
 * @file
 * Display an appropriate icon for a forum post.
 *
 * Available variables:
 * - $new_posts: Indicates whether or not the topic contains new posts.
 * - $icon: The icon to display. May be one of 'hot', 'hot-new', 'new',
 *   'default', 'closed', or 'sticky'.
 * - $iconpath: Path from Drupal root to the directory with the forum icons.
 *
 * @see template_preprocess_forum_icon()
 * @see advanced_forum_preprocess_forum_icon()
 */
?>

<?php if ($new_posts): ?>
  <a name="new">
<?php endif; ?>

<?php print theme('image', "$iconpath/topic-$icon.png"); ?>

<?php if ($new_posts): ?>
  </a>
<?php endif; ?>
