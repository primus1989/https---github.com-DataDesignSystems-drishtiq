<?php
/*
Plugin Name: Uni User Avatar
Plugin URI: http://uni-theme.net
Description: Uni User Avatar plugin
Version: 1.6.2
Author: MooMoo Web Studio
Author URI: http://moomoo.com.ua
License: GPL2 or later
*/

	/**
	*  Constants
    */
    if ( !defined( 'UNI_AVATAR_WP_PLUGIN_PATH' ) ) define( 'UNI_AVATAR_WP_PLUGIN_PATH', plugin_dir_path(__FILE__) );
    if ( !defined( 'UNI_AVATAR_WP_PLUGIN_URL' ) )  define( 'UNI_AVATAR_WP_PLUGIN_URL', plugin_dir_url(__FILE__) );
    if ( !defined( 'UNI_AVATAR_OPTIONS' ) )  define( 'UNI_AVATAR_OPTIONS', 'uni_avatar' );
    if ( !defined( 'UNI_AVATAR_VERSION' ) )  define( 'UNI_AVATAR_VERSION', '1.6.2' );
    if ( !defined( 'UNI_AVATAR_DEFAULT_DIMM' ) )  define( 'UNI_AVATAR_DEFAULT_DIMM', 48 );
    if ( !defined( 'UNI_AVATAR_DEFAULT_SIZE' ) )  define( 'UNI_AVATAR_DEFAULT_SIZE', 300 );

    // Credits:
    // Default avatar icon author: Mahm0udWally
    // Predefined avatar icons:
    // <a href='http://www.freepik.com/free-vector/man-and-woman-avatars_766415.htm'>Designed by Freepik</a>
    // <a href='http://www.freepik.com/free-vector/vintage-avatars-collection_759915.htm'>Designed by Freepik</a>
    // <a href='http://www.aha-soft.com/'>Designed by Aha Soft</a>
    // <a href='http://www.freepik.com/free-vector/people-avatars_761436.htm'>Designed by Freepik</a>

	if ( !file_exists(ABSPATH.'wp-content/uploads/uni-avatars')) {
			wp_mkdir_p(ABSPATH.'wp-content/uploads/uni-avatars');
			if (!file_exists(ABSPATH.'wp-content/uploads/uni-avatars/index.php')) {
				file_put_contents(ABSPATH.'wp-content/uploads/uni-avatars/index.php', 'Silence is the gold!');
			}
	}
	if ( !file_exists(ABSPATH.'wp-content/uploads/bfi_thumb')) {
			wp_mkdir_p(ABSPATH.'wp-content/uploads/bfi_thumb');
	}

	/**
	*  Multilanguage support
    */
    add_action('plugins_loaded', 'uni_avatar_i18n');
    function uni_avatar_i18n() {
        load_plugin_textdomain( 'uni-avatar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
    }

    //Load required resources
    require_once UNI_AVATAR_WP_PLUGIN_PATH."class/UniAvatar.class.php";
    require_once UNI_AVATAR_WP_PLUGIN_PATH."class/BFI_Thumb.php";

//Init hooks
add_action( 'init', 'uni_user_avatar_init', 9 );
function uni_user_avatar_init() {
    global $UniAvatar;
    $UniAvatar = new UniAvatar();
    $UniAvatar->__construst();

    // plugin options page
    add_action('admin_menu', 'uni_user_avatar_create_menu');
}

//z-index fix for thickbox
add_action( 'wp_head', 'uni_user_avatar_thickbox_z_index_fix' );
function uni_user_avatar_thickbox_z_index_fix(){
    ?>
    <style type="text/css">
        #TB_window, #TB_overlay {z-index:9999!important;}
    </style>
    <?php
}

//
function uni_user_avatar_create_menu() {
	add_users_page(__('Uni Avatar', 'uni-avatar'), __('Uni Avatar', 'uni-avatar'), 'manage_options', 'uni-user-avatar-options', 'uni_user_avatar_plugin_function');
	add_action( 'admin_init', 'uni_user_avatar_register_settings' );
}

//
function uni_user_avatar_register_settings() {
    register_setting( 'uni-avatar-settings-group', 'uni_avatar_enable_gravatar_override' );
    register_setting( 'uni-avatar-settings-group', 'uni_upload_form_in_front' );
    register_setting( 'uni-avatar-settings-group', 'uni_avatar_default_dimm', 'uni_avatar_options_validate' );
    register_setting( 'uni-avatar-settings-group', 'uni_avatar_default_size', 'uni_avatar_options_validate' );
	register_setting( 'uni-avatar-settings-group', 'uni_avatar_gender_mode' );
    register_setting( 'uni-avatar-settings-group', 'uni_avatar_default_avatar_image' );
    register_setting( 'uni-avatar-settings-group', 'uni_avatar_predefined_avatars' );
    register_setting( 'uni-avatar-settings-group', 'uni_avatar_custom_avatars' );

    register_setting( 'uni-avatar-settings-group', 'uni_avatar_enable_buddypress_avatars_override' );
    register_setting( 'uni-avatar-settings-group', 'uni_avatar_enable_buddypress_form_override' );
    register_setting( 'uni-avatar-settings-group', 'uni_avatar_enable_buddypress_form_text_above' );
}

//
function uni_avatar_options_validate( $sVal ){
    $iVal = absint( trim( $sVal ) );
    return $iVal;
}

//
function uni_user_avatar_plugin_function() {
?>
<div class="wrap">
<h2><?php _e('Uni Avatar Plugin Options Page', 'uni-avatar') ?></h2>

<form method="post" action="options.php">
    <?php settings_fields( 'uni-avatar-settings-group' ); ?>
    <?php do_settings_sections( 'uni-avatar-settings-group' ); ?>

    <h3><?php _e('General options', 'uni-avatar') ?></h3>

    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <?php _e('Enable custom avatars everywhere on the website?', 'uni-avatar') ?>
            </th>
            <td>
                <input type="checkbox" name="uni_avatar_enable_gravatar_override" value="1"<?php echo checked( get_option('uni_avatar_enable_gravatar_override'), 1 ); ?> />
                <p class="description"><?php _e('Check this option to enable custom avatars everywhere on the website and gravatars will be replaced. Or uncheck this and you will be able to use custom avatars only by using plugin\'s shortcode [uav-display-avatar].', 'uni-avatar') ?></p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <?php _e('Enable support of the plugin\'s upload form on the website?', 'uni-avatar') ?>
            </th>
            <td>
                <input type="checkbox" name="uni_upload_form_in_front" value="1"<?php echo checked( get_option('uni_upload_form_in_front'), 1 ); ?> />
                <p class="description"><?php _e('It is a very important option! You have to enable this only if you want to use an upload avatar form someshere on your website in front end. This option is disabled by default to prevent of loading some not needed scripts (they are really not needed if you don\'t use plugin\'s form for uploading avatars in the front end)', 'uni-avatar') ?></p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Choose default avatar image dimmensions. Default is 48 px.', 'uni-avatar') ?></th>
            <td>
                <div style="display:block;float:left;margin-right:20px;">
                        <input type="text" name="uni_avatar_default_dimm" value="<?php echo ( get_option('uni_avatar_default_dimm') ) ? get_option('uni_avatar_default_dimm') : UNI_AVATAR_DEFAULT_DIMM; ?>" />
                </div>
                <div style="clear:both;"></div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Choose default avatar image size (Kb). Default is 300 Kb.', 'uni-avatar') ?></th>
            <td>
                <div style="display:block;float:left;margin-right:20px;">
                        <input type="text" name="uni_avatar_default_size" value="<?php echo ( get_option('uni_avatar_default_size') ) ? get_option('uni_avatar_default_size') : UNI_AVATAR_DEFAULT_SIZE; ?>" />
                </div>
                <div style="clear:both;"></div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Choose default avatar image', 'uni-avatar') ?></th>
            <td>
            <?php
            $aDefaultAvatars = uni_avatar_default_avatars_array();
            foreach ( $aDefaultAvatars as $sSlug => $sTitle ) {
            ?>
                <div style="display:block;float:left;margin-right:20px;">
                    <img style="display:block;margin:0;border:1px solid grey;" src="<?php echo UNI_AVATAR_WP_PLUGIN_URL.'css/images/user-avatar-default_'.$sSlug.'.png'; ?>" width="150" height="150" />
                    <label>
                        <input type="radio" name="uni_avatar_default_avatar_image" value="<?php echo $sSlug; ?>"<?php echo checked( get_option('uni_avatar_default_avatar_image'), $sSlug ); ?> />
                        <?php echo $sTitle; ?>
                    </label>
                </div>
            <?php } ?>
                <div style="clear:both;"></div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <?php _e('Enable different avatars depending on user\'s gender', 'uni-avatar') ?>
            </th>
            <td>
                <input type="checkbox" name="uni_avatar_gender_mode" value="1"<?php echo checked( get_option('uni_avatar_gender_mode'), 1 ); ?> />
                <p class="description"><?php _e('WP itself doesn\'t have feature of choosing users\'s gender! However this plugin adds this feature. User\'s gender can be chosen on his profile page.', 'uni-avatar') ?></p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e('Choose one of the predefined avatar image sets with male/female differentiation (displays if male/female differentiation is enabled)', 'uni-avatar') ?></th>
            <td>
            <?php
            $aPredefinedAvatars = uni_avatar_predefined_avatars_array();
            foreach ( $aPredefinedAvatars as $sSlug => $sTitle ) {
            ?>
                <div style="display:block;float:left;margin-right:20px;">
                    <img style="margin:0;border:1px solid grey;" src="<?php echo UNI_AVATAR_WP_PLUGIN_URL.'css/images/user-avatar-male-'.$sSlug.'.png'; ?>" width="150" height="150" />
                    <img style="margin:0;border:1px solid grey;" src="<?php echo UNI_AVATAR_WP_PLUGIN_URL.'css/images/user-avatar-female-'.$sSlug.'.png'; ?>" width="150" height="150" />
                    <label style="display:block;">
                        <input type="radio" name="uni_avatar_predefined_avatars" value="<?php echo $sSlug; ?>"<?php echo checked( get_option('uni_avatar_predefined_avatars'), $sSlug ); ?> />
                        <?php echo $sTitle; ?>
                    </label>
                </div>
            <?php } ?>
                <div style="clear:both;"></div>
            </td>
        </tr>
        <tr valign="top" class="form-field">
        <th scope="row"><?php _e('Custom uploaded avatars', 'uni-avatar') ?></th>
            <td>
			<label><?php _e( 'Thumbnails', 'uni-avatar' ); ?></label><br><br>
            <div id="uni_avatar_thumb_container">
            <?php
            $sAvatarIds = get_option('uni_avatar_custom_avatars');
            if ( isset($sAvatarIds) && !empty($sAvatarIds) ) $aAvatarIds = explode(',', $sAvatarIds);
            if ( isset($aAvatarIds) && !empty($sAvatarIds) ) {
                foreach ( $aAvatarIds as $iImageId ) {
                    //$sAvatarImage = wp_get_attachment_thumb_url( $iImageId );
            ?>
			        <?php /*<div class="uni_avatar_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $sAvatarImage; ?>" width="100" height="100" /></div> */ ?>
                    <div class="uni_avatar_thumbnail" style="float:left;margin-right:10px;"><?php echo wp_get_attachment_image( $iImageId, 'uni-avatar-thumb' ); ?></div>
            <?php
                }
            }
            ?>
            </div>
            <div style="clear:both;"></div>
			<div style="line-height:60px;">
				<input type="hidden" id="uni_avatar_ids" name="uni_avatar_custom_avatars" value="<?php echo $sAvatarIds; ?>" />
				<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add avatars', 'uni-avatar' ); ?></button>
				<button type="button" class="remove_image_button button"><?php _e( 'Remove avatars', 'uni-avatar' ); ?></button>
			</div>
			<script type="text/javascript">
				 // Only show the "remove image" button when needed
				 if ( ! jQuery('#uni_avatar_ids').val() )
					 jQuery('.remove_image_button').hide();

				// Uploading files
				var file_frame;

				jQuery(document).on( 'click', '.upload_image_button', function( event ){

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php _e( 'Choose images', 'uni-avatar' ); ?>',
						button: {
							text: '<?php _e( 'Use images', 'uni-avatar' ); ?>',
						},
						multiple: true
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
					    selection = file_frame.state().get('selection');

                        if (!selection) {
                            return;
                        }
                        var aAttachments = [];
                        // iterate through selected elements
                        selection.each(function(attachment) {
                            //console.log( attachment );
                            jQuery('#uni_avatar_thumb_container').append('<div class="uni_avatar_thumbnail" style="float:left;margin-right:10px;"><img src="'+ attachment.attributes.url +'" width="100px" height="100px" /></div>');
                            aAttachments.push(attachment.id);
                        });

                        var sAttachments = aAttachments.toString();
                        //console.log( sAttachments );
                        jQuery('#uni_avatar_ids').val( sAttachments );
                        jQuery('.remove_image_button').show();

					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery(document).on( 'click', '.remove_image_button', function( event ){
					jQuery('#uni_avatar_thumb_container').empty();
					jQuery('#uni_avatar_ids').val('');
					jQuery('.remove_image_button').hide();
					return false;
				});
			</script>
			<div class="clear"></div>
            </td>
        </tr>
    </table>

    <h3><?php _e('Options for BuddyPress', 'uni-avatar') ?></h3>

    <?php if ( !function_exists('bp_is_active') ) { ?>
        <p><?php _e('It seems that you haven\'t activated BuddyPress yet!', 'uni-avatar') ?></p>
    <?php } ?>

    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <?php _e('Enable custom avatars for BuddyPress?', 'uni-avatar') ?>
            </th>
            <td>
                <input type="checkbox" name="uni_avatar_enable_buddypress_avatars_override" value="1"<?php echo checked( get_option('uni_avatar_enable_buddypress_avatars_override'), 1 ); ?> />
                <p class="description"><?php _e('Check this option to add support of this plugin to BuddyPress. Warning: enabling this option will disable photo upload form on BP user\'s profile page.', 'uni-avatar') ?></p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <?php _e('Enable avatar upload form on BP profile page?', 'uni-avatar') ?>
            </th>
            <td>
                <input type="checkbox" name="uni_avatar_enable_buddypress_form_override" value="1"<?php echo checked( get_option('uni_avatar_enable_buddypress_form_override'), 1 ); ?> />
                <p class="description"><?php _e('This option for those who enabled support of this plugin for BP and want to give their users an ability to change avatar any time.', 'uni-avatar') ?></p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <?php _e('Custom text above the upload form on "Change Avatar" profile tab', 'uni-avatar') ?>
            </th>
            <td>
                <textarea name="uni_avatar_enable_buddypress_form_text_above"><?php echo get_option('uni_avatar_enable_buddypress_form_text_above'); ?></textarea>
                <p class="description"><?php _e('You can add custom text above the upload form. It can be a decription or whatever you want.', 'uni-avatar') ?></p>
            </td>
        </tr>
    </table>

    <?php submit_button(); ?>

</form>
</div>
<?php
}

// *helper*
function uni_avatar_default_avatars_array(){
    $aArray = apply_filters( 'uni_avatar_default_avatars_filter',
                    array( 'white' => 'White', 'beige' => 'Beige', 'green' => 'Green',
                    'blue' => 'Blue', 'red' => 'Red', 'pink' => 'Pink' )
                    );
    return $aArray;
}

//
function uni_avatar_predefined_avatars_array(){
    $aArray = apply_filters( 'uni_avatar_predefined_avatars_filter',
            array(
            'default' => 'Default',
            'vintage' => 'Vintage',
            'cartoon-young' => 'Cartoon style (youth)',
            'cartoon-adult' => 'Cartoon style (adults)'
            )
        );
    return $aArray;
}

// *helper*
function uni_avatar_get_avatars_size( $sSize = null ){

    $iDefaultSize = get_option('uni_avatar_default_size');

    if ( isset($sSize) && !empty($sSize) ) {

            $iSize = absint( $sSize );

            if ( $iSize < 1 ) {
                $iSize = 1;
            } else if ( $iSize > 512 ) {
                $iSize = 512;
            }

    } else if ( isset( $iDefaultSize ) && !empty( $iDefaultSize ) ) {
        $iSize = $iDefaultSize;
    } else {
        $iSize = UNI_AVATAR_DEFAULT_SIZE;
    }

    return $iSize;
}

//
add_image_size( 'uni-avatar-thumb', 100, 100, true );

//
function uni_avatar_scripts() {
        wp_enqueue_script('jquery');
if ( get_option('uni_upload_form_in_front') ) {
        wp_enqueue_media();
}
if ( function_exists('bp_is_active') ) {
    if( bp_is_profile_component() && 'change-avatar' == bp_current_action() && get_option('uni_avatar_enable_buddypress_form_override') ) {
        wp_enqueue_media();
    }
}
if ( class_exists('bbPress') ) {
    if ( bbp_is_single_user_edit() ) {
        wp_enqueue_media();
    }
}
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-widget' );
        wp_enqueue_script('jcrop');
        wp_register_script('jquery.iframe-transport', UNI_AVATAR_WP_PLUGIN_URL . 'js/jquery.iframe-transport.js', array('jquery-ui-widget'), '1.8.3' );
        wp_enqueue_script('jquery.iframe-transport');
        wp_register_script('jquery.fileupload', UNI_AVATAR_WP_PLUGIN_URL . 'js/jquery.fileupload.js', array('jquery-ui-widget'), '5.42.3' );
        wp_enqueue_script('jquery.fileupload');
        wp_register_script('jquery.fileupload-process', UNI_AVATAR_WP_PLUGIN_URL . 'js/jquery.fileupload-process.js', array('jquery-ui-widget'), '5.42.3' );
        wp_enqueue_script('jquery.fileupload-process');
        wp_register_script('jquery.fileupload-validate', UNI_AVATAR_WP_PLUGIN_URL . 'js/jquery.fileupload-validate.js', array('jquery-ui-widget'), '5.42.3' );
        wp_enqueue_script('jquery.fileupload-validate');

        wp_register_script('uni-avatar-modal', UNI_AVATAR_WP_PLUGIN_URL . 'js/uni-avatar-modal.js', array('jquery.fileupload'), UNI_AVATAR_VERSION );
        wp_enqueue_script('uni-avatar-modal');

        $params = array(
            'site_url'      => get_bloginfo('url'),
		    'ajax_url' 		=> admin_url('admin-ajax.php'),
            'upload_form_in_front_on' => ( get_option('uni_upload_form_in_front') || ( function_exists('bp_is_active') && get_option('uni_avatar_enable_buddypress_form_override') && bp_is_profile_component() && 'change-avatar' == bp_current_action() ) || ( class_exists('bbPress') && bbp_is_single_user_edit() ) ) ? 1 : 0,
            'select_avatars_on' => ( get_option('uni_avatar_custom_avatars') ) ? 1 : 0,
            'uni_avatar_max' => ( ( get_option('uni_avatar_default_size') ) ? get_option('uni_avatar_default_size') * 1024 : UNI_AVATAR_DEFAULT_SIZE * 1024 ),
            'modal_upload_title' => __('Upload Avatar', 'uni-avatar' ),
            'modal_select_title' => __('Select Avatar', 'uni-avatar' ),
            'file_not_allowed' => __('File type not allowed', 'uni-avatar' ),
            'file_too_large' => __('File is too large', 'uni-avatar' )
	    );

	    wp_localize_script( 'uni-avatar-modal', 'uniavatarparams', $params );

        wp_enqueue_style( 'dashicons' );
        wp_enqueue_style( 'admin-css', UNI_AVATAR_WP_PLUGIN_URL . 'css/uniavatar-styles-front.css');

}
add_action('wp_enqueue_scripts', 'uni_avatar_scripts');

// scripts in admin area
function uni_avatar_admin_scripts($hook) {
        //print_r($hook);
        wp_enqueue_script('jquery');
        if ( $hook == 'plugins_page_uni-user-avatar-options' || $hook == 'user-edit.php' || $hook == 'profile.php' ) {
            wp_enqueue_media();
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-widget' );
            wp_enqueue_script('jcrop');
            wp_register_script('jquery.iframe-transport', UNI_AVATAR_WP_PLUGIN_URL . 'js/jquery.iframe-transport.js', array('jquery-ui-widget'), '1.8.2' );
            wp_enqueue_script('jquery.iframe-transport');
            wp_register_script('jquery.fileupload', UNI_AVATAR_WP_PLUGIN_URL . 'js/jquery.fileupload.js', array('jquery-ui-widget'), '5.42.0' );
            wp_enqueue_script('jquery.fileupload');
            wp_register_script('jquery.fileupload-process', UNI_AVATAR_WP_PLUGIN_URL . 'js/jquery.fileupload-process.js', array('jquery-ui-widget'), '5.42.3' );
            wp_enqueue_script('jquery.fileupload-process');
            wp_register_script('jquery.fileupload-validate', UNI_AVATAR_WP_PLUGIN_URL . 'js/jquery.fileupload-validate.js', array('jquery-ui-widget'), '5.42.3' );
            wp_enqueue_script('jquery.fileupload-validate');

            wp_register_script('uni-avatar-modal', UNI_AVATAR_WP_PLUGIN_URL . 'js/uni-avatar-modal.js', array('jquery.fileupload'), UNI_AVATAR_VERSION );
            wp_enqueue_script('uni-avatar-modal');

            $sCustomAvatarIds = get_option('uni_avatar_custom_avatars');

            $params = array(
                'site_url'      => get_bloginfo('url'),
		        'ajax_url' 		=> admin_url('admin-ajax.php'),
                'upload_form_in_front_on' => 1,
                'select_avatars_on' => ( isset($sCustomAvatarIds) && !empty($sCustomAvatarIds) ) ? 1 : 0,
                'uni_avatar_max' => ( ( get_option('uni_avatar_default_size') ) ? get_option('uni_avatar_default_size') * 1024 : UNI_AVATAR_DEFAULT_SIZE * 1024 ),
                'modal_upload_title' => __('Upload Avatar', 'uni-avatar' ),
                'modal_select_title' => __('Select Avatar', 'uni-avatar' ),
                'file_not_allowed' => __('File type not allowed', 'uni-avatar' ),
                'file_too_large' => __('File is too large', 'uni-avatar' )
	        );

	        wp_localize_script( 'uni-avatar-modal', 'uniavatarparams', $params );

            wp_enqueue_style( 'dashicons' );
            wp_enqueue_style( 'admin-css', UNI_AVATAR_WP_PLUGIN_URL . 'css/uniavatar-styles-admin.css');
        }
}
add_action( 'admin_enqueue_scripts', 'uni_avatar_admin_scripts' );

//
add_action( 'show_user_profile', 'uni_avatar_user_fields' );
add_action( 'edit_user_profile', 'uni_avatar_user_fields' );
function uni_avatar_user_fields( $user ) {
    $sGender = get_user_meta( $user->ID, '_uni_avatar_gender', true );
    ?>

	<h3><?php _e( 'Uni Avatar additional fields', 'uni-avatar' ); ?></h3>
	<table class="form-table">
		<tr>
			<th><label><?php _e( 'Choose your gender', 'uni-avatar' ); ?></label></th>
			<td>
                <select name="uni_avatar_gender">
                    <option value="0"<?php selected('', $sGender) ?>><?php _e( 'Not selected', 'uni-avatar' ); ?></option>
                    <option value="male"<?php selected('male', $sGender) ?>><?php _e( 'Male', 'uni-avatar' ); ?></option>
                    <option value="female"<?php selected('female', $sGender) ?>><?php _e( 'Female', 'uni-avatar' ); ?></option>
                </select>
            </td>
		</tr>
        <tr>
            <td><label><?php _e( 'Add/Remove avatar', 'uni-avatar' ) ?></label></td>
            <td>
            <?php if ( is_admin() ) { ?>
                <div class="uni-user-avatar-image-container"><?php echo uni_display_user_avatar( '', $user->ID, 150 ) ?></div>
                <?php uni_display_user_avatar_modal_link( $user->ID ); ?>
                <?php uni_display_user_avatar_delete_link( $user->ID ); ?>
            <?php } else { ?>
                <?php echo do_shortcode('[uav-display-avatar id="'.$user->ID.'" size="150"]'); ?>
                <?php $sTitle = __('Upload/Change Avatar'); echo do_shortcode('[uav-modal-link id="'.$user->ID.'" title="'.$sTitle.'"]'); ?>
                <br>
                <?php $sTitle = __('Delete'); echo do_shortcode('[uav-delete-link id="'.$user->ID.'" title="'.$sTitle.'"]'); ?>
            <?php } ?>
            </td>
        </tr>
    </table>

    <?php
}

//
add_action( 'personal_options_update', 'uni_avatar_user_fields_save' );
add_action( 'edit_user_profile_update', 'uni_avatar_user_fields_save' );
function uni_avatar_user_fields_save( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

    if ( isset($_POST['uni_avatar_gender']) && !empty($_POST['uni_avatar_gender']) ) {
        update_user_meta($user_id, '_uni_avatar_gender', $_POST['uni_avatar_gender']);
    } else {
        delete_user_meta($user_id, '_uni_avatar_gender');
    }

}

function avatar_manager_display_media_states( $media_states ) {
    global $post;

    $bIsImageAvatar = get_post_meta( $post->ID, '_uni_avatar_image', true);

    if ( !empty( $bIsImageAvatar ) )
        $media_states[] = __( 'Avatar Image', 'uni-avatar' );

    return apply_filters( 'uni_avatar_display_media_states_filter', $media_states );

}

add_filter( 'display_media_states', 'avatar_manager_display_media_states', 10, 1 );

//
function uni_display_user_avatar_modal_link( $iUserId = null, $sTitle = null ) {
    global $UniAvatar;
    $UniAvatar->display_user_avatar_modal_link( $iUserId, $sTitle );
}

//
function uni_display_user_avatar_modal_link_shortcode_func( $atts ) {
    $aAttsArray = shortcode_atts( array(
        'id' => '',
        'title' => '',
    ), $atts );

    return uni_display_user_avatar_modal_link( $aAttsArray['id'], $aAttsArray['title'] );
}
add_shortcode( 'uav-modal-link', 'uni_display_user_avatar_modal_link_shortcode_func' );

//
function uni_display_user_avatar_delete_link( $iUserId = null, $sTitle = null ) {
    global $UniAvatar;
    $UniAvatar->display_user_avatar_delete_link( $iUserId, $sTitle );
}

//
function uni_display_user_avatar_delete_link_shortcode_func( $atts ) {
    $aAttsArray = shortcode_atts( array(
        'id' => '',
        'title' => '',
    ), $atts );

    return uni_display_user_avatar_delete_link( $aAttsArray['id'], $aAttsArray['title'] );
}
add_shortcode( 'uav-delete-link', 'uni_display_user_avatar_delete_link_shortcode_func' );



//display user's avatar image
if ( get_option('uni_avatar_enable_gravatar_override') ) {
    add_filter('get_avatar', 'uni_display_user_avatar', 10, 6);
}
function uni_display_user_avatar( $avatar = '', $id_or_email, $iSize = null, $default = null, $alt = null, $args = null ) {
    global $UniAvatar;
    $sAvatar = $UniAvatar->get_user_avatar_image( $avatar, $id_or_email, $iSize, $default, $alt, $args );
    return apply_filters('uni_avatar_display_filter', $sAvatar, $id_or_email, $iSize, $default, $alt, $args );
}

//******** Support for BuddyPress **********
if ( function_exists('bp_is_active') ) {

//display user's avatar image in BuddyPress
if ( get_option('uni_avatar_enable_buddypress_avatars_override') ) {
    add_filter('bp_core_fetch_avatar', 'uni_display_user_avatar_buddy', 10, 9);
    add_filter('bp_core_fetch_avatar_url', 'uni_display_user_avatar_buddy_url', 10, 2);
}
function uni_display_user_avatar_buddy( $img, $params, $item_id, $avatar_dir, $html_css_id, $html_width, $html_height, $avatar_folder_url, $avatar_folder_dir ) {
    global $UniAvatar;
    $avatar         = '';
    $id_or_email    = $params['item_id'];
    $iSize          = intval($params['width']);
    $default        = null;
    $alt            = $params['alt'];
    $args           = null;
    $sAvatar = $UniAvatar->get_user_avatar_image( $avatar, $id_or_email, $iSize, $default, $alt, $args );
    return apply_filters('uni_avatar_display_filter', $sAvatar, $id_or_email, $iSize, $default, $alt, $args );
}
function uni_display_user_avatar_buddy_url( $gravatar, $params ) {
    global $UniAvatar;
    $id_or_email    = $params['item_id'];
    $iSize          = intval($params['width']);
    $sAvatarUrl = $UniAvatar->get_user_avatar_url( $id_or_email, $iSize );
    return $sAvatarUrl;
}

// display uni avatar upload form on bp profile page
if ( get_option('uni_avatar_enable_buddypress_avatars_override') && !get_option('uni_avatar_enable_buddypress_form_override') ) {
    add_action( 'bp_init', 'uni_avatar_bp_start' );
    add_action( 'wp', 'uni_avatar_bp_remove_nav_item' );
} else if ( get_option('uni_avatar_enable_buddypress_avatars_override') && get_option('uni_avatar_enable_buddypress_form_override') ) {
    add_action( 'bp_init', 'uni_avatar_bp_start' );
}
function uni_avatar_bp_remove_nav_item() {
    global $bp;
    bp_core_remove_subnav_item( $bp->profile->slug, 'change-avatar' );
}
function uni_avatar_register_template_location() {
    return UNI_AVATAR_WP_PLUGIN_PATH . '/templates/';
}
function uni_avatar_maybe_replace_template( $templates, $slug, $name ) {

    if( 'members/single/profile/change-avatar' != $slug )
        return $templates;

    return array( 'members/single/profile/uni-avatar-change-avatar.php' );

}
function uni_avatar_bp_start() {

    if( function_exists( 'bp_register_template_stack' ) )
        bp_register_template_stack( 'uni_avatar_register_template_location' );

    // if viewing a member page, overload the template
    if ( bp_is_user()  )
        add_filter( 'bp_get_template_part', 'uni_avatar_maybe_replace_template', 10, 3 );

}

}
//******** End of support for BuddyPress **********

//
function uni_display_user_avatar_shortcode_func( $atts ) {
    $aAttsArray = shortcode_atts( array(
        'id' => '',
        'size' => '',
        'alt' => ''
    ), $atts );

    return uni_display_user_avatar( '', $aAttsArray['id'], $aAttsArray['size'], '', $aAttsArray['alt'] );
}
add_shortcode( 'uav-display-avatar', 'uni_display_user_avatar_shortcode_func' );

//get user avatar image
function uni_get_user_avatar( $avatar = '', $id_or_email, $sSize = null, $default = null, $alt = null ) {
    global $UniAvatar;
    return $UniAvatar->get_user_avatar_image( $avatar, $id_or_email, $sSize, $default, $alt );
}

//
if ( !function_exists('uni_user_avatar_plugin_activate') ) {
    function uni_user_avatar_plugin_activate(){
        update_option('uni_avatar_default_avatar_image', 'white');
        update_option('uni_avatar_predefined_avatars', 'default');
    }
}

//
if ( !function_exists('uni_user_avatar_plugin_deactivate') ) {
    function uni_user_avatar_plugin_deactivate(){}
}

//Activation and Deactivation hooks
register_activation_hook( __FILE__, 'uni_user_avatar_plugin_activate');
register_deactivation_hook( __FILE__, 'uni_user_avatar_plugin_deactivate');

?>