<?php
// init
add_action('init', 'uni_cpo_custom_options_for_attr');
function uni_cpo_custom_options_for_attr() {
    $aAttributes = uni_cpo_get_attribute_taxonomies();
    foreach ( $aAttributes as $sAttr ) {
        add_action( 'pa_'.$sAttr.'_edit_form_fields', 'uni_cpo_tax_custom_data' );
        add_action( 'pa_'.$sAttr.'_add_form_fields', 'uni_cpo_add_new_meta_fields', 10, 2 );
        add_action( 'edited_pa_'.$sAttr, 'uni_cpo_save_custom_meta', 10, 2 );
        add_action( 'create_pa_'.$sAttr, 'uni_cpo_save_custom_meta', 10, 2 );
        add_action( 'delete_pa_'.$sAttr, 'uni_cpo_delete_custom_meta', 10, 2 );

        add_filter( 'manage_edit-pa_'.$sAttr.'_columns', 'uni_cpo_add_custom_attr_column');
        add_filter( 'manage_pa_'.$sAttr.'_custom_column', 'uni_cpo_add_custom_attr_column_content', 10, 3);
    }
}

// Add new meta fields
function uni_cpo_add_new_meta_fields( $taxonomy ) {
	?>
    <legend><?php _e('UNI CPO Attribute Meta Fields', 'uni-cpo') ?></legend>
	<div class="form-field">
		<label for="uni-cpo-attr-price"><?php _e('Price', 'uni-cpo') ?></label>
		<input type="text" name="uni_cpo_custom[attr_price]" id="uni-cpo-attr-price" value="">
		<p class="description"><?php _e('Enter a price for this option (optional)', 'uni-cpo') ?></p>
	</div>
	<div class="form-field">
		<label for="uni-cpo-attr-color-code"><?php _e('Color (for color input)', 'uni-cpo') ?></label>
		<input type="text" name="uni_cpo_custom[attr_color_code]" id="uni-cpo-attr-color-code" value="">
		<p class="description"><?php _e('Choose a color for this option (optional). Chosen color will be placed into the palette for the color input.', 'uni-cpo') ?></p>
	</div>
<?php
}

// save values of the new meta fields
function uni_cpo_save_custom_meta( $sTermId ) {
	if ( isset( $_POST['uni_cpo_custom'] ) ) {

		$aTermData = get_option('uni_cpo_attr_'.$sTermId.'_data');
		$aKeys = array_keys( $_POST['uni_cpo_custom'] );
		foreach ( $aKeys as $sKey ) {
			if ( isset ( $_POST['uni_cpo_custom'][$sKey] ) ) {
			    if ( $sKey == 'attr_price' ) {
			        if ( !$_POST['uni_cpo_custom'][$sKey] ) {
			            $aTermData[$sKey] = '0';
                    } else {
                        $aTermData[$sKey] = number_format($_POST['uni_cpo_custom'][$sKey], 4, '.', '');
                    }
			    } else {
				    $aTermData[$sKey] = $_POST['uni_cpo_custom'][$sKey];
                }
			}
		}
		// Save the option array.
        update_option( 'uni_cpo_attr_'.$sTermId.'_data' , $aTermData );
	}
}

// delete values
function uni_cpo_delete_custom_meta( $oTerm, $sTermId) {
    delete_option( 'uni_cpo_attr_'.$sTermId.'_data' );
}

// output additional fields to edit attr page
function uni_cpo_tax_custom_data( $oTerm ) {
    $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
		$image 			= '';
		$thumbnail_id 	= ( !empty($aTermData['attr_image']) ) ? absint( $aTermData['attr_image'] ) : '';
		if ( $thumbnail_id )
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		else
			$image = wc_placeholder_img_src();
    ?>

        <tr valign="top" class="form-field">
        <th scope="row"><?php _e('Price', 'uni-cpo') ?></th>
            <td>
                <input class="regular-text" type="text" name="uni_cpo_custom[attr_price]" value="<?php if ( !empty($aTermData['attr_price']) ) echo esc_attr( $aTermData['attr_price'] ); ?>" />
                <p class="description"><?php _e('Enter a price for this option. <strong>Use of different types of fields: all types except text input and textarea.</strong>', 'uni-cpo') ?></p>
            </td>
        </tr>
        <tr valign="top" class="form-field">
        <th scope="row"><?php _e('Color (for color input)', 'uni-cpo') ?></th>
            <td>
                <input class="regular-text" type="text" name="uni_cpo_custom[attr_color_code]" id="uni-cpo-attr-color-code" value="<?php if ( !empty($aTermData['attr_color_code']) ) echo esc_attr( $aTermData['attr_color_code'] ); ?>" />
                <p class="description"><?php _e('Choose a color for this option. Chosen color will be placed into the palette for the color input. <strong>Use of different types of fields: only for color input with palette.</strong>', 'uni-cpo') ?></p>
            </td>
        </tr>
        <tr valign="top" class="form-field">
        <th scope="row"><?php _e( 'Image', 'uni-cpo' ) ?></th>
            <td>
			<label><?php _e( 'Thumbnail', 'uni-cpo' ); ?></label>
			<div id="product_cat_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
			<div style="line-height:60px;">
				<input type="hidden" id="product_cat_thumbnail_id" name="uni_cpo_custom[attr_image]" value="<?php echo $thumbnail_id; ?>" />
				<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'uni-cpo' ); ?></button>
				<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'uni-cpo' ); ?></button>
			</div>
			<script type="text/javascript">

				 // Only show the "remove image" button when needed
				 if ( ! jQuery('#product_cat_thumbnail_id').val() )
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
						title: '<?php _e( 'Choose an image', 'uni-cpo' ); ?>',
						button: {
							text: '<?php _e( 'Use image', 'uni-cpo' ); ?>',
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						attachment = file_frame.state().get('selection').first().toJSON();

						jQuery('#product_cat_thumbnail_id').val( attachment.id );
						jQuery('#product_cat_thumbnail img').attr('src', attachment.url );
						jQuery('.remove_image_button').show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery(document).on( 'click', '.remove_image_button', function( event ){
					jQuery('#product_cat_thumbnail img').attr('src', '<?php echo esc_url( wc_placeholder_img_src() ); ?>');
					jQuery('#product_cat_thumbnail_id').val('');
					jQuery('.remove_image_button').hide();
					return false;
				});

			</script>
			<div class="clear"></div>
            <p class="description"><?php _e('<strong>Use of different types of fields: only for radio inputs and checkboxes.</strong>', 'uni-cpo') ?></p>
            </td>
        </tr>
        <tr valign="top" class="form-field">
        <th scope="row"><?php _e('Min. value (is used for input number only!)', 'uni-cpo') ?></th>
            <td>
                <input class="regular-text" type="text" name="uni_cpo_custom[text_min_value]" value="<?php if ( !empty($aTermData['text_min_value']) ) echo esc_attr( $aTermData['text_min_value'] ); ?>" />
                <p class="description"><?php _e('Define a min. value (number) that client can choose.', 'uni-cpo') ?></p>
            </td>
        </tr>
        <tr valign="top" class="form-field">
        <th scope="row"><?php _e('Max. value (is used for input number only!)', 'uni-cpo') ?></th>
            <td>
                <input class="regular-text" type="text" name="uni_cpo_custom[text_max_value]" value="<?php if ( !empty($aTermData['text_max_value']) ) echo esc_attr( $aTermData['text_max_value'] ); ?>" />
                <p class="description"><?php _e('Define a max. value (number) that client can choose.', 'uni-cpo') ?></p>
            </td>
        </tr>
        <tr valign="top" class="form-field">
        <th scope="row"><?php _e('Default value (is used for both regular text input and input number!)', 'uni-cpo') ?></th>
            <td>
                <input class="regular-text" type="text" name="uni_cpo_custom[text_def_value]" value="<?php if ( !empty($aTermData['text_def_value']) ) echo esc_attr( $aTermData['text_def_value'] ); ?>" />
                <p class="description"><?php _e('Define a default value that will be shown for a client at the beginning.', 'uni-cpo') ?></p>
            </td>
        </tr>
    <?php
}

// scripts needed for plupload to work on edit attr page
function uni_cpo_attr_tax_script($hook) {
    if ( $hook == 'edit-tags.php' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['taxonomy']) && isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'product' ) {
        wp_enqueue_script('jquery');
        wp_enqueue_media();
    }
}
add_action( 'admin_enqueue_scripts', 'uni_cpo_attr_tax_script' );

// columns names
function uni_cpo_add_custom_attr_column($columns){
    $columns['uni_cpo_attr_price'] = 'CPO Price';
    $columns['uni_cpo_attr_color_code'] = 'CPO Color';
    $columns['uni_cpo_attr_image'] = 'CPO Image';
    return $columns;
}

// columns content
function uni_cpo_add_custom_attr_column_content($content,$column_name,$term_id){
    $aTermData = get_option('uni_cpo_attr_'.$term_id.'_data');
    switch ($column_name) {
        case 'uni_cpo_attr_price':
            $content = ( !empty($aTermData['attr_price']) ) ? $aTermData['attr_price'] : '';
            break;
        case 'uni_cpo_attr_color_code':
            //$content = $aTermData['attr_color_code'];
            if ( !empty($aTermData['attr_color_code']) ) {
                $content = '<span style="display:block;width:20px;height:20px;background-color:'.$aTermData['attr_color_code'].';"></span><br>'.$aTermData['attr_color_code'];
            }
            break;
        case 'uni_cpo_attr_image':
			$image 			= '';
			$thumbnail_id 	= (( !empty($aTermData['attr_image']) ) ? $aTermData['attr_image'] : '');

			if ($thumbnail_id)
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			else
				$image = wc_placeholder_img_src();

			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: http://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );
			$content = '<img src="' . esc_url( $image ) . '" alt="Thumbnail" class="wp-post-image" height="48" width="48" />';
            break;
        default:
            break;
    }
    return $content;
}

?>