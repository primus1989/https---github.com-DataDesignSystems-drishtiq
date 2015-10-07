<?php get_header(); ?>

	<section class="container">
		<div class="page404Wrap">
			<img src="<?php echo get_template_directory_uri(); ?>/images/404.png" alt="error 404">
			<p><?php _e('The requested page has not been found', 'asana') ?></p>
			<a href="<?php if ( function_exists('icl_get_languages') ) { echo esc_url( icl_get_home_url() ); } else { echo esc_url( home_url() ); } ?>" class="homePage"><?php _e('Homepage', 'asana') ?></a>
		</div>
	</section>

<?php get_footer(); ?>