<?php
/*
*  Template Name: Classes
*/
get_header();
?>

	<section class="container">
        <?php if (have_posts()) : while (have_posts()) : the_post();
        if ( has_post_thumbnail() ) {
            $iAttachId = get_post_thumbnail_id( $post->ID );
            $aPageHeaderImage = wp_get_attachment_image_src( $iAttachId, 'full' );
            $sPageHeaderImage = $aPageHeaderImage[0];
        } else {
            $sPageHeaderImage = get_template_directory_uri().'/images/placeholders/pageheader-classes.jpg';
        }
        ?>
		<div class="pageHeader" style="background-image: url(<?php echo esc_url( $sPageHeaderImage ); ?>);">
			<h1><?php the_title() ?></h1>
		</div>
		<div class="contentWrap">

            <?php the_content() ?>     

		</div>
        <?php endwhile; endif; ?>
	</section>

<?php get_footer(); ?>