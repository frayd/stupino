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
		<div id="main" class="column">
			<div id="main-squeeze">
				<?php if (!empty($primary_links)): ?>
				<div id="primary" class="clear-block">
					<?php print theme('links', $primary_links, array('class' => 'links primary-links')); ?>
				</div>
				<?php endif; ?>
				
				<?php if (!empty($breadcrumb)): ?><div id="breadcrumb"><?php print $breadcrumb; ?></div><?php endif; ?>
				<?php if (!empty($mission)): ?><div id="mission"><?php print $mission; ?></div><?php endif; ?>
				<div id="content">
					<?php if (!empty($topcontent)): ?><div id="topcontent"><?php print $topcontent; ?></div><?php endif; ?>
					<?php if (!empty($title)): ?><h1 class="title" id="page-title"><?php print $title; ?></h1><?php endif; ?>
					<?php if (!empty($tabs)): ?><div class="tabs"><?php print $tabs; ?></div><?php endif; ?>
					<?php if (!empty($messages)): print $messages; endif; ?>
					<?php if (!empty($help)): print $help; endif; ?>
					<div id="content-content" class="clear-block">
						<?php print $content; ?>
					</div>
				</div>
			</div>
			<div id="footer-bg"></div>
		</div>
		<div id="sidebar-right" class="column sidebar">
			<div id="header">
				<div id="logo-title">
					<div id="logo">
						<div id="name-and-slogan">
							<?php if (!empty($site_name)): ?>
							<p id="site-name">
								<a href="<?php print $front_page ?>" title="<?php print t('Home'); ?>" rel="home"><?php print $site_name ?></a>
							</p>
							<?php endif; ?>
							<?php if (!empty($site_slogan)): ?><div id="site-slogan"><?php print $site_slogan; ?></div><?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<div id="navigation" class="menu <?php if (!empty($primary_links)) { print "withprimary"; } if (!empty($secondary_links)) { print " withsecondary"; } ?> ">
				<?php if (!empty($topright)): ?><div id="topright"><?php print $topright; ?></div><?php endif; ?>
		  
				<?php if (!empty($rss_twit)): ?>
				<div id="rss-twit">
					<?php print $rss_twit; ?>
				</div>
				<?php endif; ?>

				<?php if (!empty($secondary_links)): ?>
				<div id="secondary" class="clear-block">
					<?php print theme('links', $secondary_links, array('class' => 'links secondary-links')); ?>
				</div>
				<?php endif; ?>
			</div>
			
			<?php if (!empty($right)): ?>
			<div id="main-block">
				<div id="main-block_inside">
					<?php print $right; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<div id="basement">
	<div id="footer-wrapper">
		<div id="footer">
			<?php print $footer_message; ?>
			<p>designed by "Трололо studious" 2010</p>
			<?php if (!empty($footer)): print $footer; endif; ?>
		</div>
	</div>
    <?php print $closure; ?>
</div>
</div>
</body>
</html>