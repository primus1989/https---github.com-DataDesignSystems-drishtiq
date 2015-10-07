<?php
// init
add_action( 'init', 'uni_cpo_admin_options_init', 10 );
function uni_cpo_admin_options_init() {
    // plugin options page
    add_action('admin_menu', 'uni_cpo_create_menu');
}

//
function uni_cpo_create_menu() {
	add_submenu_page( 'woocommerce', __('Uni CPO', 'uni-cpo'), __('Uni CPO', 'uni-cpo'), 'manage_woocommerce', 'uni-cpo-options', 'uni_cpo_plugin_function');
	add_action( 'admin_init', 'uni_cpo_register_settings' );
}

//
function uni_cpo_register_settings() {
	register_setting( 'uni-cpo-settings-group', 'uni_cpo_price_container' );
}

//
function uni_cpo_plugin_function(){
    ?>
    <div class="wrap">
	    <?php screen_icon(); ?>
	    <h2><?php _e('UNI CPO Plugin Options', 'uni-cpo') ?></h2>

	    <form method="post" action="options.php">
		    <?php settings_fields( 'uni-cpo-settings-group' ); ?>
		    <h3><?php _e('General settings', 'uni-cpo') ?></h3>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="uni_cpo_price_container"><?php _e('Custom selector (id/class) for a product price container', 'uni-cpo') ?></label></th>
					<td>
                        <input type="text" id="uni_cpo_price_container" name="uni_cpo_price_container" value="<?php echo get_option('uni_cpo_price_container'); ?>" />
                        <p class="description"><?php _e('By default, the selector for a product price container is ".summary.entry-summary .price span". However html markup of this block may differ from the default, so you have to define a new one.', 'uni-cpo') ?></p>
                    </td>
				</tr>
			</table>

		    <?php submit_button(); ?>

	    </form>
    </div>
    <?php
}

?>