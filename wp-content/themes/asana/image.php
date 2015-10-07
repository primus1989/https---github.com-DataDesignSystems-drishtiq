<?php get_header();
$sDateAndTimeFormat = get_option( 'date_format' ).' '.get_option( 'time_format' ); ?>

	<section class="container">

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="wrapper">

            <time class="postItemTime" datetime="<?php the_time('Y-m-d'); ?>"><?php the_time( $sDateAndTimeFormat ); ?></time>

			<div class="singlePostWrap clear">

				<h1 class="singleTitle"><?php the_title() ?></h1>

                <?php echo wp_get_attachment_image( get_the_ID(), 'unithumb-attachment' ); ?>

			</div>

		</div>

        <?php endwhile;
        else : ?>

		<div class="wrapper">
		    <h2><?php _e('Nothing Found', 'asana') ?></h2>
			<a href="<?php if ( function_exists('icl_get_languages') ) { echo esc_url( icl_get_home_url() ); } else { echo esc_url( home_url() ); } ?>" class=""><?php _e('homepage', 'asana') ?></a>
        </div>

        <?php endif; ?>
	</section>

<?php get_footer(); ?>