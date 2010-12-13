<?php
// $Id: advf-forum-statistics.tpl.php,v 1.1.2.3 2009/02/04 17:22:18 michellec Exp $

/**
 * @file
 *
 * Theme implementation: Template for each forum forum statistics section.
 *
 * Available variables:
 * - $current_total: Total number of users currently online.
 * - $current_users: Number of logged in users.
 * - $current_guests: Number of anonymous users.
 * - $online_users: List of logged in users.
 * - $topics: Total number of nodes (threads / topics).
 * - $posts: Total number of nodes + comments.
 * - $users: Total number of registered active users.
 * - $latest_user: Linked user name of latest active user.
 */
?>

<div id="forum-statistics">
  <div id="forum-statistics-header"><?php print t("What's Going On?"); ?></div>

  <div id="forum-statistics-active-header" class="forum-statistics-sub-header">
    <?php print t('Currently active users: !current_total (!current_users users and !current_guests guests)', array('!current_total' => $current_total, '!current_users' => $current_users, '!current_guests' => $current_guests)); ?>
  </div>

  <div id="forum-statistics-active-body" class="forum-statistics-sub-body">
    <?php print $online_users; ?>
  </div>

  <div id="forum-statistics-statistics-header" class="forum-statistics-sub-header">
    <?php print t('Statistics'); ?>
  </div>

  <div id="forum-statistics-statistics-body" class="forum-statistics-sub-body">
    <?php print t('Topics: !topics, Posts: !posts, Users: !users', array('!topics' => $topics, '!posts' => $posts, '!users' => $users)); ?>
    <br /><?php print t('Welcome to our latest member, !user', array('!user' => $latest_user)); ?>
  </div>
</div>
