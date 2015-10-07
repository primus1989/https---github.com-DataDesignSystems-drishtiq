<?php get_header();
$sDateAndTimeFormat = get_option( 'date_format' ).' '.get_option( 'time_format' ); ?>

	<section class="container">

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="wrapper">

            <time class="postItemTime" datetime="<?php the_time('Y-m-d'); ?>"><?php the_time( $sDateAndTimeFormat ); ?></time>

			<div id="post-<?php the_ID(); ?>" <?php post_class('singlePostWrap clear') ?>>

				<h1 class="singleTitle"><?php the_title() ?></h1>

                <?php the_content() ?>

			</div>

            
            <div class="singleLinkPages">
                <?php wp_link_pages(); ?>   
                <br> 
            </div>
			<div class="singlePostTags clear">
			    <span><?php _e('Categories', 'asana') ?>:</span>
        <?php
        $aTags = wp_get_post_terms( $post->ID, 'category' );
        if ( $aTags && !is_wp_error( $aTags ) ) :
        $s = count($aTags);
        $i = 1;
	    foreach ( $aTags as $oTerm ) {
	        echo '<a href="'.get_term_link( $oTerm->slug, 'category' ).'">'.esc_html( $oTerm->name ).'</a>';
            if ($i < $s) echo ', ';
            $i++;
	    }
        endif;
        ?>
        <br><br>
        <?php the_tags( '<span>'.__('Tags', 'asana').':</span>', ', ', '' ); ?>
        <?php /*
        $aTags = wp_get_post_terms( $post->ID, 'post_tag' );
        if ( $aTags && !is_wp_error( $aTags ) ) :
        $s = count($aTags);
        $i = 1;
        echo '<span>'.__('Tags', 'asana').':</span>';
	    foreach ( $aTags as $oTerm ) {
	        echo '<a href="'.get_term_link( $oTerm->slug, 'post_tag' ).'">'.esc_html( $oTerm->name ).'</a>';
            if ($i < $s) echo ', ';
            $i++;
	    }
        endif;*/
        ?>
			</div>

            <?php include(locate_template('includes/social-links.php')); ?>

            <?php comments_template(); ?>

		</div>

		<?php uni_relative_posts_by_tags(); ?>

        <?php endwhile; endif; ?>
	</section>

<?php get_footer(); ?>