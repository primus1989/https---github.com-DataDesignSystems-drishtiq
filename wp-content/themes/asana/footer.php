	<footer id="footer" class="clear">
		<div class="footerSocial clear">
            <?php if ( ot_get_option( 'uni_email_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_email_link' ) ?>"><i class="fa fa-envelope"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_fb_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_fb_link' ) ?>"><i class="fa fa-facebook"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_tw_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_tw_link' ) ?>"><i class="fa fa-twitter"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_in_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_in_link' ) ?>"><i class="fa fa-instagram"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_li_link' ) ) { ?>
        	<a href="<?php echo ot_get_option( 'uni_li_link' ) ?>"><i class="fa fa-linkedin"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_bl_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_bl_link' ) ?>"><i class="fa fa-heart"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_pi_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_pi_link' ) ?>"><i class="fa fa-pinterest"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_gp_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_gp_link' ) ?>"><i class="fa fa-google-plus"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_fs_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_fs_link' ) ?>"><i class="fa fa-foursquare"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_fl_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_fl_link' ) ?>"><i class="fa fa-flickr"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_dr_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_dr_link' ) ?>"><i class="fa fa-dribbble"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_be_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_be_link' ) ?>"><i class="fa fa-behance"></i></a>
            <?php } ?>
            <?php if ( ot_get_option( 'uni_vk_link' ) ) { ?>
			<a href="<?php echo ot_get_option( 'uni_vk_link' ) ?>"><i class="fa fa-vk"></i></a>
            <?php } ?>
		</div>

		<?php wp_nav_menu( array( 'container' => '', 'container_class' => '', 'menu_class' => 'footerMenu clear', 'theme_location' => 'footer', 'depth' => 1, 'fallback_cb'=> 'uni_nav_footer_fallback' ) ); ?>

        <?php if ( ot_get_option('uni_mailchimp_footer_enable') == 'on' ) { ?>
		<div class="footerSubscribe">
		    <form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" role="form" method="post" class="uni_form">
                <input type="hidden" name="action" value="uni_mailchimp_subscribe_user" />
				<input type="text" name="uni_input_email" size="20" value="" placeholder="<?php _e('Your email', 'asana' ); ?>" data-parsley-required="true" data-parsley-trigger="change focusout submit" data-parsley-type="email">
				<input class="btnSubscribe uni_input_submit" type="button" value="<?php _e('Subscribe', 'asana' ); ?>">
			</form>
		</div>
        <?php } ?>

		<div class="copyright">
            <?php if ( ot_get_option( 'uni_footer_text' ) ) { ?>
				<?php echo ot_get_option( 'uni_footer_text' ) ?>
            <?php } else { ?>          
				<p><?php echo sprintf( __('Copyright &copy; %d. Asana All rights reserved', 'asana' ), date('Y') ); ?></p>
            <?php } ?>
		</div>
	</footer>

    <?php if ( ot_get_option('uni_home_membership_cards_enable') == 'on' || is_page_template( 'templ-prices.php' ) ) { ?>
    <div id="membershipCardOrderPopup" class="eventRegistrationWrap">
        <h3 id="uni_price_title"><?php _e('Order form', 'asana'); ?></h3>
        <p class="membershipCardOrderMsg"><?php _e('Please fill out the form and we\'ll get back to you asap', 'asana'); ?></p>
        <form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post" class="eventRegistrationForm clear uni_form">
            <input type="hidden" name="uni_contact_nonce" value="<?php echo wp_create_nonce('uni_nonce') ?>" />
            <input type="hidden" name="action" value="uni_price_form" />
            <input type="hidden" name="uni_price_id" value="" />

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
    <?php } ?>

	<div class="loaderWrap">
        <div class="la-ball-clip-rotate la-dark">
            <div></div>
        </div>
    </div>
	<div class="mobileMenu">
		<?php wp_nav_menu( array( 'container' => '', 'container_class' => '', 'menu_class' => '', 'theme_location' => 'primary', 'depth' => 3, 'fallback_cb'=> '' ) ); ?>
	</div>

    <?php wp_footer(); ?>

</body>
</html>