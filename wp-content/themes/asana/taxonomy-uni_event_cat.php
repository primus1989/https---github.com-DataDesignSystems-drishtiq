<?php get_header();
$sEventCatSlug = get_query_var( 'uni_event_cat', null );
        $iCheckoutAttachId = ( ot_get_option( 'uni_events_header_bg' ) ) ? ot_get_option( 'uni_events_header_bg' ) : '';
        if ( !empty($iCheckoutAttachId) ) {
            $aPageHeaderImage = wp_get_attachment_image_src( $iCheckoutAttachId, 'full' );
            $sPageHeaderImage = $aPageHeaderImage[0];
        } else {
            $sPageHeaderImage = get_template_directory_uri().'/images/placeholders/pageheader-events.jpg';
        }
        $sDateAndTimeFormat = get_option( 'date_format' ).' '.get_option( 'time_format' );
?>

	<section class="container">

		<div class="pageHeader" style="background-image: url(<?php echo esc_url( $sPageHeaderImage ); ?>);">
			<h1><?php echo ( ot_get_option( 'uni_events_header_title' ) ) ? ot_get_option( 'uni_events_header_title' ) : __('follow our events', 'asana'); ?></h1>
		</div>

		<div class="contentWrap">
			<div class="pagePanel clear">
				<div class="pageTitle"><?php single_term_title(); ?></div>
                <?php
                $aEventTerms = get_terms( 'uni_event_cat' );
                if ( !empty($aEventTerms) && !is_wp_error($aEventTerms) ) {
                ?>
				<div class="categoryList">
					<span><?php _e('category', 'asana') ?> <i></i></span>
					<ul>
						<li><a href="<?php if ( ot_get_option( 'uni_events_page' ) ) echo get_permalink( ot_get_option( 'uni_events_page' ) ); ?>"><?php _e('All', 'asana') ?></a></li>
                    <?php foreach ( $aEventTerms as $oTerm ) { ?>
						<li<?php if ( $sEventCatSlug == $oTerm->slug ) echo ' class="current"'; ?>><a href="<?php echo get_term_link($oTerm) ?>"><?php echo esc_html( $oTerm->name ); ?></a></li>
					<?php } ?>
					</ul>
				</div>
                <?php } ?>
			</div>
			<div class="eventsWrap">
        <?php if (have_posts()) : while (have_posts()) : the_post();
            $aCustomData = get_post_custom( $post->ID );
        ?>
				<div id="post-<?php the_ID(); ?>" <?php post_class('eventItem clear') ?>>
					<a href="<?php echo the_permalink() ?>" class="eventItemImg">
                        <?php if ( has_post_thumbnail() ) { ?>
                            <?php the_post_thumbnail( 'unithumb-eventpost', array( 'alt' => the_title_attribute('echo=0') ) ); ?>
                        <?php } else { ?>
						    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholders/unithumb-eventpost.jpg" alt="<?php the_title_attribute() ?>" width="502" height="342">
                        <?php } ?>
					</a>
					<div class="eventItemDesc">
						<time class="eventItemTime" datetime="<?php the_time('Y-m-d'); ?>"><?php the_time( $sDateAndTimeFormat ); ?></time>
						<h3><a href="<?php echo the_permalink() ?>"><?php echo the_title() ?></a></h3>
						<?php uni_excerpt(30, '', true) ?>
						<a href="<?php echo the_permalink() ?>" class="eventLearnMore"><?php _e('learn more', 'asana') ?>
							<i>
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="38px" height="38px" viewBox="0 0 38 38" enable-background="new 0 0 38 38" xml:space="preserve">
									<path fill="#6BBFAC" d="M16.558 11.5l6.884 6.494l1.059 0.999v0.015L16.558 26.5L15.5 25.486l6.882-6.494L15.5 12.5L16.558 11.5z"/>
								</svg>
							</i>
						</a>
					</div>
				</div>
        <?php
        endwhile;
        endif;
        ?>

		    <div class="pagination clear"<?php if ( ot_get_option('uni_ajax_scroll_enable_events') == 'on' ) { echo ' style="display:none;"'; } ?>>
                <?php uni_pagination(); ?>
		    </div>
				<?php /*<span class="showMoreEvents">show more</span>*/ ?>
			</div>

    <?php if ( ot_get_option('uni_mailchimp_events_enable') == 'on' ) { ?>
    <?php
        $iSubscribeAttachId = ( ot_get_option( 'uni_subscribe_header_bg' ) ) ? ot_get_option( 'uni_subscribe_header_bg' ) : '';
        if ( !empty($iSubscribeAttachId) ) {
            $aPageHeaderImage = wp_get_attachment_image_src( $iSubscribeAttachId, 'full' );
            $sPageHeaderImage = $aPageHeaderImage[0];
        } else {
            $sPageHeaderImage = get_template_directory_uri().'/images/placeholders/pageheader-events.jpg';
        }
    ?>
			<div class="subscribeBox" style="background-image: url(<?php echo esc_url( $sPageHeaderImage ); ?>);">
				<i class="iconEmail"></i>
				<h3><?php echo ( ot_get_option( 'uni_subscribe_header_title' ) ) ? esc_html( ot_get_option( 'uni_subscribe_header_title' ) ) : __('subscribe to our newsletter', 'asana'); ?></h3>
				<p><?php echo ( ot_get_option( 'uni_subscribe_header_subtitle' ) ) ? esc_html( ot_get_option( 'uni_subscribe_header_subtitle' ) ) : __('Subscribe and take all information about our latest events', 'asana'); ?></p>
		        <form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" role="form" method="post" class="clear uni_form">
                    <input type="hidden" name="action" value="uni_mailchimp_subscribe_user" />
					<input type="text" name="uni_input_email" size="20" value="" placeholder="<?php _e('Your email', 'asana' ); ?>" data-parsley-required="true" data-parsley-trigger="change focusout submit" data-parsley-type="email">
					<input class="subscribeSubmit uni_input_submit" type="button" value="<?php _e('subscribe', 'asana' ); ?>">
				</form>
			</div>
    <?php } ?>

		</div>

	</section>

<?php get_footer(); ?>