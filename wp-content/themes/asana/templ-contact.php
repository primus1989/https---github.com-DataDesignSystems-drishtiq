<?php
/*
*  Template Name: Contact Page
*/
get_header();
        $iContactAttachId = ( ot_get_option( 'uni_contact_header_bg' ) ) ? ot_get_option( 'uni_contact_header_bg' ) : '';
        if ( !empty($iContactAttachId) ) {
            $aPageHeaderImage = wp_get_attachment_image_src( $iContactAttachId, 'full' );
            $sPageHeaderImage = $aPageHeaderImage[0];
        } else {
            $sPageHeaderImage = get_template_directory_uri().'/images/placeholders/pageheader-cart.jpg';
        }
?>

    <?php if (have_posts()) : while (have_posts()) : the_post();
		$aPostCustom = get_post_custom( $post->ID );
    ?>
	<section class="container">
		<div class="pageHeader" style="background-image: url(<?php echo esc_url( $sPageHeaderImage ); ?>);">
            <?php
            $sTitleColor = ( ot_get_option( 'uni_contact_header_title_color' ) ) ? ot_get_option( 'uni_contact_header_title_color' ) : '#ffffff';
		    if ( ot_get_option( 'uni_contact_header_title' ) ) {
                $sOutput = ot_get_option( 'uni_contact_header_title' );
            } else {
			    $sOutput = __('You are welcome', 'asana');
            }
            echo '<h1>'.esc_html( $sOutput ).'</h1>';
            echo '<style>.pageHeader h1 {color:'.$sTitleColor.';}</style>';
            ?>
		</div>
		<div class="ourContact">
			<div class="wrapper clear">
				<div class="contactGallery">
                <?php
                if ( !empty($aPostCustom['uni_gallery'][0]) ) {
                $aPageGalleryIds = explode(',', $aPostCustom['uni_gallery'][0]);
                ?>
					<ul>
                    <?php foreach ( $aPageGalleryIds as $iAttachId ) { ?>
						<li><?php echo wp_get_attachment_image( $iAttachId, 'unithumb-contactgallery' ); ?></li>
					<?php } ?>
					</ul>
                <?php
                } else {
                ?>
					<ul>
						<li><img src="<?php echo get_template_directory_uri(); ?>/images/placeholders/gallery.jpg" alt=""></li>
						<li><img src="<?php echo get_template_directory_uri(); ?>/images/placeholders/story.jpg" alt=""></li>
					</ul>
                <?php
                }
                ?>
				</div>
				<div class="contactInfo">
					<h3><?php _e('Contact', 'asana') ?></h3>
					<p><i class="contactPhone"></i> <?php echo ( ot_get_option( 'uni_phone' ) ) ? esc_html( ot_get_option( 'uni_phone' ) ) : '+88 (0) 101 0000 000'; ?></p>
                    <?php $sEmail = ( ot_get_option( 'uni_email' ) ) ? esc_html( ot_get_option( 'uni_email' ) ) : esc_html( get_bloginfo('admin_email') ); ?>
					<p><i class="contactEmail"></i> <?php echo antispambot( $sEmail ); ?></p>
					<p><i class="contactLocation"></i> <?php echo ( ot_get_option( 'uni_address' ) ) ? esc_html( ot_get_option( 'uni_address' ) ) : '350 5th Ave, New York, <br> NY 10118, United States'; ?></p>
				</div>
			</div>
		</div>

    <?php if ( !empty($aPostCustom['uni_map_enable'][0]) && $aPostCustom['uni_map_enable'][0] == 'on' ) { ?>
		<div class="locationMap">
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
			        	center: new google.maps.LatLng(<?php $sCoord = ( ot_get_option( 'uni_coordinates' ) ) ? ot_get_option( 'uni_coordinates' ) : '41.404182,2.199451'; echo esc_js( $sCoord ); ?>),
	        			zoom: <?php echo ( ot_get_option( 'uni_zoom' ) ) ? esc_js( ot_get_option( 'uni_zoom' ) ) : '14'; ?>,
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
                    var marker_icon = "<?php echo trailingslashit( get_stylesheet_directory_uri() ).'images/marker-default.svg' ?>";
                    <?php
                        } else {
                    ?>
                    var marker_icon = "<?php echo trailingslashit( get_template_directory_uri() ).'images/marker-default.svg' ?>";
                    <?php
                        }
                    } else {
                    $sColourScheme = ot_get_option( 'uni_color_schemes' );
                        if ( file_exists( trailingslashit( get_stylesheet_directory_uri() ).'images/marker-'.$sColourScheme.'.svg' ) ) {
                    ?>
                    var marker_icon = "<?php echo trailingslashit( get_stylesheet_directory_uri() ).'images/marker-'.$sColourScheme.'.svg' ?>";
                    <?php
                        } else {
                    ?>
                    var marker_icon = "<?php echo trailingslashit( get_template_directory_uri() ).'images/marker-'.$sColourScheme.'.svg' ?>";
                    <?php
                        }
                    }
                    ?>
		            var myLatLng = new google.maps.LatLng(<?php echo esc_js( $sCoord ); ?>);
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
    <?php } ?>

    <?php if ( !empty($aPostCustom['uni_form_enable'][0]) && $aPostCustom['uni_form_enable'][0] == 'on' ) { ?>
		<?php if( in_array('contact-form-7/wp-contact-form-7.php', get_option('active_plugins')) && ot_get_option( 'uni_contact_form_seven_id' ) ) { ?>
			<div class="contactForm">
				<h3><?php _e('Say Hello', 'asana') ?></h3>
				<p class="contactFormDesc"><?php _e('We love to meet people and talk about possibilities', 'asana') ?></p>
                <?php echo do_shortcode('[contact-form-7 id="'.ot_get_option( 'uni_contact_form_seven_id' ).'"]'); ?>
			</div>
            <?php } else { ?>
			<div class="contactForm">
				<h3><?php _e('Contact Form', 'asana') ?></h3>
				<p class="contactFormDesc"><?php _e('We love to meet people and talk about possibilities', 'asana') ?></p>
		        <form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post" class="clear uni_form">
                    <input type="hidden" name="uni_contact_nonce" value="<?php echo wp_create_nonce('uni_nonce') ?>" />
                    <input type="hidden" name="action" value="uni_contact_form" />

                    <div class="form-row form-row-first">
                    	<input class="formInput userName" type="text" name="uni_contact_name" value="" placeholder="<?php _e('Name', 'asana') ?>" data-parsley-required="true" data-parsley-trigger="change focusout submit">
                    </div>
                    <div class="form-row form-row-last">
						<input class="formInput userEmail" type="text" name="uni_contact_email" value="" placeholder="<?php _e('E-mail', 'asana') ?>" data-parsley-required="true" data-parsley-trigger="change focusout submit" data-parsley-type="email">
					</div>
					<div class="clear"></div>
					<div class="form-row">
						<input class="formInput userSubject" type="text" name="uni_contact_subject" value="" placeholder="<?php _e('Subject', 'asana') ?>" data-parsley-required="true" data-parsley-trigger="change focusout submit">
					</div>
					<div class="form-row">
						<textarea class="formTextarea" name="uni_contact_msg" id="" cols="30" rows="10" placeholder="<?php _e('Message', 'asana') ?>" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>
					</div>
					<input id="uniSendContactForm" class="submitContactFormBtn uni_input_submit" type="button" value="<?php _e('Send', 'asana') ?>">
				</form>
			</div>
            <?php } ?>
    <?php } ?>
	</section>

    <?php
    endwhile; endif;
    ?>

<?php get_footer(); ?>