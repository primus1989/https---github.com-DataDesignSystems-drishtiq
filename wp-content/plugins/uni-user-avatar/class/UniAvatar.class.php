<?php
/*
* Class UniAvatar
*
*
*/

class UniAvatar {

    function __construst() {

        add_action( 'wp_ajax_uni_upload_avatar', array( $this, 'uni_upload_avatar') );
        add_action( 'wp_ajax_uni_save_avatar', array( $this, 'uni_save_avatar') );
        add_action( 'wp_ajax_uni_avatar_ajax_load', array( $this, 'uni_avatar_ajax_load') );
        add_action( 'wp_ajax_uni_delete_avatar', array( $this, 'uni_delete_avatar') );
        add_action( 'wp_ajax_uni_select_avatar', array( $this, 'uni_select_avatar') );
        add_action( 'admin_print_scripts', array( $this, 'print_media_templates' ) );

    }

    protected function _r() {
        $aResult = array(
		    'status' 	=> 'error',
			'message' 	=> __('Error!', 'uni-avatar'),
			'redirect'	=> ''
		);
        return $aResult;
    }

	/**
	 * Adds a new template for the HelloWorld view.
	 */
	public function print_media_templates( $iUserId = null ) {

        if ( is_user_logged_in() ) {

        $sUploadAvatarFormAction    = ( ( function_exists('icl_get_languages') ) ? add_query_arg(array('action' => 'uni_upload_avatar', 'lang' => ICL_LANGUAGE_CODE), admin_url('admin-ajax.php')) : add_query_arg(array('action' => 'uni_upload_avatar'), admin_url('admin-ajax.php')) );
        $sSaveAvatarFormAction      = ( ( function_exists('icl_get_languages') ) ? add_query_arg(array('action' => 'uni_save_avatar', 'lang' => ICL_LANGUAGE_CODE), admin_url('admin-ajax.php')) : add_query_arg(array('action' => 'uni_save_avatar'), admin_url('admin-ajax.php')) );
        $sCustomAvatarIds           = get_option('uni_avatar_custom_avatars');
        $sMaxUploadSize             = ( get_option('uni_avatar_default_size') ) ? get_option('uni_avatar_default_size') : UNI_AVATAR_DEFAULT_SIZE;
        if ( is_admin() && isset($_GET['user_id']) && !empty($_GET['user_id']) ) {
            $iUserId = $_GET['user_id'];
        } else if ( !isset($iUserId) || empty($iUserId) ) {
            global $current_user;
            get_currentuserinfo();
            $iUserId = $current_user->ID;
        }
		?>
		<script type="text/html" id="tmpl-uni-avatar-upload">
            <div id="uni-avatar-upload-form">

            <div id="uni-avatar-preview-wrapper">
                <div id="uni-avatar-preview-original">
                    <img src="<?php echo esc_url( $this->get_user_avatar_url( $iUserId ) ); ?>" id="uni_cropbox" />
                </div>

                <div id="uni-avatar-preview-pane">
                    <h5><?php _e('Preview', 'uni-avatar') ?></h5>
                    <div class="uni-avatar-preview-container">
                        <img src="" class="jcrop-preview" />
                    </div>
                </div>
            </div>

                <div style="clear:both;"></div>

                <div id="upload-alert"></div>

                <form id="uni_upload_avatar" name="uni_upload_avatar" action="<?php echo esc_url( $sUploadAvatarFormAction ); ?>" method="POST" enctype="multipart/form-data">
                    <legend class="avatar-form-title"><?php _e('Upload photo:', 'uni-avatar') ?></legend>
                    <input type="file" name="upload_avatar" id="avatar_image_upload" class="btn btn-info" value="<?php _e('Upload', 'uni-avatar') ?>" />
                </form>

                <div style="clear:both;"></div>

                <form id="uni_save_avatar" name="uni_save_avatar" action="<?php echo admin_url('admin-ajax.php'); ?>?action=uni_save_avatar" method="POST" enctype="multipart/form-data">
                    <legend class="avatar-form-title"><?php _e('Save photo:', 'uni-avatar') ?></legend>
                    <input type="hidden" name="cropped_url" value="" />
                    <input type="hidden" name="cropped_x1" value="1" />
                    <input type="hidden" name="cropped_y1" value="1" />
                    <input type="hidden" name="cropped_x2" value="250" />
                    <input type="hidden" name="cropped_y2" value="250" />
                    <input type="hidden" name="cropped_w" value="250" />
                    <input type="hidden" name="cropped_h" value="250" />
                    <input type="hidden" name="crop_session" value="1" />
                    <input type="hidden" name="user_id" value="<?php echo $iUserId; ?>" />
                    <input type="hidden" name="attach_id" value="" />
                    <input type="submit" name="save_avatar" class="btn btn-info" id="avatar_image_save" value="<?php _e('Save', 'uni-avatar') ?>" />
                </form>

                <?php do_action('uni_avatar_upload_custom_html_top'); ?>

                <div id="upload-loader" class="loader-in-upload-form"></div>

                <?php do_action('uni_avatar_upload_custom_html_bottom'); ?>

                <small><?php _e('Only images (.jpg, .png, .gif) are allowed!', 'uni-avatar') ?></small>
                <small><?php echo sprintf( __('Maximum upload size - %s Kb! <a href="http://www.picresize.com/" target="_blank">Make image smaller</a>', 'uni-avatar'), $sMaxUploadSize); ?></small>

            </div>
		</script>
        <?php if ( isset($sCustomAvatarIds) && !empty($sCustomAvatarIds) ) { ?>
		<script type="text/html" id="tmpl-uni-avatar-select">
            <div id="uni-avatar-select-form">
                <form id="uni_select_avatar" action="<?php echo admin_url('admin-ajax.php'); ?>?action=uni_select_avatar" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo $iUserId; ?>" />
                <?php
                $aAvatarIds = explode(',', $sCustomAvatarIds);
                if ( $aAvatarIds ) {
                    foreach ( $aAvatarIds as $iImageId ) {
                        $sAvatarImage = wp_get_attachment_thumb_url( $iImageId );
                ?>
			            <div class="uni_avatar_thumbnail" style="float:left;margin-right:10px;">
                            <img src="<?php echo esc_url( $sAvatarImage ); ?>" width="100px" height="100px" />
                            <input type="radio" name="select_avatar" value="<?php echo $iImageId; ?>" />
                        </div>
                <?php
                    }
                        echo '<div style="clear:both;"></div><input type="submit" name="save_avatar" id="uni_save_avatar_button" class="btn btn-info" value="'. __('Save', 'uni-avatar') .'" />';
                        echo '<div id="upload-loader" class="loader-in-select-form"></div>';
                } else {
                    printf( __( '%s There is no uploaded images. %s',  'uni-avatar' ), '<span class="uni-avatar-no-uploaded-images">', '<span>' );
                }
                ?>
                </form>
		</script>
        <?php } ?>
		<?php

        }

	}

    /*
    *  @param
    */
    function display_user_avatar_modal_link( $iUserId = null, $sTitle = null ) {

        if ( is_user_logged_in() ) {

	    $aLinkClasses = array('btn', 'btn-success', 'btn-sm');

		$aLinkClasses = apply_filters('uni_avatar_upload_link_classes_filter', $aLinkClasses);

        $sLinkClasses = implode(' ', $aLinkClasses);

        if ( !empty($sTitle) ) {
            $sLinkTitle = $sTitle;
        } else {
            $sLinkTitle = __('Upload', 'uni-avatar');
        }

        if ( isset($iUserId) && !empty($iUserId) ) {

            if ( !is_admin() ) {
                $this->print_media_templates( $iUserId );
            }

            echo '<a href="#" data-user_id="'.$iUserId.'" class="uni-avatar-open-modal '.esc_attr($sLinkClasses).'">'.esc_html($sLinkTitle).'</a>';

        } else {  // User views his own profile, so he can edit his avatar

            global $current_user;
            get_currentuserinfo();

            if ( !is_admin() ) {
                $this->print_media_templates( $iUserId );
            }

            echo '<a href="#" data-user_id="'.$current_user->ID.'" class="uni-avatar-open-modal '.esc_attr($sLinkClasses).'">'.esc_html($sLinkTitle).'</a>';

        }

        }

    }

    /*
    *  @param
    */
    function display_user_avatar_delete_link( $iUserId = null, $sTitle = null ) {

        if ( is_user_logged_in() ) {

	    $aLinkClasses = array('btn', 'btn-danger', 'btn-sm');

		$aLinkClasses = apply_filters('uni_avatar_delete_link_classes_filter', $aLinkClasses);

        $sLinkClasses = implode(' ', $aLinkClasses);

        if ( !empty($sTitle) ) {
            $sLinkTitle = $sTitle;
        } else {
            $sLinkTitle = __('Delete', 'uni-avatar');
            $sLinkTitle = apply_filters('uni_avatar_delete_link_title_filter', $sLinkTitle);
        }

        if ( isset($iUserId) && !empty($iUserId) ) {

            echo '<a href="#" data-user_id="'.$iUserId.'" class="uni_delete_avatar '.esc_attr($sLinkClasses).'">'.esc_html($sLinkTitle).'</a>';

        } else {  // User views his own profile, so he can edit his avatar

            global $current_user;
            get_currentuserinfo();

            echo '<a href="#" data-user_id="'.$current_user->ID.'" class="uni_delete_avatar '.esc_attr($sLinkClasses).'">'.esc_html($sLinkTitle).'</a>';

        }

        }

    }

    /*
    *  @param
    */
    function uni_upload_avatar() {

        $uFile = $_FILES['upload_avatar'];
        $aResult = $this->_r();

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $aUploadDir = wp_upload_dir();

        if ( $uFile ) {

            $aAllowedMimes = array(
                'gif'  => 'image/gif',
                'jpe'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'jpg'  => 'image/jpeg',
                'png'  => 'image/png',
            );

            $overrides = array(
                'mimes'     => $aAllowedMimes,
                'test_form' => false
            );

	        $overrides = array('test_form' => false);
		    $file = wp_handle_upload($uFile, $overrides);

		    if ( isset($file['error']) ){
		        $aResult['message'] = sprintf( __('Image upload error: %s', 'uni-avatar'), $file['error'] );
            } else {
		        $attachment = array (
		            'post_mime_type'    => $file['type'],
                    'post_content'      => '',
		            'guid'              => $file['url'],
		            'post_parent'       => 0,
                    'post_author'       => 1
      	        );


		        $iAttachmentID = wp_insert_attachment( $attachment, $file['file'] );
		        if ( !is_wp_error($iAttachmentID) ) {
		            wp_update_attachment_metadata($iAttachmentID, wp_generate_attachment_metadata($iAttachmentID, $file['file']));
  		        }

                $sUploadedUrl = wp_get_attachment_image_src( $iAttachmentID, 'full' );

                $aResult['status']      = 'success';
                $aResult['message']     = 'image cropped';
                $aResult['url']         = $sUploadedUrl[0];
                $aResult['width']       = $sUploadedUrl[1];
                $aResult['height']      = $sUploadedUrl[2];
                $aResult['attach_id']   = $iAttachmentID;

            }
        } else {
            $aResult['message'] = __('File has not been uploaded for unknown reason!', 'uni-avatar');
        }

        wp_send_json( $aResult );
    }

    /*
    *  @param
    */
    function uni_save_avatar() {

        $aNewData = $_POST;
        $aResult = $this->_r();

        $sUrlPart = parse_url($aNewData['cropped_url'], PHP_URL_PATH);
        $sCropedImagePath = $_SERVER['DOCUMENT_ROOT'].$sUrlPart;

        if ( $sCropedImagePath ) {
            if (function_exists('wp_get_image_editor')) {
            $img = wp_get_image_editor( $sCropedImagePath );
            if ( ! is_wp_error( $img ) ) {
                if ( $aNewData['cropped_w'] <= 512 && $aNewData['cropped_h'] <= 512 ) {
                    $img->crop( $aNewData['cropped_x1'], $aNewData['cropped_y1'], $aNewData['cropped_x2'], $aNewData['cropped_y2'], $aNewData['cropped_w'], $aNewData['cropped_h'], true );
                } else {
                    $img->crop( $aNewData['cropped_x1'], $aNewData['cropped_y1'], $aNewData['cropped_x2'], $aNewData['cropped_y2'], 250, 250, true );
                }

                $sFilename = $aNewData['user_id'].'_avatar' . '_' . $aNewData['crop_session'] . '.png';
                $aCroppedFile = $img->save( ABSPATH.'wp-content/uploads/uni-avatars/' . $sFilename );

                $sCroppedUrl = get_bloginfo('url').'/wp-content/uploads/uni-avatars/'.$sFilename;

                $tmp = download_url( $sCroppedUrl );
                $file_array = array(
                    'name'      => basename( $sCroppedUrl ),
                    'tmp_name'  => $tmp
                );

		        if ( $aCroppedFile && !is_wp_error( $tmp ) ){
		            $post_data = array (
		                'post_mime_type'    => $aCroppedFile['mime-type'],
                        'post_title'        => 'user_'.$aNewData['user_id'].'_avatar',
                        'post_content'      => '',
  		                'post_parent'       => 0,
                        'post_author'       => $aNewData['user_id']
      	            );

                    $iAttachmentID = media_handle_sideload( $file_array, 0, '', $post_data );

                    @unlink( $aCroppedFile['path'] );
                    @unlink( $file_array[ 'tmp_name' ] );

                    //
                    $sAvatarIds = get_option('uni_avatar_custom_avatars');
                    $aAvatarIds = array();
                    if ( $sAvatarIds ) $aAvatarIds = explode(',', $sAvatarIds);

                    $sUserAvatarId = get_user_meta( $aNewData['user_id'], 'uni_user_avatar', true);
                    if ( isset($sUserAvatarId) && !empty($sUserAvatarId) ) {
                        if ( !in_array( $sUserAvatarId, $aAvatarIds ) ) {
                            wp_delete_attachment( $sUserAvatarId, true );
                        }
                    }
                    if ( !in_array( $aNewData['attach_id'], $aAvatarIds ) ) {
                        wp_delete_attachment( $aNewData['attach_id'], true );
                    }
                    update_user_meta( $aNewData['user_id'], 'uni_user_avatar', $iAttachmentID);
                    update_post_meta( $iAttachmentID, '_uni_avatar_image', 1);

                    $aUserAvatar = wp_get_attachment_image_src( $iAttachmentID, 'full' );
                    $sUserAvatar = $aUserAvatar[0];

                    $aResult['status']      = 'success';
                    $aResult['message']     = __('New avatar has been saved!', 'uni-avatar');
                    $aResult['url']         = $sUserAvatar;

                } else {
                    $aResult['status']  = 'error';
                    $aResult['message'] = $aCroppedFile['error'];
                }

            } else {

                $aResult['status']  = 'error';
                $aResult['message'] = $img->get_error_message();

            }

        } else {
            $aResult['status']  = 'error';
            $aResult['message'] = __('WP 3.5+ required!', 'uni-avatar');
        }
        } else {
            $aResult['status']  = 'error';
            $aResult['message'] = __('No coordinates or image URI!', 'uni-avatar');
        }

        wp_send_json( $aResult );
    }

    /*
    *  @param
    */
    function uni_avatar_ajax_load() {

        $aResult = $this->_r();
        $iUserId = (int)$_POST['user_id'];

        if ( $iUserId ) {

            $sUserAvatar = $this->get_user_avatar_url( $iUserId );

            $aResult['status']  = 'success';
            $aResult['message'] = '';
            $aResult['url']     = $sUserAvatar;

        } else {
            $aResult['status']  = 'error';
            $aResult['message'] = __('Error: user ID not defined!', 'uni-avatar');
        }

        wp_send_json( $aResult );
    }

    /*
    *  @param
    */
    function uni_delete_avatar() {

        $aResult = $this->_r();
        $iUserId = (int)$_POST['user_id'];

        if ( $iUserId ) {

            $iUserAvatarAttachId = get_user_meta( $iUserId, 'uni_user_avatar', true );

            //
            $sAvatarIds = get_option('uni_avatar_custom_avatars');
            $aAvatarIds = array();
            if ( $sAvatarIds ) $aAvatarIds = explode(',', $sAvatarIds);
            if ( !in_array( $iUserAvatarAttachId, $aAvatarIds ) ) {
                wp_delete_attachment( $iUserAvatarAttachId, true );
            }

            delete_user_meta( $iUserId, 'uni_user_avatar');

            // this will return default avatar
            $sUserAvatar = $this->get_user_avatar_url( $iUserId );

            $aResult['status']  = 'success';
            $aResult['message'] = __('User avatar successfully deleted!', 'uni-avatar');
            $aResult['url']     = $sUserAvatar;

        } else {
            $aResult['status']  = 'error';
            $aResult['message'] = __('Error: user ID not defined!', 'uni-avatar');
        }

        wp_send_json( $aResult );
    }

    /*
    *  @param
    */
    function uni_select_avatar() {

        $aResult = $this->_r();
        $iAttachId = (int)$_POST['select_avatar'];

        global $current_user;
        get_currentuserinfo();

        if ( $iAttachId && $current_user->ID ) {

                    update_user_meta( $current_user->ID, 'uni_user_avatar', $iAttachId);
                    $aUserAvatar = wp_get_attachment_image_src( $iAttachId, 'full' );
                    $sUserAvatar = $aUserAvatar[0];

                    $aResult['status']      = 'success';
                    $aResult['message']     = __('New avatar has been saved!', 'uni-avatar');
                    $aResult['url']         = $sUserAvatar;

        } else {
            $aResult['status']  = 'error';
            $aResult['message'] = __('Error: image ID not defined!', 'uni-avatar');
        }

        wp_send_json( $aResult );
    }

    /*
    *  Helper function
    *  Gets url for avatar
    */
    function get_avatar_url( $get_avatar ){
        preg_match("/src='(.*?)'/i", $get_avatar, $matches);
        if ( !$matches[1] ) {
            preg_match('/src="(.*?)"/i', $get_avatar, $matches);
        }
        return $matches[1];
    }

    /*
    *  @param mixed
    *
    */
    function get_user_avatar_url( $id_or_email, $iSize = null ) {

        $aArgs = array();

        $aDefaults = array (
                    'size' => uni_avatar_get_avatars_size( $iSize ),
                    'crop' => true
                    );
        $aArgs = wp_parse_args( $aArgs, $aDefaults );

	    $iUserId = '';
	    if ( is_numeric($id_or_email) ) {
		    $iUserId = absint( $id_or_email );
        } elseif ( is_string( $id_or_email ) ) {
            $oUser = get_user_by( 'email', $id_or_email );
            $iUserId = $oUser->ID;
  	    } elseif ( is_object($id_or_email) ) {
		    // No avatar for pingbacks or trackbacks
		    $allowed_comment_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );
		    if ( ! empty( $id_or_email->comment_type ) && ! in_array( $id_or_email->comment_type, (array) $allowed_comment_types ) )
			    return false;

		    if ( ! empty( $id_or_email->user_id ) ) {
			    $iUserId = (int) $id_or_email->user_id;
		    }
	    }

        if ( get_user_meta( $iUserId, 'uni_user_avatar', true) ) {

            $sUserAvatarId = get_user_meta( $iUserId, 'uni_user_avatar', true);
            $aUserAvatar = wp_get_attachment_image_src( $sUserAvatarId, 'full' );
            $sUserAvatar = $aUserAvatar[0];

            return bfi_thumb( $sUserAvatar, $aArgs );

        } else {

            $sGender = get_user_meta( $iUserId, '_uni_avatar_gender', true );

            if ( get_option('uni_avatar_gender_mode') && isset($sGender) && !empty($sGender) ) {

                $aPredefinedAvatars = uni_avatar_predefined_avatars_array();
                $sChosenPredefinedAvatar = get_option('uni_avatar_predefined_avatars');
                if ( $sGender == 'male' ) {
                    $sDefaultImageUrl = UNI_AVATAR_WP_PLUGIN_URL.'css/images/user-avatar-male-'.$sChosenPredefinedAvatar.'.png';
                    return bfi_thumb( $sDefaultImageUrl, $aArgs );
                } elseif ( $sGender == 'female' ) {
                    $sDefaultImageUrl = UNI_AVATAR_WP_PLUGIN_URL.'css/images/user-avatar-female-'.$sChosenPredefinedAvatar.'.png';
                    return bfi_thumb( $sDefaultImageUrl, $aArgs );
                }
            } else {
                $aDefaultAvatars = uni_avatar_default_avatars_array();
                $sChosenDefaultAvatar = get_option('uni_avatar_default_avatar_image');
                $sDefaultImageUrl = UNI_AVATAR_WP_PLUGIN_URL.'css/images/user-avatar-default_'.$sChosenDefaultAvatar.'.png';
                return bfi_thumb( $sDefaultImageUrl, $aArgs );
            }

        }

    }

    /*
    *  @param mixed
    *
    */
    function get_user_avatar_image( $avatar = '', $id_or_email, $iSize = null, $default = null, $alt = null, $args = null ) {

        $aArgs = array();

        $aDefaults = array (
                    'size' => uni_avatar_get_avatars_size( $iSize ),
                    'crop' => true
                    );
        $aArgs = wp_parse_args( $aArgs, $aDefaults );

	    $iUserId = '';
	    if ( is_numeric($id_or_email) ) {
		    $iUserId = absint( $id_or_email );
        } elseif ( is_string( $id_or_email ) ) {
            $oUser = get_user_by( 'email', $id_or_email );
            $iUserId = $oUser->ID;
  	    } elseif ( is_object($id_or_email) ) {
		    // No avatar for pingbacks or trackbacks
		    $allowed_comment_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );
		    if ( ! empty( $id_or_email->comment_type ) && ! in_array( $id_or_email->comment_type, (array) $allowed_comment_types ) )
			    return false;

		    if ( ! empty( $id_or_email->user_id ) ) {
			    $iUserId = (int) $id_or_email->user_id;
		    }
	    }

        $oUserData = get_userdata( $iUserId );

        if ( get_user_meta( $iUserId, 'uni_user_avatar', true) ) {

            $sUserAvatarId = get_user_meta( $iUserId, 'uni_user_avatar', true);
            $aUserAvatar = wp_get_attachment_image_src( $sUserAvatarId, 'full' );
            $sUserAvatar = $aUserAvatar[0];

            $sImageUrl = bfi_thumb( $sUserAvatar, $aArgs );

        } else {

            $sGender = get_user_meta( $iUserId, '_uni_avatar_gender', true );

            if ( get_option('uni_avatar_gender_mode') && isset($sGender) && !empty($sGender) ) {

                $aPredefinedAvatars = uni_avatar_predefined_avatars_array();
                $sChosenPredefinedAvatar = get_option('uni_avatar_predefined_avatars');
                if ( $sGender == 'male' ) {
                    $sDefaultImageUrl = UNI_AVATAR_WP_PLUGIN_URL.'css/images/user-avatar-male-'.$sChosenPredefinedAvatar.'.png';
                    $sImageUrl = bfi_thumb( $sDefaultImageUrl, $aArgs );
                } elseif ( $sGender == 'female' ) {
                    $sDefaultImageUrl = UNI_AVATAR_WP_PLUGIN_URL.'css/images/user-avatar-female-'.$sChosenPredefinedAvatar.'.png';
                    $sImageUrl = bfi_thumb( $sDefaultImageUrl, $aArgs );
                }
            } else {
                $aDefaultAvatars = uni_avatar_default_avatars_array();
                $sChosenDefaultAvatar = get_option('uni_avatar_default_avatar_image');
                $sDefaultImageUrl = UNI_AVATAR_WP_PLUGIN_URL.'css/images/user-avatar-default_'.$sChosenDefaultAvatar.'.png';
                $sImageUrl = bfi_thumb( $sDefaultImageUrl, $aArgs );
            }

        }

        $sFinalSize = $aArgs['size'];

	    $aAvatarClasses = array('uni-user-avatar-image', 'avatar', 'avatar-'.$sFinalSize, 'photo', 'avatar-default');
	    $aAvatarClasses = apply_filters('uni_avatar_img_classes_filter', $aAvatarClasses);

        $sAvatarClasses = implode(' ', $aAvatarClasses);

        if ( isset($alt) && !empty($alt) ) {
		    $sAvatar = '<img src="'.esc_url($sImageUrl).'" data-user_id="'.$iUserId.'" width="'.$sFinalSize.'" height="'.$sFinalSize.'" class="'.esc_attr($sAvatarClasses).'" alt="'.esc_attr($alt).'" />';
        } else {
            $sAvatar = '<img src="'.esc_url($sImageUrl).'" data-user_id="'.$iUserId.'" width="'.$sFinalSize.'" height="'.$sFinalSize.'" class="'.esc_attr($sAvatarClasses).'" alt="'.esc_attr($oUserData->display_name).'" />';
        }

        return $sAvatar;

    }

}

?>