<?php
?>	

<div id="node-<?php print $node->nid; ?>"  class="post node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">

	<h2 class="title"><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>

		<p class="date">Posted on <?php print $date ?>
			<?php //if ($submitted): ?>
			<!-- <span class="submitted"> -->
			<?php //print $submitted; ?>
			<!-- </span> -->
			<?php //endif; ?>
		</p>

	<div class="entry">

  		<?php print $content ?>
	
	</div>

	<div class="meta clearfix">

	    <?php if ($taxonomy): ?> 
			<?php $fnd1 = array("<ul ", "</ul>", "</li>\n<li ", "<li ", "</li>", "class=\"terms\">"); ?>
			<?php $rep1   = array("<p ", "</p>", "</span>, <span ", "<span ", "</span>", "class=\"terms\"> Tags: "); ?>
			<?php $terms = str_replace($fnd1, $rep1, $terms); ?>
	    	<?php print $terms ?>
	    <?php endif;?>

		
		<p class="links">
			
		<?php 
			print "<a href=\"/comment/reply/" . $node->nid . "#comment-form\" class=\"comments\">Comment";
			if ($comment_count > 0) {
				print " (" . $comment_count . ")";
			}
			print "</a>";
		?>
		

		<a href="<?php print $node_url ?>" title="<?php print $title ?>" class="more">More</a>
		</p>

	</div>

</div>
