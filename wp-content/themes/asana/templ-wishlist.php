<?php
/*
*  Template Name: Wishlist
*/
get_header();
?>

	<section class="container">
        <?php if (have_posts()) : while (have_posts()) : the_post();
        $aPostCustom = get_post_custom( $post->ID );
        if ( !empty($aPostCustom['uni_wishlist_page_header_image'][0]) ) {
            $aPageHeaderAttachIds = explode(',', $aPostCustom['uni_wishlist_page_header_image'][0]);
            $aPageHeaderImage = wp_get_attachment_image_src( $aPageHeaderAttachIds[0], 'full' );
            $sPageHeaderImage = $aPageHeaderImage[0];
        } else {
            $sPageHeaderImage = get_template_directory_uri().'/images/placeholders/pageheader-classes.jpg';
        }
        ?>
		<div class="pageHeader" style="background-image: url(<?php echo esc_url( $sPageHeaderImage ); ?>);">
			<h1><?php the_title() ?></h1>
		</div>
		<div class="contentWrap">
            <div class="pagePanel clear">
                <div class="pageTitle"><?php _e('Wishlist', 'asana') ?></div>
            </div>

        <?php the_content() ?>

		</div>
        <?php endwhile; endif; ?>
	</section>

<?php get_footer(); ?>