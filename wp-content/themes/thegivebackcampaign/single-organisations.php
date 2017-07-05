<?php
/**
 Template Name: 2 Column Page
 *	
 */

get_header(); ?>
<div id="primary" class="content-area column content">
		<main id="main" class="site-main" role="main">
			<section class="main single twocolumn">
			<?php
			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content', 'page' );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>
			</section>
			<?php get_sidebar();?>
		</main><!-- #main -->
	</div><!-- #primary -->
</section>
<?php

get_footer();
