<h4><?php _e( 'Change Profile Photo', 'buddypress' ); ?></h4>

<?php do_action( 'bp_before_profile_avatar_upload_content' ); ?>

<?php if ( !(int)bp_get_option( 'bp-disable-avatar-uploads' ) ) : ?>

    <?php if ( get_option('uni_avatar_enable_buddypress_form_text_above') ) { ?>
        <p><?php echo get_option('uni_avatar_enable_buddypress_form_text_above'); ?></p>
    <?php } ?>

	<p><?php $sTitleAdd = __('Upload Image', 'buddypress'); echo do_shortcode('[uav-modal-link title="'.$sTitleAdd.'"]') ?></p>

    <p><?php $sTitleRemove = __('Delete Profile Photo', 'buddypress'); echo do_shortcode('[uav-delete-link title="'.$sTitleRemove.'"]') ?></p>

<?php else : ?>

	<p><?php _e( 'Your profile photo will be used on your profile and throughout the site. To change your profile photo, please create an account with <a href="http://gravatar.com">Gravatar</a> using the same email address as you used to register with this site.', 'buddypress' ); ?></p>

<?php endif; ?>

<?php do_action( 'bp_after_profile_avatar_upload_content' ); ?>
