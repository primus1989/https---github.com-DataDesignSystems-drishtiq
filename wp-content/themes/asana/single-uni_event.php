<?php get_header();
$sDateAndTimeFormat = get_option( 'date_format' ).' '.get_option( 'time_format' ); ?>

	<section class="container">

        <?php if (have_posts()) : while (have_posts()) : the_post();
            $aCustomData = get_post_custom( $post->ID );
            if ( !empty($aCustomData['uni_event_page_header_image'][0]) ) {
                $aPageHeaderAttachIds = explode(',', $aCustomData['uni_event_page_header_image'][0]);
                $aPageHeaderImage = wp_get_attachment_image_src( $aPageHeaderAttachIds[0], 'full' );
                $sPageHeaderImage = $aPageHeaderImage[0];
            } else {
                $sPageHeaderImage = get_template_directory_uri() . '/images/placeholders/pageheader-singleevent.jpg';
            }
        ?>
		<div class="pageHeader" style="background-image: url(<?php echo esc_url( $sPageHeaderImage ); ?>);"></div>

		<div class="contentWrap">
			<div class="pagePanel clear">
            <?php
            if ( !empty($aCustomData['uni_local_events_page'][0]) ) {
            ?>
				<a href="<?php echo get_permalink( $aCustomData['uni_local_events_page'][0] ); ?>" class="backToBtn"><i></i> <?php _e('Back to events', 'asana' ) ?></a>
            <?php
            } else { ?>
				<a href="<?php if ( ot_get_option( 'uni_events_page' ) ) echo get_permalink( ot_get_option( 'uni_events_page' ) ); ?>" class="backToBtn"><i></i> <?php _e('Back to events', 'asana' ) ?></a>
            <?php } ?>
			</div>

			<div class="wrapper">
				<div id="post-<?php the_ID(); ?>" <?php post_class('singlePostWrap clear') ?>>
					<h1 class="singleTitle"><?php the_title() ?></h1>
					<div class="singleEventDetails clear">
						<div class="fcell">
							<div class="eventDetailItem">
								<i class="fa fa-calendar"></i>
								<p><?php if ( !empty($aCustomData['uni_event_date'][0]) ) { echo esc_html( $aCustomData['uni_event_date'][0] ); } else { _e('- not specified -', 'asana'); } ?></p>
							</div>
							<div class="eventDetailItem">
								<i class="fa fa-clock-o"></i>
								<p><?php if ( !empty($aCustomData['uni_event_time'][0]) ) { echo esc_html( $aCustomData['uni_event_time'][0] ); } else { _e('- not specified -', 'asana'); } ?></p>
							</div>
							<div class="eventDetailItem">
								<i class="fa fa-map-marker"></i>
								<p><?php if ( !empty($aCustomData['uni_event_address'][0]) ) { echo esc_html( $aCustomData['uni_event_address'][0] ); } else { _e('- not specified -', 'asana'); } ?></p>
							</div>
							<div class="eventDetailItem">
								<i class="fa fa-credit-card"></i>
								<p><?php if ( !empty($aCustomData['uni_event_price'][0]) ) { echo esc_html( $aCustomData['uni_event_price'][0] ); } else { _e('- not specified -', 'asana'); } ?></p>
							</div>
						</div>
						<div class="scell">
							<!-- Map -->
							<script type="text/javascript">
							    // Asana style
							      var Asana = [
							          { "featureType": "road.highway", "stylers": [ { "visibility": "off" } ]
							        },{ "featureType": "landscape", "stylers": [ { "visibility": "off" } ]
							        },{ "featureType": "transit", "stylers": [ { "visibility": "off" } ]
							        },{ "featureType": "poi", "stylers": [ { "visibility": "off" } ]
							        },{ "featureType": "poi.park", "stylers": [ { "visibility": "on" } ]
							        },{ "featureType": "poi.park", "elementType": "labels", "stylers": [ { "visibility": "off" } ]
							        },{ "featureType": "poi.park", "elementType": "geometry.fill", "stylers": [ { "color": "#d3d3d3" }, { "visibility": "on" } ]
							        },{ "featureType": "poi.medical", "stylers": [ { "visibility": "off" } ]
							        },{ "featureType": "poi.medical", "stylers": [ { "visibility": "off" } ]
							        },{ "featureType": "road", "elementType": "geometry.stroke", "stylers": [ { "color": "#cccccc" } ]
							        },{ "featureType": "water", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#cecece" } ]
							        },{ "featureType": "road.local", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#808080" } ]
							        },{ "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#808080" } ]
							        },{ "featureType": "road", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#fdfdfd" } ]
							        },{ "featureType": "road", "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ]
							        },{ "featureType": "water", "elementType": "labels", "stylers": [ { "visibility": "off" } ]
							        },{ "featureType": "poi", "elementType": "geometry.fill", "stylers": [ { "color": "#d2d2d2" } ]
							        }
							      ];
							      function initialize() {

							        // Declare new style
							        var AsanastyledMap = new google.maps.StyledMapType(Asana, {name: "Asana"});

							        // Declare Map options
							        var mapOptions = {
							        	center: new google.maps.LatLng(<?php if ( !empty($aCustomData['uni_event_coord'][0]) ) { echo esc_js( $aCustomData['uni_event_coord'][0] ); } else { echo '40.777504,-73.9549428'; } ?>),
							        	zoom: <?php if ( !empty($aCustomData['uni_event_zoom'][0]) ) { echo esc_js( $aCustomData['uni_event_zoom'][0] ); } else { echo '12'; } ?>,
							        	scrollwheel: false,
							        	mapTypeControl:false,
						                streetViewControl: false,
						                panControl:false,
						                rotateControl:false,
						                zoomControl:true
							        };

							        // Create map
							        var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

							        // Setup skin for the map
							        map.mapTypes.set('Asana_style', AsanastyledMap);
							        map.setMapTypeId('Asana_style');

						            //add marker
                                    <?php
                                    if ( !ot_get_option( 'uni_color_schemes' ) ) {
                                        if ( file_exists( trailingslashit( get_stylesheet_directory_uri() ).'images/marker-default.svg' ) ) {
                                    ?>
                                    var marker_icon = "<?php echo trailingslashit( get_stylesheet_directory_uri() ).'images/marker_small-default.svg' ?>";
                                    <?php
                                        } else {
                                    ?>
                                    var marker_icon = "<?php echo trailingslashit( get_template_directory_uri() ).'images/marker_small-default.svg' ?>";
                                    <?php
                                        }
                                    } else {
                                    $sColourScheme = ot_get_option( 'uni_color_schemes' );
                                        if ( file_exists( trailingslashit( get_stylesheet_directory_uri() ).'images/marker-default.svg' ) ) {
                                    ?>
                                    var marker_icon = "<?php echo trailingslashit( get_stylesheet_directory_uri() ).'images/marker_small-'.$sColourScheme.'.svg' ?>";
                                    <?php
                                        } else {
                                    ?>
                                    var marker_icon = "<?php echo trailingslashit( get_template_directory_uri() ).'images/marker_small-'.$sColourScheme.'.svg' ?>";
                                    <?php
                                        }
                                    }
                                    ?>
						            var myLatLng = new google.maps.LatLng(<?php if ( !empty($aCustomData['uni_event_coord'][0]) ) { echo esc_js( $aCustomData['uni_event_coord'][0] ); } else { echo '40.777504,-73.9549428'; } ?>);
						            var beachMarker = new google.maps.Marker({
						                position: myLatLng,
						                map: map,
						                icon: marker_icon
						            });

							      }
							      google.maps.event.addDomListener(window, 'load', initialize);
							</script>

							<div class="location-map">
								<div class="map" id="map-canvas"></div>
							</div>
						</div>

						<div class="clear"></div>
            <?php
            if ( ( !empty($aCustomData['uni_local_events_join_on'][0]) && $aCustomData['uni_local_events_join_on'][0] == 'on' )
                || ( ot_get_option( 'uni_events_join_on' ) == 'on' && empty($aCustomData['uni_local_events_join_on'][0]) ) ) {
            ?>
						<div class="singleEventJoinBtnWrap">
							<a id="joinEventBtn" href="#eventRegistrationPopup">
                            <?php echo ( !empty($aCustomData['uni_local_events_button_text'][0]) ) ? esc_html( $aCustomData['uni_local_events_button_text'][0] ) : __('Join event', 'asana'); ?>
                            </a>
						</div>
            <?php
            }
            ?>
					</div>

                    <?php the_content() ?>

				</div>

                <?php include(locate_template('includes/social-links.php')); ?>

			</div>
		</div>

        <?php
        $oNextPost = get_next_post();
        if ( !empty($oNextPost) ) {
            $aCustomDataNextPost = get_post_custom( $oNextPost->ID );
            if ( !empty($aCustomDataNextPost['uni_event_page_header_image'][0]) ) {
                $aPageHeaderAttachIds = explode(',', $aCustomDataNextPost['uni_event_page_header_image'][0]);
                $aPageHeaderImage = wp_get_attachment_image_src( $aPageHeaderAttachIds[0], 'full' );
                $sPageHeaderImageNextPost = $aPageHeaderImage[0];
            } else {
                $sPageHeaderImageNextPost = get_template_directory_uri() . '/images/placeholders/pageheader-singleevent.jpg';
            }
        ?>
		<div class="nextEventBox" style="background-image: url(<?php echo esc_url( $sPageHeaderImageNextPost ); ?>);">
			<time class="eventItemTime"><?php echo esc_html( $aCustomDataNextPost['uni_event_date'][0] ); ?></time>
			<h3><?php echo esc_html( $oNextPost->post_title ); ?></h3>
			<a href="<?php echo get_permalink( $oNextPost->ID ) ?>" class="nextEventBtn"><?php _e('read next', 'asana') ?></a>
		</div>
        <?php } ?>

        <?php endwhile; endif; ?>

	</section>

    <?php
    if ( ( !empty($aCustomData['uni_local_events_join_on'][0]) && $aCustomData['uni_local_events_join_on'][0] == 'on' )
        || ( ot_get_option( 'uni_events_join_on' ) == 'on' && empty($aCustomData['uni_local_events_join_on'][0]) ) ) {
    ?>
	<div id="eventRegistrationPopup" class="eventRegistrationWrap">
		<h3><?php echo ( !empty($aCustomData['uni_local_events_button_text'][0]) ) ? esc_html( $aCustomData['uni_local_events_button_text'][0] ) : __('Join event', 'asana'); ?></h3>

		<form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post" class="eventRegistrationForm clear uni_form">
            <input type="hidden" name="uni_contact_nonce" value="<?php echo wp_create_nonce('uni_nonce') ?>" />
            <input type="hidden" name="action" value="uni_join_event_form" />
            <input type="hidden" name="event_id" value="<?php echo get_the_ID() ?>" />

			<div class="form-row form-row-first">
				<input class="formInput" type="text" name="uni_contact_firstname" value="" placeholder="<?php _e('First Name', 'asana') ?>" data-parsley-required="true" data-parsley-trigger="change focusout submit">
			</div>
			<div class="form-row form-row-last">
				<input class="formInput" type="text" name="uni_contact_lastname" value="" placeholder="<?php _e('Last Name', 'asana') ?>" data-parsley-required="true" data-parsley-trigger="change focusout submit">
			</div>
			<div class="form-row form-row-first">
				<input class="formInput" type="text" name="uni_contact_email" value="" placeholder="<?php _e('Your Email', 'asana') ?>" data-parsley-required="true" data-parsley-trigger="change focusout submit"  data-parsley-type="email">
			</div>
			<div class="form-row form-row-last">
				<input class="formInput" type="text" name="uni_contact_phone" value="" placeholder="<?php _e('Phone Number', 'asana') ?>" data-parsley-required="true" data-parsley-trigger="change focusout submit" data-parsley-type="integer">
			</div>
			<div class="clear"></div>
			<div class="form-row">
				<textarea class="formTextarea" name="uni_contact_msg" id="" cols="30" rows="10" placeholder="<?php _e('Message', 'asana') ?>" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>
			</div>
			<input class="submitEventRegistrationBtn uni_input_submit" type="button" value="<?php _e('Send', 'asana') ?>">
		</form>

	</div>
    <?php
    }
    ?>

<?php get_footer(); ?>