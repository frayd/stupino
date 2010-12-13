<?php
// $Id: advanced_forum-forum-legend.tpl.php,v 1.1.2.5 2009/06/24 14:43:52 michellec Exp $

/**
 * @file
 * Theme implementation to show forum legend.
 *
 */
?>
<div class="forum-folder-legend forum-smalltext clear-block">
  <dl>
    <dt><?php print $folder_new_posts; ?></dt>
    <dd><?php print t('Forum Contains New Posts'); ?></dd>
    <dt><?php print $folder_default; ?></dt>
    <dd><?php print t('Forum Contains No New Posts'); ?></dd>
    <dt><?php print $folder_locked ?></dt>
    <dd><?php print t('Forum is Locked'); ?></dd>
  </dl>
</div>
