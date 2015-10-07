<?php get_header();
$sDateAndTimeFormat = get_option( 'date_format' ).' '.get_option( 'time_format' ); ?>

	<section class="container">

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<h1 class="blockTitle"><?php printf( __( 'Search results for: "%s"', 'asana' ), get_search_query() ); ?></h1>
		<div class="blogPostWrap">

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
		</div>
        <?php
        endwhile;
        else :
        ?>

            <?php get_template_part( 'no-results', 'archive' ); ?>

        <?php
        endif;
        ?>

		<div class="pagination clear">
            <?php uni_pagination(); ?>
		</div>
		<?php /*<a href="#" class="loadMoreItems">load more</a>*/ ?>
	</section>

<?php get_footer(); ?>