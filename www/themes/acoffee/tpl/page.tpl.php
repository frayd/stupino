<?php
// $Id: page.tpl.php,v 1.2 2010/09/12 13:29:28 stocker Exp $

/**
 * @file page.tpl.php
 *
 * Theme implementation to display a single Drupal page.
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language ?>" lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">
<head>
<?php print $head; ?>
<title><?php print $head_title; ?></title>
<?php print $styles; ?>
<?php print $scripts; ?>
</head>
<body class="<?php print $body_classes; ?>">
<div id="page-wraper">
  <div id="page">

    <div id="container" class="clear-block">
      <div id="main" class="column"><div id="main-squeeze">
        <?php if (!empty($breadcrumb)): ?><div id="breadcrumb"><?php print $breadcrumb; ?></div><?php endif; ?>
        <?php if (!empty($mission)): ?><div id="mission"><?php print $mission; ?></div><?php endif; ?>

        <div id="content">
          <?php if (!empty($topcontent)): ?><div id="topcontent"><?php print $topcontent; ?></div><?php endif; ?>
          <?php if (!empty($title)): ?><h1 class="title" id="page-title"><?php print $title; ?></h1><?php endif; ?>
          <?php if (!empty($tabs)): ?><div class="tabs"><?php print $tabs; ?></div><?php endif; ?>
          <?php if (!empty($messages)): print $messages; endif; ?>
          <?php if (!empty($help)): print $help; endif; ?>
          <div id="content-content" class="clear-block">
            <?php if (!empty($precontent)): ?><div id="precontent"><?php print $precontent; ?></div><?php endif; ?>
            <?php print $content; ?>
            <?php if (!empty($contentleftbox) || !empty($contentrightbox)): ?>
              <div id="content-boxes">
                <div class="area-l"><?php if (!empty($contentleftbox)): print $contentleftbox; endif; ?></div>
                <div class="area-r"><?php if (!empty($contentrightbox)): print $contentrightbox; endif; ?></div>
              </div>
            <?php endif; ?>
          </div> <!-- /content-content -->
          <?php print $feed_icons; ?>
        </div> <!-- /content -->

      </div>
      <div id="footer-bg"></div>
      </div> <!-- /main-squeeze /main -->

      <div id="sidebar-right" class="column sidebar">

        <div id="header">
          <div id="logo-title">

            <div id="logo">
              <div id="name-and-slogan">
                <?php if (!empty($site_name)): ?>
                  <p id="site-name">
                    <a href="<?php print $front_page ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
                  </p>
                <?php endif; ?>

                <?php if (!empty($site_slogan)): ?>
                  <div id="site-slogan"><?php print $site_slogan; ?></div>
                <?php endif; ?>
              </div> <!-- /name-and-slogan -->
            </div>
          </div> <!-- /logo-title -->

        </div> <!-- /header -->

        <div id="navigation" class="menu <?php if (!empty($primary_links)) { print "withprimary"; } if (!empty($secondary_links)) { print " withsecondary"; } ?> ">
          <?php if (!empty($topright)): ?><div id="topright"><?php print $topright; ?></div><?php endif; ?>
          <?php if (!empty($primary_links)): ?>
            <div id="primary" class="clear-block">
              <?php print theme('links', $primary_links, array('class' => 'links primary-links')); ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($secondary_links)): ?>
            <div id="secondary" class="clear-block">
              <?php print theme('links', $secondary_links, array('class' => 'links secondary-links')); ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($rss_twit)): ?>
            <div id="rss-twit">
              <?php print $rss_twit; ?>
            </div>
          <?php endif; ?>

        </div> <!-- /navigation -->

      <?php if (!empty($right)) print $right; ?>
      </div> <!-- /sidebar-right -->

    </div> <!-- /container -->
  </div> <!-- /page -->
  <div id="basement">

    <div id="footer-wrapper">
      <div id="footer">
        <?php if (!empty($footer)): print $footer; endif; ?>
        <?php if (!empty($footerboxleft) || !empty($footerboxcenter) || !empty($footerboxr)): ?>
          <div id="footer-boxes">
            <div class="area"><?php if (!empty($footerboxleft)): print $footerboxleft; endif; ?></div>
            <div class="area"><?php if (!empty($footerboxcenter)): print $footerboxcenter; endif; ?></div>
            <div class="area"><?php if (!empty($footerboxright)): print $footerboxright; endif; ?></div>
          </div>
        <?php endif; ?>
        <?php print $footer_message; ?>
      </div> <!-- /footer -->
    </div> <!-- /footer-wrapper -->

    <?php print $closure; ?>

  </div>
</div>
</body>
</html>