<?php
// $Id: page.tpl.php,v 1.1.2.1 2009/02/24 15:34:45 dvessel Exp $
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language ?>" lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">

<head>
  <title><?php print $head_title; ?></title>
  <?php print $head; ?>
  <?php print $styles; ?>
  <?php print $scripts; ?>
</head>
<body>

<div id="wrapper">

	<?php if ($aboveheader): ?>
		<div id="above-header" class="container"><?php print $aboveheader ?></div>
	<?php endif; ?>

	<div id="header" class="container">

		<div id="logo">

		      <?php if ($site_name): ?>
		        <h1 id="site-name"><a href="<?php print $base_path; ?>"><?php print $site_name; ?></a></h1>
		      <?php endif; ?>
		      <?php if ($site_slogan): ?>
		        <p id="site-slogan"><?php print $site_slogan; ?></p>
		      <?php endif; ?>

		</div>

		<div id="banner"><img src="/<?php print $directory; ?>/images/img01.jpg" width="667" height="118" alt="" /></div>

	</div>

	<div id="menu" class="container">

		<?php if (isset($primary_links)) : ?>
        	<?php print theme('links', $primary_links, array('class' => 'links primary-links')) ?>
        <?php endif; ?>

	</div>

	<div id="top-bar" class="container">

		<div class="bar">

		    <?php if ($mission || ($show_messages && $messages)): ?>

				<div class="text">
				  <?php print $breadcrumb; ?>
		          <?php if ($mission): print '<div id="mission">'. $mission .'</div>'; endif; ?>
		          <?php if ($show_messages && $messages): print $messages; endif; ?>
		          <?php print $help; ?>

				</div>



			
			<?php endif; ?>

		    <?php if ($search_box): ?>
	
		        <?php print $search_box; ?>
		
		    <?php endif; ?>



		</div>

	</div>

	<div id="page" class="container">

		<div id="content">

			<?php if ($abovecontent): ?>
				<div id="above-content"><?php print $abovecontent ?></div>
			<?php endif; ?>

            <?php print $content ?>

			<?php if ($belowcontent): ?>
				<div id="below-content"><?php print $belowcontent ?></div>
			<?php endif; ?>

		</div>

		<div id="sidebar">

			<ul>

				<?php if ($sidebar): ?>
					<?php print $sidebar ?>
				<?php endif; ?>

			</ul>

		</div>

		<div class="clearfix">&nbsp;</div>

		<div id="footer-bar" class="two-cols">

			<div class="col1">

			<?php if ($leftupperfooter): ?>
				<?php print $leftupperfooter ?>
			<?php endif; ?>

			</div>

			<div class="col2">

			<?php if ($rightupperfooter): ?>
				<?php print $rightupperfooter ?>
			<?php endif; ?>

			</div>

			<div class="clearfix">&nbsp;</div>

		</div>

	</div>

</div>

<div id="footer" class="container">

	<?php if ($footer): ?>
		<?php print $footer ?>
	<?php endif; ?>

	<p>(c) 2009 Sitename.com. Design by <a href="http://www.nodethirtythree.com/">nodethirtythree</a> + <a href="http://www.freecsstemplates.org/">Free CSS Templates</a></p>

</div>
<?php print $closure ?>
</body>

</html>

