<?php
add_theme_support( 'woocommerce' );
// Enable featured image
add_theme_support( 'post-thumbnails');

// $content_width
if ( ! isset( $content_width ) ) {
	$content_width = 1170;
}

// Add default posts and comments RSS feed links to head
add_theme_support( 'automatic-feed-links' );

// Styles the visual editor with editor-style.css to match the theme style
add_editor_style();

// Load theme languages
load_theme_textdomain( 'asana', get_template_directory().'/languages' );

// Option tree theme options
add_filter( 'ot_show_pages', '__return_false' );
add_filter( 'ot_theme_mode', '__return_true' );
require( trailingslashit( get_template_directory() ) . 'includes/theme-options.php' );
require( trailingslashit( get_template_directory() ) . 'option-tree/ot-loader.php' );
require( trailingslashit( get_template_directory() ) . 'includes/uni-metabox.php' );

//
add_filter( 'ot_radio_images', 'uni_color_schemes_radio_images', 10, 2 );
function uni_color_schemes_radio_images( $array, $field_id ) {
    if ( $field_id == 'uni_color_schemes' ) {
        $array = array(
            array(
                'value'   => 'default',
                'label'   => __( 'Default scheme', 'asana' ),
                'src'     => get_template_directory_uri() . '/images/schemes/default.jpg'
            ),
            array(
                'value'   => 'purple',
                'label'   => __( 'Purple colour scheme', 'asana' ),
                'src'     => get_template_directory_uri() . '/images/schemes/purple.jpg'
            ),
            array(
                'value'   => 'blue',
                'label'   => __( 'Blue colour scheme', 'asana' ),
                'src'     => get_template_directory_uri() . '/images/schemes/blue.jpg'
            ),
            array(
                'value'   => 'red',
                'label'   => __( 'Red colour scheme', 'asana' ),
                'src'     => get_template_directory_uri() . '/images/schemes/red.jpg'
            ),
            array(
                'value'   => 'orange',
                'label'   => __( 'Orange colour scheme', 'asana' ),
                'src'     => get_template_directory_uri() . '/images/schemes/orange.jpg'
            ),
        );
    }
    return $array;
}

// Mailchimp API 3.0
include(trailingslashit( get_template_directory() ) . 'includes/class-uni-mailchimp-universal.php');

// Register Custom Menu Function
if (function_exists('register_nav_menus')) {
		register_nav_menus( array(
			'primary' => ( 'Asana Main Menu' ),
            'footer' => ( 'Asana Footer Menu' )
		) );
}

// Menu fallback
function uni_nav_fallback() {
    $sOutput = '<nav class="mainMenu"><ul class="clear">';
    $sOutput .= wp_list_pages( array('title_li' => '', 'echo' => false) );
    $sOutput .= '</ul></nav>';
    echo $sOutput;
}

// Menu footer fallback
function uni_nav_footer_fallback() {
    $sOutput = '';
    echo $sOutput;
}

// wp-title
function uni_wp_title( $sTitle, $sSeparator ) {
	global $paged, $page;

	if ( is_feed() )
		return $sTitle;

    $sSeparator = '&raquo;';

	// Add the site name.
	$sTitle = $sTitle . " $sSeparator " . get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$sSiteDesc = get_bloginfo( 'description' );
	if ( $sSiteDesc && ( is_home() || is_front_page() ) ) {
		$sTitle = get_bloginfo( 'name' )." $sSeparator $sSiteDesc";
    }

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$sTitle = "$sTitle $sSeparator " . sprintf( __( 'Page %s', 'asana' ), max( $paged, $page ) );

	return $sTitle;
}
add_filter( 'wp_title', 'uni_wp_title', 10, 2 );

// Add html5 suppost for search form and comments list
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

// TGM class 2.5.0 - neccessary plugins
include("includes/class-tgm-plugin-activation.php");

add_action( 'tgmpa_register', 'my_theme_register_required_plugins' );
function my_theme_register_required_plugins() {

    $plugins = array(
        array(
            'name'      => 'Instagram Feed',
            'slug'      => 'instagram-feed',
            'required'  => true,
        ),
		array(
			'name'               => 'Envato WordPress Toolkit',
			'slug'               => 'envato-wordpress-toolkit',
			'source'             => get_template_directory() . '/includes/plugins/envato-wordpress-toolkit.zip',
			'required'           => false,
			'version'            => '1.7.3',
			'force_activation'   => false,
			'force_deactivation' => false,
            'external_url'       => 'https://github.com/envato/envato-wordpress-toolkit'
		),
		array(
			'name'               => 'Uni Custom Post Types and Taxonomies',
			'slug'               => 'uni-cpt-and-tax',
			'source'             => get_template_directory() . '/includes/plugins/uni-cpt-and-tax.zip',
			'required'           => true,
			'version'            => '1.2.5',
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url'       => '',
		),
		array(
			'name'               => 'Uni Events Calendars Manager',
			'slug'               => 'uni-events-calendar',
			'source'             => get_template_directory() . '/includes/plugins/uni-events-calendar.zip',
			'required'           => true,
			'version'            => '1.0.7',
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url'       => '',
		),
		array(
			'name'               => 'Uni User Avatar',
			'slug'               => 'uni-user-avatar',
			'source'             => get_template_directory() . '/includes/plugins/uni-user-avatar.zip',
			'required'           => true,
			'version'            => '1.6.2',
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url'       => 'http://codecanyon.net/item/uni-avatar-wp-avatar-manager-plugin/10751977',
		),
		array(
			'name'               => 'Uni Woo Wish & Bridal Lists',
			'slug'               => 'uni-woo-wishlist',
			'source'             => get_template_directory() . '/includes/plugins/uni-woo-wishlist.zip',
			'required'           => false,
			'version'            => '1.1.0',
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url'       => '',
		),
		array(
			'name'               => 'Revolution Slider',
			'slug'               => 'revslider',
			'source'             => get_template_directory() . '/includes/plugins/revslider.zip',
			'required'           => true,
			'version'            => '5.0.4.1',
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url'       => 'http://themeforest.net/item/slider-revolution-responsive-wordpress-plugin/2751380',
		),
        array(
            'name'      => 'Contact Form 7',
            'slug'      => 'contact-form-7',
            'required'  => false,
        ),
        array(
            'name'      => 'Intuitive Custom Post Order',
            'slug'      => 'intuitive-custom-post-order',
            'required'  => false,
        ),
        array(
            'name'      => 'Shortcodes Ultimate',
            'slug'      => 'shortcodes-ultimate',
            'required'  => false,
        ),
		array(
			'name'               => 'Uni Woo Custom Product Options',
			'slug'               => 'uni-woo-custom-product-options',
			'source'             => '',
			'required'           => false,
			'version'            => '1.4.4',
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url'       => 'http://codecanyon.net/item/uni-cpo-price-calculation-formulas-for-woocommerce/9333768',
		),
    );

    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'default_path' => '',                      // Default absolute path to pre-packaged plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => __( 'Install Required Plugins', 'tgmpa' ),
            'menu_title'                      => __( 'Install Plugins', 'tgmpa' ),
            'installing'                      => __( 'Installing Plugin: %s', 'tgmpa' ), // %s = plugin name.
            'oops'                            => __( 'Something went wrong with the plugin API.', 'tgmpa' ),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
            'return'                          => __( 'Return to Required Plugins Installer', 'tgmpa' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'tgmpa' ),
            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'tgmpa' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );

    tgmpa( $plugins, $config );

}

// Load necessary theme scripts and styles
function uni_theme_scripts() {
    wp_enqueue_script('jquery');
    // cssua
    /*wp_register_script('jquery-cssua', get_template_directory_uri() . '/js/cssua.min.js', array('jquery'), '2.1.29' );
    wp_enqueue_script('jquery-cssua');
    */
    // bxSlider
    wp_register_script('jquery-bxslider', get_template_directory_uri() . '/js/jquery.bxslider.min.js', array('jquery'), '4.2.3' );
    wp_enqueue_script('jquery-bxslider');
    // jscrollpane
    wp_register_script('jquery-jscrollpane', get_template_directory_uri() . '/js/jquery.jscrollpane.min.js', array('jquery'), '4.2.3' );
    wp_enqueue_script('jquery-jscrollpane');
    // mousewheel
    wp_register_script('jquery-mousewheel', get_template_directory_uri() . '/js/jquery.mousewheel.js', array('jquery'), '4.2.3' );
    wp_enqueue_script('jquery-mousewheel');
    // dotdotdot
    wp_register_script('jquery-dotdotdot', get_template_directory_uri() . '/js/jquery.dotdotdot.min.js', array('jquery'), '4.2.3' );
    wp_enqueue_script('jquery-dotdotdot');
    // jquery.selectric.min
    wp_register_script('jquery-selectric', get_template_directory_uri() . '/js/jquery.selectric.min.js', array('jquery'), '1.8.7' );
    wp_enqueue_script('jquery-selectric');
    // jquery.infinitescroll
    wp_register_script('jquery-infinitescroll', get_template_directory_uri() . '/js/jquery.infinitescroll.min.js', array('jquery'), '2.1.0' );
    wp_enqueue_script('jquery-infinitescroll');
    // jquery.fancybox.pack
    wp_register_script('jquery-fancybox', get_template_directory_uri() . '/js/jquery.fancybox.pack.js', array('jquery'), '2.1.5' );
    wp_enqueue_script('jquery-fancybox');
    // jquery.blockUI
    wp_register_script('jquery-blockui', get_template_directory_uri() . '/js/jquery.blockUI.js', array('jquery'), '2.70.0' );
    wp_enqueue_script('jquery-blockui');
    // parsley localization
    $sLocale = get_locale();
    $aLocale = explode('_',$sLocale);
    $sLangCode = $aLocale[0];
    if ( !file_exists( get_template_directory() . '/js/parsley/i18n/'.$sLangCode.'.js' ) ) {
        $sLangCode = 'en';
    }
    wp_register_script('parsley-localization', get_template_directory_uri() . '/js/parsley/i18n/'.$sLangCode.'.js', array('jquery'), '2.1.3' );
    wp_enqueue_script('parsley-localization');
    // parsley
    wp_register_script('jquery-parsley', get_template_directory_uri() . '/js/parsley.min.js', array('jquery'), '2.1.3' );
    wp_enqueue_script('jquery-parsley');
    // Bauhaus scripts
    wp_register_script('uni-script', get_template_directory_uri() . '/js/script.js', array('jquery'), '1.4.1' );
    wp_enqueue_script('uni-script');
    // Google Maps API
    if ( is_page_template('templ-contact.php') || is_singular('uni_event') ) {
        wp_register_script('maps.googleapis', 'https://maps.googleapis.com/maps/api/js?sensor=true' );
        wp_enqueue_script('maps.googleapis');
    }

    if ( is_home() ) {
        $params = array(
            'site_url'      => home_url(),
		    'ajax_url' 		=> admin_url('admin-ajax.php'),
            'is_home'       => 'yes',
            'locale'        => $sLangCode,
            'lazy_load_on_products'  => ( ( ot_get_option('uni_ajax_scroll_enable_products') == 'on' ) ? true : false ),
            'lazy_load_on_posts'  => ( ( ot_get_option('uni_ajax_scroll_enable_posts') == 'on' ) ? true : false ),
            'lazy_load_on_events'  => ( ( ot_get_option('uni_ajax_scroll_enable_events') == 'on' ) ? true : false ),
            'lazy_load_end' => __( 'You have reached the end', 'asana' ),
            'lazy_loader'   => get_template_directory_uri().'/images/lazy_loader.png'
	    );
    } else {
        $params = array(
            'site_url'      => home_url(),
		    'ajax_url' 		=> admin_url('admin-ajax.php'),
            'is_home'       => 'no',
            'locale'        => $sLangCode,
            'lazy_load_on_products'  => ( ( ot_get_option('uni_ajax_scroll_enable_products') == 'on' ) ? true : false ),
            'lazy_load_on_posts'  => ( ( ot_get_option('uni_ajax_scroll_enable_posts') == 'on' ) ? true : false ),
            'lazy_load_on_events'  => ( ( ot_get_option('uni_ajax_scroll_enable_events') == 'on' ) ? true : false ),
            'lazy_load_end' => __( 'You have reached the end', 'asana' ),
            'lazy_loader'   => get_template_directory_uri().'/images/lazy_loader.png'
	    );
    }

	wp_localize_script( 'uni-script', 'unithemeparams', $params );

}
add_action('wp_enqueue_scripts', 'uni_theme_scripts');

// Enqueue style.css (default WordPress stylesheet)
function uni_theme_style() {

    wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' );

    wp_register_style( 'bxslider-style', get_template_directory_uri() . '/css/bxslider.css', '4.2.3' );
    wp_enqueue_style( 'bxslider-style');

    wp_register_style( 'ball-clip-rotate-style', get_template_directory_uri() . '/css/ball-clip-rotate.css', '0.1.0' );
    wp_enqueue_style( 'ball-clip-rotate-style');

    wp_register_style( 'fancybox-style', get_template_directory_uri() . '/css/fancybox.css', '2.1.5' );
    wp_enqueue_style( 'fancybox-style');

    wp_register_style( 'jscrollpane-style', get_template_directory_uri() . '/css/jscrollpane.css', '2.1.5' );
    wp_enqueue_style( 'jscrollpane-style');

    wp_register_style( 'selectric-style', get_template_directory_uri() . '/css/selectric.css', '2.1.5' );
    wp_enqueue_style( 'selectric-style' );

    wp_register_style( 'style', get_template_directory_uri() . '/style.css', array('bxslider-style', 'ball-clip-rotate-style', 'fancybox-style', 'jscrollpane-style', 'selectric-style'), '1.4.1', 'all' );
    wp_enqueue_style( 'style' );

    if ( !ot_get_option( 'uni_color_schemes' ) ) {
        wp_register_style( 'uni-theme-asana-scheme', get_template_directory_uri() . '/css/scheme-default.css', array('style'), '1.4.1', 'screen' );
        wp_enqueue_style( 'uni-theme-asana-scheme' );
    } else {
        $sColourScheme = ot_get_option( 'uni_color_schemes' );
        wp_register_style( 'uni-theme-asana-scheme', get_template_directory_uri() . '/css/scheme-'.$sColourScheme.'.css', array('style'), '1.4.1', 'screen' );
        wp_enqueue_style( 'uni-theme-asana-scheme' );
    }

    wp_register_style( 'adaptive', get_template_directory_uri() . '/css/adaptive.css', array('style'), '1.4.1', 'screen' );
    wp_enqueue_style( 'adaptive' );
}
add_action( 'wp_enqueue_scripts', 'uni_theme_style' );

//
add_action('admin_enqueue_scripts', 'uni_admin_script');
function uni_admin_script() {
    wp_enqueue_script( 'my-admin', get_template_directory_uri() . '/js/uni-admin.js', array('jquery'), '1.4.1' );
}

// Add new image sizes
add_image_size( 'unithumb-contactgallery', 570, 405, true );
add_image_size( 'unithumb-relativepost', 260, 174, true );
add_image_size( 'unithumb-eventpost', 502, 342, true );
add_image_size( 'unithumb-blog', 408, 272, true );
add_image_size( 'unithumb-homepostbig', 684, 684, true );
add_image_size( 'unithumb-homepostsmall', 342, 342, true );
add_image_size( 'unithumb-homeposthalf', 684, 342, true );
add_image_size( 'unithumb-minicartphoto', 52, 52, true );
add_image_size( 'unithumb-cartimage', 128, 128, true );
add_image_size( 'unithumb-attachment', 1170, 0, false );

// add the new role
add_action('after_switch_theme', 'uni_theme_activation_func', 10);
function uni_theme_activation_func() {
    add_role( 'instructor', __('Instructor', 'asana'), array('read' => true) );
	$instructor = get_role('instructor');
	$instructor->add_cap('read');
    $instructor->add_cap('edit_published_posts');
    $instructor->add_cap('upload_files');
    $instructor->add_cap('publish_posts');
    $instructor->add_cap('delete_published_posts');
    $instructor->add_cap('edit_posts');
    $instructor->add_cap('delete_posts');
    update_option('posts_per_page', 9);
    flush_rewrite_rules();
}

// remove the new role on theme deactivation
add_action('switch_theme', 'uni_theme_deactivation_func');
function uni_theme_deactivation_func() {
    remove_role( 'instructor' );
}

// Additional fields for user's profile
add_action( 'show_user_profile', 'uni_user_additional_fields_section' );
add_action( 'edit_user_profile', 'uni_user_additional_fields_section' );
function uni_user_additional_fields_section( $oUser ) {
    $aUserAdditionalData = ( get_user_meta( $oUser->ID, '_uni_user_data', true ) ) ? get_user_meta( $oUser->ID, '_uni_user_data', true ) : array();
    ?>

	<h3><?php _e( 'Asana: additional fields', 'asana' ); ?></h3>
	<table class="form-table">
		<tr>
			<th><label><?php _e( 'Profession', 'asana' ); ?></label></th>
			<td>
                <p><input type="text" class="regular-text" name="uni_user_data[profession]" value="<?php if ( !empty($aUserAdditionalData['profession']) ) echo esc_attr( $aUserAdditionalData['profession'] ) ?>" /></p>
            </td>
		</tr>
    </table>

	<h3><?php _e( 'Asana: social links', 'asana' ); ?></h3>
	<table class="form-table">
		<tr>
			<th><label><?php _e( 'Facebook', 'asana' ); ?></label></th>
			<td>
                <p><input type="text" class="regular-text" name="uni_user_data[fb_link]" value="<?php if ( !empty($aUserAdditionalData['fb_link']) ) echo esc_url( $aUserAdditionalData['fb_link'] ) ?>" /></p>
            </td>
		</tr>
		<tr>
			<th><label><?php _e( 'Twitter', 'asana' ); ?></label></th>
			<td>
                <p><input type="text" class="regular-text" name="uni_user_data[tw_link]" value="<?php if ( !empty($aUserAdditionalData['tw_link']) ) echo esc_url( $aUserAdditionalData['tw_link'] ) ?>" /></p>
            </td>
		</tr>
		<tr>
			<th><label><?php _e( 'Google+', 'asana' ); ?></label></th>
			<td>
                <p><input type="text" class="regular-text" name="uni_user_data[gplus_link]" value="<?php if ( !empty($aUserAdditionalData['gplus_link']) ) echo esc_url( $aUserAdditionalData['gplus_link'] ) ?>" /></p>
            </td>
		</tr>
		<tr>
			<th><label><?php _e( 'Pinterest', 'asana' ); ?></label></th>
			<td>
                <p><input type="text" class="regular-text" name="uni_user_data[pi_link]" value="<?php if ( !empty($aUserAdditionalData['pi_link']) ) echo esc_url( $aUserAdditionalData['pi_link'] ) ?>" /></p>
            </td>
		</tr>
    </table>

    <?php
}

// save
add_action( 'personal_options_update', 'uni_user_additional_fields_save' );
add_action( 'edit_user_profile_update', 'uni_user_additional_fields_save' );
function uni_user_additional_fields_save( $iUserId ) {

	if ( !current_user_can( 'edit_user', $iUserId ) )
		return false;

    if ( isset($_POST['uni_user_data']) && !empty($_POST['uni_user_data']) ) {
        update_user_meta($iUserId, '_uni_user_data', $_POST['uni_user_data']);
    } else {
        delete_user_meta($iUserId, '_uni_user_data');
    }

}


// Related by tags posts with thumb
function uni_relative_posts_by_tags() {

    global $post;
    $oOriginalPost = $post;
    $aTags = wp_get_post_tags( $post->ID );

    if ( isset($aTags) ) {
        $aRelativeTagArray = array();
        foreach($aTags as $oTag)
            $aRelativeTagArray[] = $oTag->term_id;

        $aRelatedArgs = array(
            'post_type' => 'post',
            'tag__in' => $aRelativeTagArray,
            'post__not_in' => array($post->ID),
            'posts_per_page' => 3,
            'orderby' => 'rand',
            'ignore_sticky_posts' => 1
        );

        $oRelatedQuery = new wp_query( $aRelatedArgs );
        if( $oRelatedQuery->have_posts() ) {

        echo '<div class="relatedPosts">
			    <div class="blockTitle">'.__('Related posts', 'asana').'</div>
			    <div class="blogPostWrap">';

        while( $oRelatedQuery->have_posts() ) {
        $oRelatedQuery->the_post();
        $sRelatedPlaceholderImage = get_template_directory_uri() . '/images/placeholders/unithumb-blog.jpg';
        ?>
				<div class="postItem">
					<a href="<?php the_permalink() ?>" class="postItemImg">
                        <?php if ( has_post_thumbnail() ) { ?>
                            <?php the_post_thumbnail( 'unithumb-blog', array( 'alt' => the_title_attribute('echo=0') ) ); ?>
                        <?php } else { ?>
                            <img src="<?php echo esc_url( $sRelatedPlaceholderImage ); ?>" width="408" height="272" alt="<?php the_title_attribute() ?>" />
                        <?php } ?>
					</a>
					<h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
					<?php uni_excerpt(15, '', true) ?>
				</div>
        <?php }
        echo '</div>
				</div>';
        }
        }
        $post = $oOriginalPost;
        wp_reset_postdata();
}

// post navigation
function uni_pagination($sPages = '', $sRange = 1) {
     $sShowItems = ($sRange * 2)+1;

     global $paged;
     if(empty($paged)) $paged = 1;

     if($sPages == '') {
         global $wp_query;
         $sPages = $wp_query->max_num_pages;
         if(!$sPages) {
             $sPages = 1;
         }
     }

     if( 1 != $sPages ) { ?>
				<ul>
                <?php //if ( $paged > 1 && $sShowItems < $sPages ) { ?>
					<li class="prevPage">
						<a href="<?php echo get_pagenum_link( 1 ); ?>" class="uni-page-previous">
							<i>
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="&#1057;&#1083;&#1086;&#1081;_1" x="0px" y="0px" width="7px" height="11px" viewBox="0 0 7 11" enable-background="new 0 0 7 11" xml:space="preserve">
									<path fill="#c3c3c3" class="paginationArrowIcon" d="M0.95 4.636L6.049 0L7 0.864L1.899 5.5L7 10.136L6.049 11L0 5.5L0.95 4.636z"/>
								</svg>
							</i> <?php _e('previous', 'asana'); ?>
						</a>
					</li>
                <?php //} ?>
                <?php
                if ($paged > 2 && $paged > $sRange+2 && $sShowItems < $sPages) {
                ?>
					<li class="threeDot">...</li>
				<?php
                }
                ?>
                <?php
                for ($i=1; $i <= $sPages; $i++) {
                    if (1 != $sPages && ( !($i >= $paged+$sRange+1 || $i <= $paged-$sRange-1) || $sPages <= $sShowItems ) ) {
                        echo ($paged == $i) ? '<li class="current">'.$i.'</li>' : '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
                    }
                }
                ?>
                <?php
                if ($paged < $sPages-3 &&  $paged+$sRange-3 < $sPages && $sShowItems < $sPages) {
                ?>
					<li class="threeDot">...</li>
				<?php
                }
                ?>
                <?php //if ($paged < $sPages && $sShowItems < $sPages) { ?>
					<li class="nextPage">
						<a href="<?php echo get_pagenum_link( $paged + 1 ); ?>" class="uni-page-next"><?php _e('next', 'asana') ?>
							<i>
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="&#1057;&#1083;&#1086;&#1081;_1" x="0px" y="0px" width="7px" height="11px" viewBox="0 0 7 11" enable-background="new 0 0 7 11" xml:space="preserve">
									<path fill="#c3c3c3" class="paginationArrowIcon" d="M6.05 6.364L0.951 11L0 10.136L5.102 5.5L0 0.864L0.951 0L7 5.5L6.05 6.364z"/>
								</svg>
							</i>
						</a>
					</li>
                <?php //} ?>
				</ul>
     <?php
     }
}

// custom excerpt
function uni_excerpt( $iLength, $iPostId = '', $bEcho = false, $sMore = null ) {
    if ( !empty($iPostId) ) {
        $oPost = get_post( $iPostId );
    } else {
        global $post;
        $oPost = $post;
    }

	if ( null === $sMore )
		$sMore = __( '&hellip;', 'asana' );

    $sContent = $oPost->post_content;
	$sContent = wp_strip_all_tags( $sContent );
    $sContent = strip_shortcodes( $sContent );
	if ( 'characters' == _x( 'words', 'word count: words or characters?', 'asana' ) && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
		$sContent = trim( preg_replace( "/[\n\r\t ]+/", ' ', $sContent ), ' ' );
		preg_match_all( '/./u', $sContent, $aWordsArray );
		$aWordsArray = array_slice( $aWordsArray[0], 0, $iLength + 1 );
		$sep = '';
	} else {
		$aWordsArray = preg_split( "/[\n\r\t ]+/", $sContent, $iLength + 1, PREG_SPLIT_NO_EMPTY );
		$sep = ' ';
	}

	if ( count( $aWordsArray ) > $iLength ) {
		array_pop( $aWordsArray );
		$sContent = implode( $sep, $aWordsArray );
        $sContent = $sContent . $sMore;
	} else {
		$sContent = implode( $sep, $aWordsArray );
	}
    if ( $bEcho ) {
        echo '<p>'.$sContent.'</p>';
    } else {
        return $sContent;
    }
}

function custom_excerpt_length( $length ) {
	return 10;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function new_excerpt_more( $more ) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

function uni_share_facebook() {
	return 'http://www.facebook.com/sharer.php?u='.urlencode(get_permalink()).'&t='.urlencode(get_the_title());
}

function uni_share_twitter() {
	return 'http://twitter.com/share?text='.urlencode(get_the_title()).'&url='.urlencode(get_permalink());
}

function uni_share_gplus() {
	return 'https://plus.google.com/share?url='.urlencode(get_permalink());
}

function uni_share_pinterest() {
    $aImage = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
    $sImageUrl = '';
	if ( isset($aImage[0]) ) $sImageUrl = $aImage[0];
	if ( $sImageUrl == false ) {
		$sImageUrl = get_template_directory_uri() . '/images/placeholders/unithumb-blog.jpg';
	}
	return 'http://pinterest.com/pin/create/button/?url='.urlencode( get_permalink() )
            .'&media='.urlencode($sImageUrl).'&description='.urlencode(get_the_title());
}

// comments
function uni_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
    global $post;
	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?>>
	<a id="view-comment-<?php comment_ID(); ?>" class="comment-anchor"></a>
	<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
		<footer class="comment-meta">
			<div class="comment-author vcard">
				<?php if (0 != $args['avatar_size']) echo get_avatar($comment, $args['avatar_size']); ?>
			</div><!-- .comment-author -->

			<div class="reply">
				<?php comment_reply_link(array_merge($args, array(
					'add_below' => 'div-comment',
					'depth' => $depth,
					'max_depth' => $args['max_depth'],
					'before' => '<div>',
					'after' => '</div>'
				))); ?>
			</div><!-- .reply -->
		</footer><!-- .comment-meta -->

		<div class="comment-wrapper">
			<?php
            if ( $comment->user_id === $post->post_author ) {
                printf('<cite class="fn">%s</cite><span class="uni-post-author">%s</span>', get_comment_author_link(), __('post author', 'asana'));
            } else {
                printf('<cite class="fn">%s</cite>', get_comment_author_link());
            }
            ?>

			<span class="comment-metadata">
				<a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
					<time datetime="<?php comment_time('c'); ?>">
						<?php printf(_x('%1$s at %2$s', '1: date, 2: time', 'asana'), get_comment_date(), get_comment_time()); ?>
					</time>
				</a>
				<?php edit_comment_link(__('Edit', 'asana'), '<span class="separator">&middot;</span> <span class="edit-link">', '</span>'); ?>
			</span><!-- .comment-metadata -->

			<?php if ('0' == $comment->comment_approved): ?>
				<p class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'asana'); ?></p>
			<?php endif; ?>

			<div class="comment-content">
				<?php comment_text(); ?>
			</div><!-- .comment-content -->
		</div>
	</article><!-- .comment-body -->
<?php
}

// popup messages
if ( !function_exists('uni_add_js_message_div') ) {
    function uni_add_js_message_div() {
        echo '<div id="uni_popup"></div>';
    }
    add_action('wp_footer', 'uni_add_js_message_div');
}

// Ajax contact form - processing
function uni_contact_form(){

        $aResult               = array();
        $aResult['message']    = __('Error!', 'asana');
        $aResult['status']     = 'error';

        $sCustomerName          = esc_sql($_POST['uni_contact_name']);
        $sCustomerEmail         = esc_sql($_POST['uni_contact_email']);
        $sCustomerSubject       = esc_sql($_POST['uni_contact_subject']);
        $sCustomerMsg           = esc_sql($_POST['uni_contact_msg']);
        $sNonce                 = $_POST['uni_contact_nonce'];
        $sAntiCheat             = $_POST['cheaters_always_disable_js'];

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || !wp_verify_nonce( $_POST['uni_contact_nonce'], 'uni_nonce' ) ) {
            wp_send_json( $aResult );
        }

        if ( $sCustomerName && $sCustomerEmail && $sCustomerSubject && $sCustomerMsg ) {

            $sToEmail 		    = ( ot_get_option( 'uni_email' ) ) ? ot_get_option( 'uni_email' ) : get_bloginfo('admin_email');
            $sFromEmail         = $sCustomerEmail;
            $sHeadersText       = "$sCustomerName <$sFromEmail>";
	        $sSubjectText 		= $sCustomerSubject;

            $sBlogName          = get_bloginfo('name');

            $sMessage           =
                    "<h3>".sprintf( __('You have a new message sent from "%s"!', 'asana'), $sBlogName )."</h3>
                    <p></p>
                    <p><strong>".__('Contact information', 'asana').":</strong><br>
                    ".sprintf( __('Name: %s', 'asana'), $sCustomerName )."
                    <br>
                    ".sprintf( __('Email: %s', 'asana'), $sCustomerEmail )."
                    <br>
                    ".__('Message', 'asana').":
                    <br>$sCustomerMsg
                    </p>";
            $sMessage = stripslashes_deep( $sMessage );

            uni_send_email_wrapper( $sToEmail, $sHeadersText, $sSubjectText, false, array(), $sMessage );

            $aResult['status']     = 'success';
            $aResult['message']    = __('Thanks! You message has been sent!', 'asana');

        } else {
            $aResult['message']    = __('All fields are required!', 'asana');
        }

	    wp_send_json( $aResult );
}
add_action('wp_ajax_uni_contact_form', 'uni_contact_form');
add_action('wp_ajax_nopriv_uni_contact_form', 'uni_contact_form');

// join event form - processing
function uni_join_event_form(){

	$sCharset = 'UTF-8';
	mb_internal_encoding($sCharset);

        $aResult               = array();
        $aResult['message']    = __('Error!', 'asana');
        $aResult['status']     = 'error';

        $sCustomerFName         = ( !empty($_POST['uni_contact_firstname']) ) ? esc_sql($_POST['uni_contact_firstname']) : '';
        $sCustomerLName         = ( !empty($_POST['uni_contact_lastname']) ) ? esc_sql($_POST['uni_contact_lastname']) : '';
        $sCustomerEmail         = ( !empty($_POST['uni_contact_email']) ) ? esc_sql($_POST['uni_contact_email']) : '';
        $sCustomerPhone         = ( !empty($_POST['uni_contact_phone']) ) ? esc_sql($_POST['uni_contact_phone']) : '';
        $sCustomerMsg           = ( !empty($_POST['uni_contact_msg']) ) ? stripslashes_deep( strip_tags( $_POST['uni_contact_msg'] ) ) : '';
        $iEventId               = absint( esc_sql($_POST['event_id']) );
        $sNonce                 = $_POST['uni_contact_nonce'];
        $sAntiCheat             = $_POST['cheaters_always_disable_js'];

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || !wp_verify_nonce( $_POST['uni_contact_nonce'], 'uni_nonce' ) ) {
            wp_send_json( $aResult );
        }

        if ( $sCustomerFName && $sCustomerLName && $sCustomerEmail && $sCustomerPhone && $sCustomerMsg ) {

            $sAdminEmail        = ( ot_get_option( 'uni_email' ) ) ? ot_get_option( 'uni_email' ) : get_bloginfo('admin_email');
            $sEventTitle        = esc_attr( get_the_title( $iEventId ) );
            $aCustomData        = get_post_custom( $iEventId );
            $sEventDate         = ( !empty($aCustomData['uni_event_date'][0]) ) ? esc_html( $aCustomData['uni_event_date'][0] ) : __('- not specified -', 'asana');
            $sEventTime         = ( !empty($aCustomData['uni_event_time'][0]) ) ? esc_html( $aCustomData['uni_event_time'][0] ) : __('- not specified -', 'asana');
            $sEventLocation     = ( !empty($aCustomData['uni_event_address'][0]) ) ? esc_html( $aCustomData['uni_event_address'][0] ) : __('- not specified -', 'asana');
            $sEventPrice        = ( !empty($aCustomData['uni_event_price'][0]) ) ? esc_html( $aCustomData['uni_event_price'][0] ) : __('- not specified -', 'asana');
            $sPhone             = ( ot_get_option( 'uni_phone' ) ) ? esc_html( ot_get_option( 'uni_phone' ) ) : '+88 (0) 101 0000 000';
            $sEmail             = ( ot_get_option( 'uni_email' ) ) ? esc_html( ot_get_option( 'uni_email' ) ) : esc_html( get_bloginfo('admin_email') );

            // send an email to the client
            $sBlogName          = get_bloginfo('name');
            $sHeadersText       = esc_attr($sBlogName)." "."<$sAdminEmail>";
            $sSubjectText 		= sprintf( __( 'Successful registration for "%s"', 'asana'), $sEventTitle );
            $sEmailTemplateName = apply_filters( 'uni_asana_event_email_filter', 'email/event-guest.php', 'guest' );
            $aMailVars = array( '$sEventTitle' => '"'.$sEventTitle.'"', '$sEventDate' => $sEventDate, '$sEventTime' => $sEventTime,
                                '$sEventLocation' => $sEventLocation, '$sEventPrice' => $sEventPrice, '$sPhone' => $sPhone, '$sEmail' => $sEmail );

            uni_send_email_wrapper( $sCustomerEmail, $sHeadersText, $sSubjectText, $sEmailTemplateName, $aMailVars, '' );

            // send an email to the admin
            $sHeadersText       = "$sCustomerFName $sCustomerLName <$sCustomerEmail>";
            $sSubjectText 		= sprintf( __( 'A new registration for "%s"', 'asana'), $sEventTitle );
            $sClientName        = $sCustomerFName.' '.$sCustomerLName;
            $sClientTel         = $sCustomerPhone;
            $sClientEmail       = $sCustomerEmail;
            $sClientMsg         = $sCustomerMsg;
            $sEmailTemplateName = apply_filters( 'uni_asana_event_email_filter', 'email/event-admin.php', 'admin' );
            $aMailVars = array( '$sEventTitle' => '"'.$sEventTitle.'"', '$sEventDate' => $sEventDate, '$sClientName' => $sClientName,
                                '$sClientTel' => $sClientTel, '$sClientEmail' => $sClientEmail, '$sClientMsg' => $sClientMsg );

            uni_send_email_wrapper( $sAdminEmail, $sHeadersText, $sSubjectText, $sEmailTemplateName, $aMailVars, '' );

            $aResult['status']     = 'success';
            $aResult['message']    = __('Thanks! You have successfully registered!', 'asana');

        } else {
            $aResult['message']    = __('All fields are required!', 'asana');
        }

	    wp_send_json( $aResult );
}
add_action('wp_ajax_uni_join_event_form', 'uni_join_event_form');
add_action('wp_ajax_nopriv_uni_join_event_form', 'uni_join_event_form');

// price form - processing
function uni_price_form(){

	$sCharset = 'UTF-8';
	mb_internal_encoding($sCharset);

        $aResult               = array();
        $aResult['message']    = __('Error!', 'asana');
        $aResult['status']     = 'error';

        $sCustomerFName         = ( !empty($_POST['uni_contact_firstname']) ) ? esc_sql($_POST['uni_contact_firstname']) : '';
        $sCustomerLName         = ( !empty($_POST['uni_contact_lastname']) ) ? esc_sql($_POST['uni_contact_lastname']) : '';
        $sCustomerEmail         = ( !empty($_POST['uni_contact_email']) ) ? esc_sql($_POST['uni_contact_email']) : '';
        $sCustomerPhone         = ( !empty($_POST['uni_contact_phone']) ) ? esc_sql($_POST['uni_contact_phone']) : '';
        $sCustomerMsg           = ( !empty($_POST['uni_contact_msg']) ) ? stripslashes_deep( strip_tags( $_POST['uni_contact_msg'] ) ) : '';
        $iPriceId               = absint( esc_sql($_POST['uni_price_id']) );
        $sNonce                 = $_POST['uni_contact_nonce'];
        $sAntiCheat             = $_POST['cheaters_always_disable_js'];

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || !wp_verify_nonce( $_POST['uni_contact_nonce'], 'uni_nonce' ) ) {
            wp_send_json( $aResult );
        }

        if ( $sCustomerFName && $sCustomerLName && $sCustomerEmail && $sCustomerPhone && $sCustomerMsg ) {

            $sAdminEmail        = ( ot_get_option( 'uni_email' ) ) ? ot_get_option( 'uni_email' ) : get_bloginfo('admin_email');
            $sPriceTitle        = esc_attr( get_the_title( $iPriceId ) );
            $aCustomData        = get_post_custom( $iPriceId );
            $sPriceCurrency     = ( !empty($aCustomData['uni_currency'][0]) ) ? esc_html( $aCustomData['uni_currency'][0] ) : __('- not specified -', 'asana');
            $sPriceVal          = ( !empty($aCustomData['uni_price_val'][0]) ) ? esc_html( $aCustomData['uni_price_val'][0] ) : __('- not specified -', 'asana');
            $sPricePeriod       = ( !empty($aCustomData['uni_period'][0]) ) ? esc_html( $aCustomData['uni_period'][0] ) : __('- not specified -', 'asana');
            $sPhone             = ( ot_get_option( 'uni_phone' ) ) ? esc_html( ot_get_option( 'uni_phone' ) ) : '+88 (0) 101 0000 000';
            $sEmail             = ( ot_get_option( 'uni_email' ) ) ? esc_html( ot_get_option( 'uni_email' ) ) : esc_html( get_bloginfo('admin_email') );

            // send an email to the client
            $sBlogName          = get_bloginfo('name');
            $sHeadersText       = esc_attr($sBlogName)." "."<$sAdminEmail>";
            $sSubjectText 		= sprintf( __( 'Successful request for "%s"', 'asana'), $sPriceTitle );
            $sEmailTemplateName = apply_filters( 'uni_asana_price_email_filter', 'email/price-guest.php', 'guest' );
            $aMailVars = array( '$sPriceTitle' => '"'.$sPriceTitle.'"', '$sPriceCurrency' => $sPriceCurrency, '$sPriceVal' => $sPriceVal,
                                '$sPricePeriod' => $sPricePeriod, '$sPhone' => $sPhone, '$sEmail' => $sEmail );

            uni_send_email_wrapper( $sCustomerEmail, $sHeadersText, $sSubjectText, $sEmailTemplateName, $aMailVars, '' );

            // send an email to the admin
            $sHeadersText       = "$sCustomerFName $sCustomerLName <$sCustomerEmail>";
            $sSubjectText 		= sprintf( __( 'A new request for "%s"', 'asana'), $sPriceTitle );
            $sClientName        = $sCustomerFName.' '.$sCustomerLName;
            $sClientTel         = $sCustomerPhone;
            $sClientEmail       = $sCustomerEmail;
            $sClientMsg         = $sCustomerMsg;
            $sEmailTemplateName = apply_filters( 'uni_asana_price_email_filter', 'email/price-admin.php', 'admin' );
            $aMailVars = array( '$sPriceTitle' => '"'.$sPriceTitle.'"', '$sPricePeriod' => $sPricePeriod, '$sClientName' => $sClientName,
                                '$sClientTel' => $sClientTel, '$sClientEmail' => $sClientEmail, '$sClientMsg' => $sClientMsg );

            uni_send_email_wrapper( $sAdminEmail, $sHeadersText, $sSubjectText, $sEmailTemplateName, $aMailVars, '' );

            $aResult['status']     = 'success';
            $aResult['message']    = __('Thanks! You request is successfully sent!', 'asana');

        } else {
            $aResult['message']    = __('All fields are required!', 'asana');
        }

	    wp_send_json( $aResult );
}
add_action('wp_ajax_uni_price_form', 'uni_price_form');
add_action('wp_ajax_nopriv_uni_price_form', 'uni_price_form');

// mailchimp subscribe user
function uni_mailchimp_subscribe_user(){

    $aResult                = array();
    $aResult['message']     = __('Error!', 'asana');
    $aResult['status']      = 'error';

    $sEmail                 = $_POST['uni_input_email'];
    $sAntiCheat             = $_POST['cheaters_always_disable_js'];

    if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
        wp_send_json( $aResult );
    }

    $sApiKey            = ot_get_option( 'uni_mailchimp_api_key' );
    $sListId            = ot_get_option( 'uni_mailchimp_list_id' );

    if ( !empty($sApiKey) && !empty($sListId) ) {

        $oUniMailchimp = Uni_Mailchimp_Universal( $sApiKey, $sListId );
        $aResponse = $oUniMailchimp->call_lists_members( 'lists/members', 'POST', array('email_address' => $sEmail, 'status' => 'pending') );

        if ( $aResponse['status'] == 'success' ) {
            $aResult['message']     = __('Success!', 'asana');
            $aResult['status']      = 'success';
        } else {
            $aResult['message']     = ( isset($aResponse['response']->detail) && !empty($aResponse['response']->detail) ) ? $aResponse['response']->detail : $aResponse['message'];
        }

    } else {
        $aResult['message']     = __('No API key and/or List ID is/are specified!', 'asana');
    }

    wp_send_json( $aResult );
}
add_action('wp_ajax_uni_mailchimp_subscribe_user', 'uni_mailchimp_subscribe_user');
add_action('wp_ajax_nopriv_uni_mailchimp_subscribe_user', 'uni_mailchimp_subscribe_user');

// body classes
function asana_body_classes( $classes ) {
    if ( class_exists('Woocommerce') ) {
        if ( is_cart() || is_checkout() ) {
            foreach ( $classes as $sKey => $sClass ) {
                if ( $sClass == 'page-template-default' ) {
                    unset($classes[$sKey]);
                }
            }
        }
    }
	return $classes;
}
add_filter( 'body_class', 'asana_body_classes' );

//
function uni_send_email_wrapper( $sEmailTo, $sEmailFrom, $sSubjectText, $sEmailTemplateName, $aMailVars = array(), $sEmailText = '' ) {

	    $sCharset = 'UTF-8';
	    mb_internal_encoding($sCharset);

	    $sSubject           = mb_convert_encoding($sSubjectText, $sCharset, 'auto');
	    $sSubject           = mb_encode_mimeheader($sSubjectText, $sCharset, 'B');
        $sHeaders 			= "From: $sEmailFrom\r\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";

        if ( $sEmailTemplateName != false ) {
            $sMailContent   = uni_get_email_content_html( $sEmailTemplateName, $aMailVars );
        } else {
            $sMailContent   = $sEmailText;
        }

        wp_mail($sEmailTo, $sSubject, $sMailContent, $sHeaders);

}

//
function uni_get_email_content_html( $sEmailTemplateName, $aMailVars = array() ) {
		ob_start();
		uni_get_template( $sEmailTemplateName );
		$sMailContent = ob_get_clean();
        if ( !empty($aMailVars) ) {
            foreach ( $aMailVars as $sVarName => $sVarValue ) {
                $sMailContent = str_replace($sVarName, $sVarValue, $sMailContent);
            }
        }
        return $sMailContent;
}

//
function uni_get_template( $sEmailTemplateName, $args = array() ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

    if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $sEmailTemplateName ) ) {
        $sTemplatePath = trailingslashit( get_stylesheet_directory() ) . $sEmailTemplateName;
    } else if ( file_exists( trailingslashit( get_template_directory() ) . $sEmailTemplateName ) ) {
        $sTemplatePath = trailingslashit( get_template_directory() ) . $sEmailTemplateName;
    } else {
		return;
	}

	include( $sTemplatePath );
}

//*********** WooCommerce ****************************************************************
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 5);
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 15);
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
//remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

add_action( 'woocommerce_before_main_content', 'uni_woo_output_content_wrapper', 10);
//add_action( 'woocommerce_before_main_content', 'uni_woo_search_form', 20);
//add_action( 'woocommerce_before_main_content', 'uni_woocommerce_breadcrumb', 30);
add_action( 'woocommerce_after_main_content', 'uni_woo_output_content_wrapper_end', 10);

// Display 12 products per page.
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ), 20 );

add_filter( 'woocommerce_currency_symbol', 'uni_currency_symbol_markup', 2, 10 );
function uni_currency_symbol_markup($sCurrencySymbol, $currency) {
    return '<span>'.$sCurrencySymbol.'</span>';
}

function uni_woo_output_content_wrapper() {
    // if this is single product page
    if ( is_singular('product') ) {
        $iShopAttachId  = ( ot_get_option( 'uni_shop_header_bg' ) ) ? ot_get_option( 'uni_shop_header_bg' ) : '';
        $sTitleColor    = ( ot_get_option( 'uni_shop_header_title_color' ) ) ? ot_get_option( 'uni_shop_header_title_color' ) : '#ffffff';
        if ( !empty($iShopAttachId) ) {
            $aPageHeaderImage = wp_get_attachment_image_src( $iShopAttachId, 'full' );
            $sPageHeaderImage = $aPageHeaderImage[0];
        } else {
            $sPageHeaderImage = get_template_directory_uri().'/images/placeholders/pageheader-shop.jpg';
        }
        $sOutput = '<section class="container">
		    <div class="pageHeader" style="background-image: url('.esc_url( $sPageHeaderImage ).');">';
		    if ( ot_get_option( 'uni_shop_header_title' ) ) {
                $sOutput .= '<h1>'.ot_get_option( 'uni_shop_header_title' ).'</h1>';
            } else {
			    $sOutput .= '<h1>'.__('ONLINE BOUTIQUE', 'asana').'</h1>';
            }
	    $sOutput .= '</div>';
        $sOutput .= '<style>.pageHeader h1 {color:'.$sTitleColor.';}</style>';
		$sOutput .= '<div class="contentWrap">';
    // other pages
    } else {
        $iShopAttachId  = ( ot_get_option( 'uni_shop_header_bg' ) ) ? ot_get_option( 'uni_shop_header_bg' ) : '';
        $sTitleColor    = ( ot_get_option( 'uni_shop_header_title_color' ) ) ? ot_get_option( 'uni_shop_header_title_color' ) : '#ffffff';
        if ( !empty($iShopAttachId) ) {
            $aPageHeaderImage = wp_get_attachment_image_src( $iShopAttachId, 'full' );
            $sPageHeaderImage = $aPageHeaderImage[0];
        } else {
            $sPageHeaderImage = get_template_directory_uri().'/images/placeholders/pageheader-shop.jpg';
        }
        $sOutput = '<section class="container">
		    <div class="pageHeader" style="background-image: url('.esc_url( $sPageHeaderImage ).');">';
            if ( is_shop() ) {
		        if ( ot_get_option( 'uni_shop_header_title' ) ) {
                    $sOutput .= '<h1>'.ot_get_option( 'uni_shop_header_title' ).'</h1>';
                } else {
			        $sOutput .= '<h1>'.__('ONLINE BOUTIQUE', 'asana').'</h1>';
                }
            } else {
                $sOutput .= '<h1 class="page-title">'.woocommerce_page_title( false ).'</h1>';
            }
	    $sOutput .= '</div>';
        $sOutput .= '<style>.pageHeader h1 {color:'.$sTitleColor.';}</style>';
		$sOutput .= '<div class="contentWrap">';
    }

    echo $sOutput;
}

function uni_woo_output_content_wrapper_end() {
  echo '<div class="overlay"></div>
		</div>
	</section>';
}

//
function uni_woo_get_formatted_price( $sPrice ) {

	$num_decimals    = absint( get_option( 'woocommerce_price_num_decimals' ) );
	$currency        = isset( $args['currency'] ) ? $args['currency'] : '';
	$currency_symbol = get_woocommerce_currency_symbol($currency);
	$decimal_sep     = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), ENT_QUOTES );
	$thousands_sep   = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ), ENT_QUOTES );

	$price           = apply_filters( 'raw_woocommerce_price', floatval( $sPrice ) );
	$price           = apply_filters( 'formatted_woocommerce_price', number_format( $price, $num_decimals, $decimal_sep, $thousands_sep ), $price, $num_decimals, $decimal_sep, $thousands_sep );
    return sprintf( get_woocommerce_price_format(), $currency_symbol, $price );
}

//
function uni_woo_get_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0, $iPostId ) {
         if ( has_post_thumbnail($iPostId) )
             return get_the_post_thumbnail( $iPostId, $size );
         elseif ( wc_placeholder_img_src() )
             return uni_woo_placeholder_img( $placeholder_width, $placeholder_height );
}

//
function uni_woo_placeholder_img( $sWidth = 100, $sHeight = 100 ) {
     return '<img src="' . wc_placeholder_img_src() . '" alt="' . __( 'Placeholder', 'woocommerce' ) . '" width="' . esc_attr( $sWidth ) . '" class="woocommerce-placeholder wp-post-image" height="' . esc_attr( $sHeight ) . '" />';
}

function uni_minicart_content() {
    global $woocommerce;

    ?>
			<div class="miniCartPopup">
				<div class="miniCartPopupHead">
					<h3><?php _e('YOUR CART', 'asana'); ?></h3>
					<span class="closeCartPopup"></span>
				</div>

                <?php if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) : ?>
                    <div class="miniCartItemWrap">
                <?php
                    $sTotal = '';
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id     = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
					    $product_name   = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
  					    $thumbnail      = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                        $product_price = $cart_item['data']->price * $cart_item['quantity'];
                        $sTotal += $product_price;
                ?>
    					<div class="miniCartItem" data-product_id="<?php echo $product_id ?>"<?php if ( !empty($cart_item['_uni_cpo_cart_item_id']) ) { ?> data-cart_item_id="<?php echo esc_attr( $cart_item['_uni_cpo_cart_item_id'] ) ?>"<?php } ?>>
    						<a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>" class="miniCartItemImg">
    							<?php echo uni_woo_get_product_thumbnail('unithumb-minicartphoto', 52, 52, $product_id) ?>
    						</a>
    						<h3>
    							<a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>"><?php echo $product_name; ?></a>
    						</h3>
    						<p class="price"><?php echo uni_woo_get_formatted_price( $product_price ); ?></p>
    						<div class="quantity clear">
    							<span><?php _e('Quantity', 'asana'); ?></span>
    							<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s', $cart_item['quantity'] ) . '</span>', $cart_item, $cart_item_key ); ?>
    						</div>
                            <?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove removeMiniCartItem" title="%s"></a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key ); ?>
                            <?php echo WC()->cart->get_item_data( $cart_item ); ?>
    					</div>
                        <?php } ?>
    					<div class="miniCartSubTotal">
    						<?php _e( 'Subtotal', 'woocommerce' ); ?>
    						<span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
    					</div>
    					<a href="<?php echo WC()->cart->get_cart_url(); ?>" class="btnViewCart"><?php _e('view cart', 'asana'); ?></a>
    					<a href="<?php echo WC()->cart->get_checkout_url(); ?>" class="btnCheckout"><?php _e('checkout', 'asana'); ?></a>
                    
                <?php else: ?>
                    <div class="miniCartEmpty">
                        <i></i>
                        <p><?php _e( 'Your cart is empty', 'woocommerce' ); ?></p>
                    </div>
                <?php endif; ?>
				</div>
			</div>
    <?php
}

// Related products by product tags with thumb
function uni_relative_products_by_tags() {

    global $post;
    $oOriginalPost = $post;
    $aTags = wp_get_object_terms( $post->ID, 'product_tag' );

    if ( isset($aTags) ) {
        $aRelativeTagArray = array();
        foreach($aTags as $oTag)
            $aRelativeTagArray[] = $oTag->term_id;

        $aRelatedArgs = array(
            'post_type' => 'product',
            'tax_query' => array(
		        array(
			        'taxonomy' => 'product_tag',
			        'field'    => 'id',
			        'terms'    => $aRelativeTagArray,
		        ),
	        ),
            'post__not_in' => array($post->ID),
            'posts_per_page' => 5,
            'orderby' => 'rand',
            'ignore_sticky_posts' => 1
        );

        $oRelatedQuery = new wp_query( $aRelatedArgs );

        if( $oRelatedQuery->have_posts() ) {

        echo '<div class="relatedProducts">
				<div class="blockTitle">'.__('Related products', 'asana').'</div>
				<div class="shopItems">
					<ul class="shopItemsWrap">';

        while($oRelatedQuery->have_posts()) : $oRelatedQuery->the_post();

            wc_get_template_part( 'content', 'product' );

        endwhile;

                echo '</ul>
				</div>
			</div>';

        }

        }
        $post = $oOriginalPost;
        wp_reset_postdata();
}

// wishlist filters
add_filter( 'uni_wc_wishlist_table_image_size', 'uni_asana_wishlist_table_image_size', 10, 1 );
function uni_asana_wishlist_table_image_size( $sImageSize ) {
    return 'unithumb-cartimage';
}

function uni_order_by_rating_post_clauses( $args ) {

                global $wpdb;

                $args['where'] .= " AND $wpdb->commentmeta.meta_key = 'rating' ";

                $args['join'] = "
                        LEFT JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
                        LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
                ";

                $args['orderby'] = "$wpdb->commentmeta.meta_value DESC";

                $args['groupby'] = "$wpdb->posts.ID";

                return $args;
}

//
header("X-XSS-Protection: 0");
?>