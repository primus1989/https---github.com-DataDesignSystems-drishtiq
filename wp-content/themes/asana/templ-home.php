<?php
/*
*  Template Name: Home page
*/
get_header() ?>

	<section class="container">

    <?php if ( ot_get_option('uni_home_rev_slider_enable') == 'on' ) { ?>
        <?php echo do_shortcode('[rev_slider alias="home"]'); ?>
    <?php } ?>

    <?php if ( ot_get_option('uni_home_slider_enable') == 'on' ) { ?>
	<?php
	$aHomeSlidesArgs = array(
	    'post_type' => 'uni_home_slides',
	    'orderby' => 'menu_order',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'order' => 'asc'
	);
	$oHomeSlides = new WP_Query( $aHomeSlidesArgs );
	if ( $oHomeSlides->have_posts() ) :
    $i = 0;
    ?>
		<div class="homeBxSliderWrap">
			<div class="homeBxSlider">
    <?php
	while ( $oHomeSlides->have_posts() ) : $oHomeSlides->the_post();
    $aPostCustom = get_post_custom( $post->ID );
	$sThumbId = get_post_thumbnail_id( $post->ID );
	$aImage = wp_get_attachment_image_src( $sThumbId, 'full' );
	?>
				<div class="slide active" data-slide="<?php echo $i; ?>" style="background-image: url(<?php echo ( ( isset($aImage[0]) && !empty($aImage[0]) ) ? esc_url( $aImage[0] ) : get_template_directory_uri().'/images/placeholders/pageheader-singleevent.jpg' ); ?>);">
					<div class="slideDesc">
						<h2><?php the_title() ?></h2>
							<style type="text/css">
								.learnMore_<?php echo get_the_ID() ?> {color:<?php echo ( ( isset($aPostCustom['uni_button_a_colour'][0]) ) ? esc_attr( $aPostCustom['uni_button_a_colour'][0] ) : '#ffffff' ); ?>;background-color:<?php echo ( ( isset($aPostCustom['uni_button_a_bg'][0]) ) ? esc_attr( $aPostCustom['uni_button_a_bg'][0] ) : '#168cb9' ); ?>;}
								.learnMore_<?php echo get_the_ID() ?>:hover {color:<?php echo ( ( isset($aPostCustom['uni_button_a_colour'][0]) ) ? esc_attr( $aPostCustom['uni_button_a_colour'][0] ) : '#ffffff' ); ?>;background-color:<?php echo ( ( isset($aPostCustom['uni_button_a_bg_hover'][0]) ) ? esc_attr( $aPostCustom['uni_button_a_bg_hover'][0] ) : '#168cb2' ); ?>;}
							</style>
                        <?php if ( isset($aPostCustom['uni_slide_uri'][0]) && !empty($aPostCustom['uni_slide_uri'][0]) ) { ?>
						<a href="<?php echo ( ( isset($aPostCustom['uni_slide_uri'][0]) ) ? esc_url( $aPostCustom['uni_slide_uri'][0] ) : '' ); ?>" class="learnMore learnMore_<?php echo get_the_ID() ?>"><?php echo ( ( isset($aPostCustom['uni_slide_label'][0]) ) ? esc_attr( $aPostCustom['uni_slide_label'][0] ) : __('learn more', 'asana') ); ?></a>
                        <?php } ?>
					</div>
				</div>
	<?php
    $i++;
	endwhile;
    ?>
			</div>
		</div>
    <?php
    endif;
	wp_reset_postdata();
    ?>
    <?php } ?>

    <?php if ( ot_get_option('uni_home_about_enable') == 'on' ) { ?>
        <div class="homeGrid homeAboutSection">

            <div class="mainItem clear">
                <div class="mainItemImg">
                <?php
                $sHomeAboutTitle = ot_get_option('uni_home_about_title');
                if ( ot_get_option('uni_home_about_image') ) {
                $aHomeAboutBlockImage = wp_get_attachment_image_src( ot_get_option('uni_home_about_image'), 'unithumb-homepostbig' );
                ?>
                    <img src="<?php echo $aHomeAboutBlockImage[0] ?>" alt="<?php if ( !empty($sHomeAboutTitle) ) echo esc_attr($sHomeAboutTitle); ?>" width="684" height="684">
                <?php
                } else {
                ?>
                    <img src="<?php echo get_template_directory_uri() ?>/images/placeholders/unithumb-homepostbig.jpg" alt="<?php if ( !empty($sHomeAboutTitle) ) echo esc_attr($sHomeAboutTitle); ?>" width="684" height="684">
                <?php } ?>
                </div>
                <div class="mainItemDesc">
                    <h3><?php if ( !empty($sHomeAboutTitle) ) echo esc_html($sHomeAboutTitle); ?></h3>
                    <?php echo ot_get_option('uni_home_about_text') ?>
                    <?php if ( ot_get_option('uni_home_about_more_link_url') ) { ?>
                    <a href="<?php echo ot_get_option('uni_home_about_more_link_url') ?>" class="viewMore"><?php echo ( ot_get_option('uni_home_about_more_link_text') ) ? ot_get_option('uni_home_about_more_link_text') : __('view more', 'asana') ?></a>
                    <?php } ?>
                </div>
            </div>

        </div>
    <?php } ?>

    <?php if ( ot_get_option('uni_home_grid_custom_enable') == 'on' ) { ?>
		<div class="homeGrid">

            <?php if ( ot_get_option('uni_home_grid_custom_uri_one') ) { ?>
            <div class="mainItem clear">
                <div class="mainItemImg">
                <?php
                $sHomeItemOneTitle = ot_get_option('uni_home_grid_custom_title_one');
                if ( ot_get_option('uni_home_grid_custom_image_one') ) {
                $aHomeItemOneImage = wp_get_attachment_image_src( ot_get_option('uni_home_grid_custom_image_one'), 'unithumb-homepostbig' );
                ?>
                    <img src="<?php echo esc_url( $aHomeItemOneImage[0] ) ?>" alt="<?php if ( !empty($sHomeItemOneTitle) ) echo esc_attr($sHomeItemOneTitle); ?>" width="<?php echo esc_attr( $aHomeItemOneImage[1] ) ?>" height="<?php echo esc_attr( $aHomeItemOneImage[2] ) ?>">
                <?php
                } else {
                ?>
                    <img src="<?php echo get_template_directory_uri() ?>/images/placeholders/unithumb-homepostbig.jpg" alt="<?php if ( !empty($sHomeItemOneTitle) ) echo esc_attr($sHomeItemOneTitle); ?>" width="684" height="684">
                <?php } ?>
                </div>
                <div class="mainItemDesc">
                    <h3><?php if ( !empty($sHomeItemOneTitle) ) echo esc_html($sHomeItemOneTitle); ?></h3>
                    <p><?php echo ot_get_option('uni_home_grid_custom_text_one') ?></p>
                    <a href="<?php echo ot_get_option('uni_home_grid_custom_uri_one') ?>" class="viewMore"><?php _e('view more', 'asana') ?></a>
                </div>
            </div>
            <?php } ?>

			<div class="gridItemWrap clear">

                <?php if ( ot_get_option('uni_home_grid_custom_uri_two') ) { ?>
				<a href="<?php echo ot_get_option('uni_home_grid_custom_uri_two') ?>" class="gridItem clear">
					<div class="gridItemImg">
                    <?php
                    $sHomeItemTwoTitle = ot_get_option('uni_home_grid_custom_title_two');
                    if ( ot_get_option('uni_home_grid_custom_image_two') ) {
                    $aHomeItemTwoImage = wp_get_attachment_image_src( ot_get_option('uni_home_grid_custom_image_two'), 'unithumb-homepostsmall' );
                    ?>
                        <img src="<?php echo $aHomeItemTwoImage[0] ?>" alt="<?php if ( !empty($sHomeItemTwoTitle) ) echo esc_attr($sHomeItemTwoTitle); ?>" width="342" height="342">
                    <?php
                    } else {
                    ?>
                        <img src="<?php echo get_template_directory_uri() ?>/images/placeholders/unithumb-homepostsmall.jpg" alt="<?php if ( !empty($sHomeItemTwoTitle) ) echo esc_attr($sHomeItemTwoTitle); ?>" width="342" height="342">
                    <?php } ?>
					</div>
					<div class="gridItemDesc">
						<h3><?php if ( !empty($sHomeItemTwoTitle) ) echo esc_html($sHomeItemTwoTitle); ?></h3>
						<p><?php echo ot_get_option('uni_home_grid_custom_text_two') ?></p>
						<span class="viewMore"><?php _e('view more', 'asana') ?>
							<i>
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="9px" height="15px" viewBox="0 0 9 15" enable-background="new 0 0 9 15" xml:space="preserve">
									<path fill="#96e9d5" d="M10-184.5l-0.826 0.757L1.826-177L1-177.758l7.349-6.742L1-191.243L1.826-192l7.349 6.743L10-184.5z M9.174-183.743L9.174-183.743L10-184.5L9.174-183.743z M9.175-185.257L10-184.5v0L9.175-185.257z"/>
									<path fill="#96e9d5" d="M9 7.5L8.174 8.257L0.826 15L0 14.242L7.349 7.5L0 0.757L0.826 0l7.349 6.743L9 7.5z M8.174 8.3 L8.174 8.257L9 7.5L8.174 8.257z M8.175 6.743L9 7.5v0L8.175 6.743z"/>
								</svg>
							</i>
						</span>
					</div>
				</a>
                <?php } ?>

                <?php if ( ot_get_option('uni_home_grid_custom_uri_three') ) { ?>
				<a href="<?php echo ot_get_option('uni_home_grid_custom_uri_three') ?>" class="gridItem gridItemWhite clear">
					<div class="gridItemImg">
                    <?php
                    $sHomeItemThreeTitle = ot_get_option('uni_home_grid_custom_title_three');
                    if ( ot_get_option('uni_home_grid_custom_image_three') ) {
                    $aHomeItemThreeImage = wp_get_attachment_image_src( ot_get_option('uni_home_grid_custom_image_three'), 'unithumb-homepostsmall' );
                    ?>
                        <img src="<?php echo $aHomeItemThreeImage[0] ?>" alt="<?php if ( !empty($sHomeItemThreeTitle) ) echo esc_attr($sHomeItemThreeTitle); ?>" width="342" height="342">
                    <?php
                    } else {
                    ?>
                        <img src="<?php echo get_template_directory_uri() ?>/images/placeholders/unithumb-homepostsmall.jpg" alt="<?php if ( !empty($sHomeItemThreeTitle) ) echo esc_attr($sHomeItemThreeTitle); ?>" width="342" height="342">
                    <?php } ?>
					</div>
					<div class="gridItemDesc">
						<h3><?php if ( !empty($sHomeItemThreeTitle) ) echo esc_html($sHomeItemThreeTitle); ?></h3>
						<p><?php echo ot_get_option('uni_home_grid_custom_text_three') ?></p>
						<span class="viewMore"><?php _e('view more', 'asana') ?>
							<i>
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="9px" height="15px" viewBox="0 0 9 15" enable-background="new 0 0 9 15" xml:space="preserve">
								<path fill="#96e9d5" d="M10-184.5l-0.826 0.757L1.826-177L1-177.758l7.349-6.742L1-191.243L1.826-192l7.349 6.743L10-184.5z M9.174-183.743L9.174-183.743L10-184.5L9.174-183.743z M9.175-185.257L10-184.5v0L9.175-185.257z"/>
								<path fill="#96e9d5" d="M9 7.5L8.174 8.257L0.826 15L0 14.242L7.349 7.5L0 0.757L0.826 0l7.349 6.743L9 7.5z M8.174 8.3 L8.174 8.257L9 7.5L8.174 8.257z M8.175 6.743L9 7.5v0L8.175 6.743z"/>
								</svg>
							</i>
						</span>
					</div>
				</a>
                <?php } ?>

				<div class="gridItemWrapLeft">

                    <?php if ( ot_get_option('uni_home_grid_custom_uri_four') ) { ?>
					<a href="<?php echo ot_get_option('uni_home_grid_custom_uri_four') ?>" class="gridItem gridItemWhite clear">
						<div class="gridItemImg">
                        <?php
                        $sHomeItemFourTitle = ot_get_option('uni_home_grid_custom_title_four');
                        if ( ot_get_option('uni_home_grid_custom_image_four') ) {
                        $aHomeItemFourImage = wp_get_attachment_image_src( ot_get_option('uni_home_grid_custom_image_four'), 'unithumb-homepostsmall' );
                        ?>
                            <img src="<?php echo $aHomeItemFourImage[0] ?>" alt="<?php if ( !empty($sHomeItemFourTitle) ) echo esc_attr($sHomeItemFourTitle); ?>" width="342" height="342">
                        <?php
                        } else {
                        ?>
                            <img src="<?php echo get_template_directory_uri() ?>/images/placeholders/unithumb-homepostsmall.jpg" alt="<?php if ( !empty($sHomeItemFourTitle) ) echo esc_attr($sHomeItemFourTitle); ?>" width="342" height="342">
                        <?php } ?>
						</div>
						<div class="gridItemDesc">
							<h3><?php if ( !empty($sHomeItemFourTitle) ) echo esc_html($sHomeItemFourTitle); ?></h3>
							<p><?php echo ot_get_option('uni_home_grid_custom_text_four') ?></p>
							<span class="viewMore"><?php _e('view more', 'asana') ?>
								<i>
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="9px" height="15px" viewBox="0 0 9 15" enable-background="new 0 0 9 15" xml:space="preserve">
									<path fill="#96e9d5" d="M10-184.5l-0.826 0.757L1.826-177L1-177.758l7.349-6.742L1-191.243L1.826-192l7.349 6.743L10-184.5z M9.174-183.743L9.174-183.743L10-184.5L9.174-183.743z M9.175-185.257L10-184.5v0L9.175-185.257z"/>
									<path fill="#96e9d5" d="M9 7.5L8.174 8.257L0.826 15L0 14.242L7.349 7.5L0 0.757L0.826 0l7.349 6.743L9 7.5z M8.174 8.3 L8.174 8.257L9 7.5L8.174 8.257z M8.175 6.743L9 7.5v0L8.175 6.743z"/>
									</svg>
								</i>
							</span>
						</div>
					</a>
                    <?php } ?>

                    <?php if ( ot_get_option('uni_home_grid_custom_uri_five') ) { ?>
					<a href="<?php echo ot_get_option('uni_home_grid_custom_uri_five') ?>" class="gridItem clear">
						<div class="gridItemImg">
                        <?php
                        $sHomeItemFiveTitle = ot_get_option('uni_home_grid_custom_title_five');
                        if ( ot_get_option('uni_home_grid_custom_image_five') ) {
                        $aHomeItemFiveImage = wp_get_attachment_image_src( ot_get_option('uni_home_grid_custom_image_five'), 'unithumb-homepostsmall' );
                        ?>
                            <img src="<?php echo $aHomeItemFiveImage[0] ?>" alt="<?php if ( !empty($sHomeItemFiveTitle) ) echo esc_attr($sHomeItemFiveTitle); ?>" width="342" height="342">
                        <?php
                        } else {
                        ?>
                            <img src="<?php echo get_template_directory_uri() ?>/images/placeholders/unithumb-homepostsmall.jpg" alt="<?php if ( !empty($sHomeItemFiveTitle) ) echo esc_attr($sHomeItemFiveTitle); ?>" width="342" height="342">
                        <?php } ?>
						</div>
						<div class="gridItemDesc">
							<h3><?php if ( !empty($sHomeItemFiveTitle) ) echo esc_html($sHomeItemFiveTitle); ?></h3>
							<p><?php echo ot_get_option('uni_home_grid_custom_text_five') ?></p>
							<span class="viewMore"><?php _e('view more', 'asana') ?>
								<i>
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="9px" height="15px" viewBox="0 0 9 15" enable-background="new 0 0 9 15" xml:space="preserve">
									<path fill="#96e9d5" d="M10-184.5l-0.826 0.757L1.826-177L1-177.758l7.349-6.742L1-191.243L1.826-192l7.349 6.743L10-184.5z M9.174-183.743L9.174-183.743L10-184.5L9.174-183.743z M9.175-185.257L10-184.5v0L9.175-185.257z"/>
									<path fill="#96e9d5" d="M9 7.5L8.174 8.257L0.826 15L0 14.242L7.349 7.5L0 0.757L0.826 0l7.349 6.743L9 7.5z M8.174 8.3 L8.174 8.257L9 7.5L8.174 8.257z M8.175 6.743L9 7.5v0L8.175 6.743z"/>
									</svg>
								</i>
							</span>
						</div>
					</a>
                    <?php } ?>

				</div>

                <?php if ( ot_get_option('uni_home_grid_custom_uri_six') ) { ?>
				<div class="gridItemWrapRight">
					<a href="<?php echo ot_get_option('uni_home_grid_custom_uri_six') ?>" class="gridItem2 clear">
                    <?php
                    $sHomeItemSixTitle = ot_get_option('uni_home_grid_custom_title_six');
                    if ( ot_get_option('uni_home_grid_custom_image_six') ) {
                    $aHomeItemSixImage = wp_get_attachment_image_src( ot_get_option('uni_home_grid_custom_image_six'), 'unithumb-homepostbig' );
                    ?>
                        <img src="<?php echo $aHomeItemSixImage[0] ?>" alt="<?php if ( !empty($sHomeItemSixTitle) ) echo esc_attr($sHomeItemSixTitle); ?>" width="684" height="684">
                    <?php
                    } else {
                    ?>
                        <img src="<?php echo get_template_directory_uri() ?>/images/placeholders/unithumb-homepostbig.jpg" alt="<?php if ( !empty($sHomeItemSixTitle) ) echo esc_attr($sHomeItemSixTitle); ?>" width="684" height="684">
                    <?php } ?>
						<div class="gridItemDesc">
							<h3><?php if ( !empty($sHomeItemSixTitle) ) echo esc_html($sHomeItemSixTitle); ?></h3>
							<p><?php echo ot_get_option('uni_home_grid_custom_text_six') ?></p>
							<span class="viewMore"><?php _e('view more', 'asana') ?>
								<i>
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="9px" height="15px" viewBox="0 0 9 15" enable-background="new 0 0 9 15" xml:space="preserve">
									<path fill="#96e9d5" d="M10-184.5l-0.826 0.757L1.826-177L1-177.758l7.349-6.742L1-191.243L1.826-192l7.349 6.743L10-184.5z M9.174-183.743L9.174-183.743L10-184.5L9.174-183.743z M9.175-185.257L10-184.5v0L9.175-185.257z"/>
									<path fill="#96e9d5" d="M9 7.5L8.174 8.257L0.826 15L0 14.242L7.349 7.5L0 0.757L0.826 0l7.349 6.743L9 7.5z M8.174 8.3 L8.174 8.257L9 7.5L8.174 8.257z M8.175 6.743L9 7.5v0L8.175 6.743z"/>
									</svg>
								</i>
							</span>
						</div>
					</a>
				</div>
                <?php } ?>

			</div> <!-- end of <?php echo $i ?>  -->

		</div>
    <?php } ?>

    <?php if ( ot_get_option('uni_home_grid_enable') == 'on' ) { ?>
		<div class="homeGrid">
    <?php
    $aSelectedPosts = array();
    for ( $l = 1; $l <= 6; $l++ ) {
        if ( ot_get_option( 'uni_home_posts_'.$l ) ) $aSelectedPosts[] = ot_get_option( 'uni_home_posts_'.$l );
    }
    $iNumberOfPosts = count($aSelectedPosts);
    if ( $iNumberOfPosts == 6 ) {
        $aFeaturedArgs = array(
            'post_type'	=> 'post',
            'post_status' => 'publish',
            'ignore_sticky_posts'	=> 1,
            'posts_per_page' => 6,
            'post__in' => $aSelectedPosts,
            'orderby' => 'post__in',
            'tax_query' => array(
                array(
                    'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array(
                        'post-format-aside',
                        'post-format-audio',
                        'post-format-chat',
                        'post-format-gallery',
                        'post-format-image',
                        'post-format-link',
                        'post-format-quote',
                        'post-format-status',
                        'post-format-video'
                    ),
                    'operator' => 'NOT IN'
                )
            )
        );
    } else {
        $aFeaturedArgs = array(
            'post_type'	=> 'post',
            'post_status' => 'publish',
            'ignore_sticky_posts'	=> 1,
            'posts_per_page' => 6,
            'tax_query' => array(
                array(
                    'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array(
                        'post-format-aside',
                        'post-format-audio',
                        'post-format-chat',
                        'post-format-gallery',
                        'post-format-image',
                        'post-format-link',
                        'post-format-quote',
                        'post-format-status',
                        'post-format-video'
                    ),
                    'operator' => 'NOT IN'
                )
            )
        );
    }

    $oFeaturedQuery = new WP_Query( $aFeaturedArgs );
    if ( $oFeaturedQuery->have_posts() ) :
    $iPostsFound = count($oFeaturedQuery->posts);
    $i = 1;
    while ( $oFeaturedQuery->have_posts() ) : $oFeaturedQuery->the_post(); ?>
        <?php if ( $i == 1 ) { ?>
			<div id="post-<?php the_ID(); ?>" <?php $classes_one[] = 'mainItem'; $classes_one[] = 'clear'; post_class( $classes_one ); ?>><!-- <?php echo $i ?>  -->
				<div class="mainItemImg">
                    <?php if ( has_post_thumbnail() ) { ?>
                        <?php the_post_thumbnail( 'unithumb-homepostbig', array( 'alt' => the_title_attribute('echo=0') ) ); ?>
                    <?php } else { ?>
					    <img src="<?php echo get_template_directory_uri() ?>/images/placeholders/unithumb-homepostbig.jpg" alt="<?php the_title_attribute() ?>" width="684" height="684">
                    <?php } ?>
				</div>
				<div class="mainItemDesc">
					<h3><?php the_title() ?></h3>
					<?php uni_excerpt(20, '', true) ?>
					<a href="<?php the_permalink() ?>" class="viewMore"><?php _e('view more', 'asana') ?></a>
				</div>
			</div> <!-- end of <?php echo $i ?>  -->
        <?php } ?>

        <?php if ( $i == 2 && $iPostsFound >= 6 ) { ?>
			<div class="gridItemWrap clear"> <!-- <?php echo $i ?>  -->
        <?php } ?>
            <?php if ( $i == 2 && $iPostsFound >= 6 ) { ?>
				<a href="<?php the_permalink() ?>" id="post-<?php the_ID(); ?>" <?php $classes_two[] = 'gridItem'; $classes_two[] = 'clear'; post_class($classes_two); ?>>
					<div class="gridItemImg">
                    <?php if ( has_post_thumbnail() ) { ?>
                        <?php the_post_thumbnail( 'unithumb-homepostsmall', array( 'alt' => the_title_attribute('echo=0') ) ); ?>
                    <?php } else { ?>
					    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholders/unithumb-homepostsmall.jpg" alt="<?php the_title_attribute() ?>" width="342" height="342">
                    <?php } ?>
					</div>
					<div class="gridItemDesc">
						<h3><?php the_title() ?></h3>
						<?php uni_excerpt(20, '', true) ?>
						<span class="viewMore"><?php _e('view more', 'asana') ?>
							<i>
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="9px" height="15px" viewBox="0 0 9 15" enable-background="new 0 0 9 15" xml:space="preserve">
									<path fill="#96e9d5" d="M10-184.5l-0.826 0.757L1.826-177L1-177.758l7.349-6.742L1-191.243L1.826-192l7.349 6.743L10-184.5z M9.174-183.743L9.174-183.743L10-184.5L9.174-183.743z M9.175-185.257L10-184.5v0L9.175-185.257z"/>
									<path fill="#96e9d5" d="M9 7.5L8.174 8.257L0.826 15L0 14.242L7.349 7.5L0 0.757L0.826 0l7.349 6.743L9 7.5z M8.174 8.3 L8.174 8.257L9 7.5L8.174 8.257z M8.175 6.743L9 7.5v0L8.175 6.743z"/>
								</svg>
							</i>
						</span>
					</div>
				</a>
            <?php } ?>
            <?php if ( $i == 3 && $iPostsFound >= 6 ) { ?>
				<a href="<?php the_permalink() ?>" id="post-<?php the_ID(); ?>" <?php $classes_three[] = 'gridItem'; $classes_three[] = 'gridItemWhite'; $classes_three[] = 'clear'; post_class($classes_three); ?>>
					<div class="gridItemImg">
                    <?php if ( has_post_thumbnail() ) { ?>
                        <?php the_post_thumbnail( 'unithumb-homepostsmall', array( 'alt' => the_title_attribute('echo=0') ) ); ?>
                    <?php } else { ?>
					    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholders/unithumb-homepostsmall.jpg" alt="<?php the_title_attribute() ?>" width="342" height="342">
                    <?php } ?>
					</div>
					<div class="gridItemDesc">
						<h3><?php the_title() ?></h3>
						<?php uni_excerpt(20, '', true) ?>
						<span class="viewMore"><?php _e('view more', 'asana') ?>
							<i>
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="9px" height="15px" viewBox="0 0 9 15" enable-background="new 0 0 9 15" xml:space="preserve">
								<path fill="#96e9d5" d="M10-184.5l-0.826 0.757L1.826-177L1-177.758l7.349-6.742L1-191.243L1.826-192l7.349 6.743L10-184.5z M9.174-183.743L9.174-183.743L10-184.5L9.174-183.743z M9.175-185.257L10-184.5v0L9.175-185.257z"/>
								<path fill="#96e9d5" d="M9 7.5L8.174 8.257L0.826 15L0 14.242L7.349 7.5L0 0.757L0.826 0l7.349 6.743L9 7.5z M8.174 8.3 L8.174 8.257L9 7.5L8.174 8.257z M8.175 6.743L9 7.5v0L8.175 6.743z"/>
								</svg>
							</i>
						</span>
					</div>
				</a>
            <?php } ?>
                <?php if ( $i == 4 && $iPostsFound >= 6 ) { ?>
				<div class="gridItemWrapLeft">
                <?php } ?>
                    <?php if ( $i == 4 && $iPostsFound >= 6 ) { ?>
					<a href="<?php the_permalink() ?>" id="post-<?php the_ID(); ?>" <?php $classes_four[] = 'gridItem'; $classes_four[] = 'gridItemWhite'; $classes_four[] = 'clear'; post_class($classes_four); ?>>
						<div class="gridItemImg">
                        <?php if ( has_post_thumbnail() ) { ?>
                            <?php the_post_thumbnail( 'unithumb-homepostsmall', array( 'alt' => the_title_attribute('echo=0') ) ); ?>
                        <?php } else { ?>
					        <img src="<?php echo get_template_directory_uri(); ?>/images/placeholders/unithumb-homepostsmall.jpg" alt="<?php the_title_attribute() ?>" width="342" height="342">
                        <?php } ?>
						</div>
						<div class="gridItemDesc">
							<h3><?php the_title() ?></h3>
							<?php uni_excerpt(20, '', true) ?>
							<span class="viewMore"><?php _e('view more', 'asana') ?>
								<i>
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="9px" height="15px" viewBox="0 0 9 15" enable-background="new 0 0 9 15" xml:space="preserve">
									<path fill="#96e9d5" d="M10-184.5l-0.826 0.757L1.826-177L1-177.758l7.349-6.742L1-191.243L1.826-192l7.349 6.743L10-184.5z M9.174-183.743L9.174-183.743L10-184.5L9.174-183.743z M9.175-185.257L10-184.5v0L9.175-185.257z"/>
									<path fill="#96e9d5" d="M9 7.5L8.174 8.257L0.826 15L0 14.242L7.349 7.5L0 0.757L0.826 0l7.349 6.743L9 7.5z M8.174 8.3 L8.174 8.257L9 7.5L8.174 8.257z M8.175 6.743L9 7.5v0L8.175 6.743z"/>
									</svg>
								</i>
							</span>
						</div>
					</a>
                    <?php } ?>
                    <?php if ( $i == 5 && $iPostsFound >= 6 ) { ?>
					<a href="<?php the_permalink() ?>" id="post-<?php the_ID(); ?>" <?php $classes_five[] = 'gridItem'; $classes_five[] = 'clear'; post_class($classes_five); ?>>
						<div class="gridItemImg">
                        <?php if ( has_post_thumbnail() ) { ?>
                            <?php the_post_thumbnail( 'unithumb-homepostsmall', array( 'alt' => the_title_attribute('echo=0') ) ); ?>
                        <?php } else { ?>
					        <img src="<?php echo get_template_directory_uri(); ?>/images/placeholders/unithumb-homepostsmall.jpg" alt="<?php the_title_attribute() ?>" width="342" height="342">
                        <?php } ?>
						</div>
						<div class="gridItemDesc">
							<h3><?php the_title() ?></h3>
							<?php uni_excerpt(20, '', true) ?>
							<span class="viewMore"><?php _e('view more', 'asana') ?>
								<i>
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="9px" height="15px" viewBox="0 0 9 15" enable-background="new 0 0 9 15" xml:space="preserve">
									<path fill="#96e9d5" d="M10-184.5l-0.826 0.757L1.826-177L1-177.758l7.349-6.742L1-191.243L1.826-192l7.349 6.743L10-184.5z M9.174-183.743L9.174-183.743L10-184.5L9.174-183.743z M9.175-185.257L10-184.5v0L9.175-185.257z"/>
									<path fill="#96e9d5" d="M9 7.5L8.174 8.257L0.826 15L0 14.242L7.349 7.5L0 0.757L0.826 0l7.349 6.743L9 7.5z M8.174 8.3 L8.174 8.257L9 7.5L8.174 8.257z M8.175 6.743L9 7.5v0L8.175 6.743z"/>
									</svg>
								</i>
							</span>
						</div>
					</a>
                    <?php } ?>
                <?php if ( $i == 6 && $iPostsFound >= 6 ) { ?>
				</div>
                <?php } ?>
            <?php if ( $i == 6 && $iPostsFound >= 6 ) { ?>
				<div class="gridItemWrapRight">
					<a href="<?php the_permalink() ?>" id="post-<?php the_ID(); ?>" <?php $classes_six[] = 'gridItem2'; $classes_six[] = 'clear'; post_class($classes_six); ?>>
                    <?php if ( has_post_thumbnail() ) { ?>
                        <?php the_post_thumbnail( 'unithumb-homepostbig', array( 'alt' => the_title_attribute('echo=0') ) ); ?>
                    <?php } else { ?>
					    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholders/unithumb-homepostbig.jpg" alt="<?php the_title_attribute() ?>" width="684" height="684">
                    <?php } ?>
						<div class="gridItemDesc">
							<h3><?php the_title() ?></h3>
							<?php uni_excerpt(20, '', true) ?>
							<span class="viewMore"><?php _e('view more', 'asana') ?>
								<i>
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="9px" height="15px" viewBox="0 0 9 15" enable-background="new 0 0 9 15" xml:space="preserve">
									<path fill="#96e9d5" d="M10-184.5l-0.826 0.757L1.826-177L1-177.758l7.349-6.742L1-191.243L1.826-192l7.349 6.743L10-184.5z M9.174-183.743L9.174-183.743L10-184.5L9.174-183.743z M9.175-185.257L10-184.5v0L9.175-185.257z"/>
									<path fill="#96e9d5" d="M9 7.5L8.174 8.257L0.826 15L0 14.242L7.349 7.5L0 0.757L0.826 0l7.349 6.743L9 7.5z M8.174 8.3 L8.174 8.257L9 7.5L8.174 8.257z M8.175 6.743L9 7.5v0L8.175 6.743z"/>
									</svg>
								</i>
							</span>
						</div>
					</a>
				</div>
            <?php } ?>
        <?php if ( $i == 6 && $iPostsFound >= 6 ) { ?>
			</div> <!-- end of <?php echo $i ?>  -->
        <?php } ?>
	<?php
    $i++;
    endwhile; endif;
	wp_reset_postdata(); ?>
		</div>
    <?php } ?>

    <?php if ( ot_get_option('uni_home_membership_cards_enable') == 'on' ) { ?>
    <div class="membershipCardsBlock">
        <div class="blockTitle"><?php echo ( ot_get_option( 'uni_home_membership_cards_title' ) ) ? esc_html( ot_get_option( 'uni_home_membership_cards_title' ) ) : __('Membership cards', 'asana'); ?></div>
        <div class="membershipCardsWrap">
    <?php
    $aPricesArgs = array(
        'post_type'	=> 'uni_price',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'posts_per_page' => 3,
    );

    $oPricesQuery = new WP_Query( $aPricesArgs );
    if ( $oPricesQuery->have_posts() ) :
    while ( $oPricesQuery->have_posts() ) : $oPricesQuery->the_post();
    $aPostCustom = get_post_custom( $post->ID );
    ?>
            <div class="membershipCardItem">
                <h3><?php the_title() ?></h3>
                <div class="membershipCard">
                    <span><span><?php if ( !empty($aPostCustom['uni_currency'][0]) ) echo esc_html($aPostCustom['uni_currency'][0]) ?></span><?php if ( !empty($aPostCustom['uni_price_val'][0]) ) echo esc_html($aPostCustom['uni_price_val'][0]) ?></span>
                    <em><?php if ( !empty($aPostCustom['uni_period'][0]) ) echo esc_html($aPostCustom['uni_period'][0]) ?></em>
                </div>
                <?php uni_excerpt(40, '', true) ?>
                <?php if ( isset($aPostCustom['uni_order_button_ext_url_enable'][0]) && $aPostCustom['uni_order_button_ext_url_enable'][0] == 'on' && !empty($aPostCustom['uni_order_button_uri'][0]) ) { ?>
                <a href="<?php echo $aPostCustom['uni_order_button_uri'][0]; ?>" class="membership-card-order"><?php echo ( !empty($aPostCustom['uni_order_button_text'][0]) ) ? $aPostCustom['uni_order_button_text'][0] : __('Order Now', 'asana') ?></a>
                <?php } else { ?>
                <a href="#membershipCardOrderPopup" class="membershipCardOrder membership-card-order" data-priceid="<?php echo $post->ID; ?>" data-pricetitle="<?php echo esc_attr( get_the_title($post->ID) ) ?>"><?php echo ( !empty($aPostCustom['uni_order_button_text'][0]) ) ? $aPostCustom['uni_order_button_text'][0] : __('Order Now', 'asana') ?></a>
                <?php } ?>
            </div>
	<?php endwhile; endif;
	wp_reset_postdata(); ?>
        </div>
    </div>
    <?php } ?>

    <?php if ( ot_get_option('uni_home_shop_enable') == 'on' ) { ?>
    <?php if ( class_exists( 'WooCommerce' ) ) { ?>
    	<div class="woocommerce">
		<div class="shopItems">
			<div class="blockTitle"><?php _e('yoga shop', 'asana') ?></div>
			<ul class="shopItemsWrap">
    <?php
    if ( ot_get_option( 'uni_home_products_type' ) == 'bestsellers' ) {
        $args = array(
            'post_type'	=> 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts'	=> 1,
            'orderby' => 'meta_value_num',
            'posts_per_page' => 8,
            'meta_query' => array(
                array(
                    'key' => 'total_sales',
                    'compare' => 'EXISTS',
                )
            )
        );
        $products = new WP_Query( $args );
    } else {
        $args = array(
            'post_type'	=> 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts'	=> 1,
            'orderby' => 'rand',
            'posts_per_page' => 8,
            'meta_query' => array(
                array(
                    'key' => '_visibility',
                    'value' => array('catalog', 'visible'),
                    'compare' => 'IN'
                )
            )
        );
        $products = new WP_Query( $args );
    }

    if ( $products->have_posts() ) :
    while ( $products->have_posts() ) : $products->the_post();

        wc_get_template_part( 'content', 'product' );

	endwhile; endif;
	wp_reset_postdata(); ?>
			</ul>
			<a href="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" class="showAllItems"><?php _e('Shop all', 'asana') ?></a>
		</div>
		</div>
    <?php } ?>
    <?php } ?>

    <?php if ( ot_get_option('uni_home_blog_enable') == 'on' || !ot_get_option('uni_home_blog_enable') ) { ?>
		<div class="blogPosts">
			<div class="blockTitle"><?php _e('blog', 'asana') ?></div>
			<div class="blogPostWrap">
    <?php
    $aBlogArgs = array(
        'post_type'	=> 'post',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'posts_per_page' => 3,
    );

    $oBlogQuery = new WP_Query( $aBlogArgs );
    if ( $oBlogQuery->have_posts() ) :
    while ( $oBlogQuery->have_posts() ) : $oBlogQuery->the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class('postItem'); ?>>
					<a href="<?php the_permalink() ?>" class="postItemImg">
                        <?php if ( has_post_thumbnail() ) { ?>
                            <?php the_post_thumbnail( 'unithumb-blog', array( 'alt' => the_title_attribute('echo=0') ) ); ?>
                        <?php } else { ?>
						    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholders/unithumb-blog.jpg" alt="<?php the_title_attribute() ?>" width="408" height="272">
                        <?php } ?>
					</a>
					<h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
					<?php uni_excerpt(12, '', true) ?>
				</div>

	<?php endwhile; endif;
	wp_reset_postdata(); ?>
			</div>
		</div>
    <?php } ?>

    <?php
    if ( ot_get_option( 'uni_home_classes_enable' ) == 'on' ) {
        $iClassesPageUrl = ( ot_get_option( 'uni_home_classes_button_uri' ) ) ? ot_get_option( 'uni_home_classes_button_uri' ) : '';
        if ( ot_get_option( 'uni_home_classes_bg' ) ) {
            $iClassesBgAttachId = ot_get_option( 'uni_home_classes_bg' );
            $aClassesBgAttach = wp_get_attachment_image_src( $iClassesBgAttachId, 'full' );
            $sClassesImage = $aClassesBgAttach[0];
        } else {
            $sClassesImage = get_template_directory_uri().'/images/placeholders/subscribe.jpg';
        } ?>
		<div class="classesBox" data-type="parallax" data-speed="10" style="background-image: url(<?php echo esc_url( $sClassesImage ); ?>);">
            <div class="classesBoxDesc">
                <a href="<?php if ( !empty($iClassesPageUrl) ) echo $iClassesPageUrl; ?>" class="classesCategory"><?php echo ( ot_get_option( 'uni_home_classes_small_title' ) ) ? esc_html( ot_get_option( 'uni_home_classes_small_title' ) ) : __('classes', 'asana'); ?></a>
                <h3><?php echo ( ot_get_option( 'uni_home_classes_title' ) ) ? esc_html( ot_get_option( 'uni_home_classes_title' ) ) : __('choose your classes and start <br> your training', 'asana'); ?></h3>
                <?php if ( !empty($iClassesPageUrl) ) { ?>
                <a href="<?php echo esc_url( $iClassesPageUrl ); ?>" class="viewClasses"><?php echo ( ot_get_option( 'uni_home_classes_button_title' ) ) ? esc_html( ot_get_option( 'uni_home_classes_button_title' ) ) : __('view classes', 'asana'); ?></a>
                <?php } ?>    
            </div>
		</div>
    <?php } ?>
	</section>

<?php get_footer(); ?>