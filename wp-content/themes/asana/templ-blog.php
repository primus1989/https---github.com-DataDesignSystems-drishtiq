<?php
/*
*  Template Name: Blog
*/
get_header();
$sDateAndTimeFormat = get_option( 'date_format' ).' '.get_option( 'time_format' ); ?>

	<section class="container">
		<div class="blogPostWrap">
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $aBlogArgs = array(
            'post_type' => 'post',
            'paged' => $paged
        );

        $oBlogQuery = new wp_query( $aBlogArgs );
        if ( $oBlogQuery->have_posts() ) :
        while ( $oBlogQuery->have_posts() ) : $oBlogQuery->the_post();
        ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class('postItem'); ?>>
				<a href="<?php the_permalink() ?>" class="postItemImg">
                        <?php if ( has_post_thumbnail() ) { ?>
                            <?php the_post_thumbnail( 'unithumb-blog', array( 'alt' => the_title_attribute('echo=0') ) ); ?>
                        <?php } else { ?>
						    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholders/unithumb-blog.jpg" alt="<?php the_title_attribute() ?>" width="408" height="272">
                        <?php } ?>
				</a>
                <time class="postItemTime" datetime="<?php the_time('Y-m-d'); ?>"><?php the_time( $sDateAndTimeFormat ); ?></time>
				<h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
				<?php uni_excerpt(12, '', true) ?>
			</div>
        <?php
        endwhile;
        else :
        ?>

            <?php get_template_part( 'no-results', 'archive' ); ?>

        <?php
        endif;
        ?>
		</div>

		<div class="pagination clear"<?php if ( ot_get_option('uni_ajax_scroll_enable_posts') == 'on' ) { echo ' style="display:none;"'; } ?>>
            <?php uni_pagination( $oBlogQuery->max_num_pages ); ?>
		</div>

	</section>

<?php get_footer(); ?>