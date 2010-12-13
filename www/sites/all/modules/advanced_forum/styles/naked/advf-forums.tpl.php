<?php
// $Id: advf-forums.tpl.php,v 1.1.2.5 2009/01/27 23:31:11 michellec Exp $

/**
 * @file
 * Default theme implementation to display a forum which may contain forum
 * containers as well as forum topics.
 *
 * Variables available:
 * - $links: An array of links that allow a user to post new forum topics.
 *   It may also contain a string telling a user they must log in in order
 *   to post. Empty if there are no topics on the page. (ie: forum overview)
 * - $links_orig: Same as $links but not emptied on forum overview page.
 * - $forums: The forums to display (as processed by forum-list.tpl.php)
 * - $topics: The topics to display (as processed by forum-topic-list.tpl.php)
 * - $forums_defined: A flag to indicate that the forums are configured.
 * - $forum_description: The forum's taxonomy term description, if any.
 *
 * @see template_preprocess_forums()
 * @see advanced_forum_preprocess_forums()
 */
?>

<?php if ($forums_defined): ?>
<div id="forum">

  <?php if ($forum_description): ?>
  <div class="forum-description">
    <?php print $forum_description; ?>
  </div>
  <?php endif; ?>

  <div class="forum-top-links"><?php print theme('links', $links, array('class' => 'links forum-links')); ?></div>
  <?php print $forums; ?>
  <?php print $topics; ?>
</div>
<?php endif; ?>
