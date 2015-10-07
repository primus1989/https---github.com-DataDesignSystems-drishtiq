<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<title><?php echo wp_title(''); ?></title>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
		<!--[if lte IE 8]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<!--[if lt IE 8]>
			<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script>
		<![endif]-->
		<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" type="image/x-icon" />
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo get_stylesheet_directory_uri(); ?>/apple-touch-icon-144x144.png" />
        <link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo get_stylesheet_directory_uri(); ?>/apple-touch-icon-152x152.png" />
        <link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon-32x32.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon-16x16.png" sizes="16x16" />

        <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php echo bloginfo('rss2_url'); ?>">

        <!-- wp_header -->
        <?php
        if ( is_singular() ) wp_enqueue_script( "comment-reply" );
        wp_head(); ?>

<?php
if ( ot_get_option('uni_google_fonts') ) {
    $aCustomFonts = ot_get_option('uni_google_fonts');
    $ot_google_fonts      = get_theme_mod( 'ot_google_fonts', array() );
    $sFontNameOne = $sFontNameTwo = '';
    if ( isset($aCustomFonts) && !empty($aCustomFonts[0]) ) $sFontNameOne = $ot_google_fonts[$aCustomFonts[0]["family"]]['family'];
    if ( isset($aCustomFonts) && !empty($aCustomFonts[1]) ) $sFontNameTwo = $ot_google_fonts[$aCustomFonts[1]["family"]]['family'];
?>
<style type="text/css">
/* regular text */
body, table, input, textarea, select, li, button, p, blockquote, ol, dl, form, pre, th, td, a,
.uni-bridallist-title,
.uni-bridallist-title-wrapper .uni-bridallist-title.uni-bridallist-editable input,
.mainMenu ul li ul li a,
.footerSubscribe input[type="text"],
.pageHeader h1,
.contactInfo h3, .storyDesc h3,
.contactInfo p, .storyDesc p,
.teamItemNameWrap h3,
.teamItem .overlay p,
.teamItemDesc h3,
.teamItemDesc p,
.ourValues .wrapper p,
.sbi_header_link,
.blog section.container,
.singleTitle, .singlePostWrap h6,
.singlePostWrap h1, .singlePostWrap h2,
.singlePostWrap h3, .singlePostWrap h4, .singlePostWrap h5,
.singlePostWrap h6 a, .singlePostWrap h6 a:visited,
.singlePostWrap h5 a, .singlePostWrap h5 a:visited,
.singlePostWrap h4 a, .singlePostWrap h4 a:visited,
.singlePostWrap h3 a, .singlePostWrap h3 a:visited,
.singlePostWrap h2 a, .singlePostWrap h2 a:visited,
.singlePostWrap table th,
.singlePostWrap table th a, .singlePostWrap table th a:visited,
.singlePostWrap table td,
.singlePostWrap table td a, .singlePostWrap table td a:visited,
.singlePostWrap p,
.singlePostWrap,
.singlePostWrap dt a, .singlePostWrap dt a:visited,
.singlePostWrap dd a, .singlePostWrap dd a:visited,
.singlePostWrap p a, .singlePostWrap p a:visited,
.singlePostWrap dt,
.singlePostWrap dd,
.singlePostWrap > ul li, .singlePostWrap > ol li,
.singlePostWrap > ol li:before,
.singlePostWrap p a, .singlePostWrap p a:visited, .singlePostWrap > ul li a, .singlePostWrap > ul li a:visited,
.singlePostWrap > ol li a, .singlePostWrap > ol li a:visited,
.singlePostWrap address,
.singlePostWrap blockquote p,
.singlePostWrap blockquote p a, .singlePostWrap blockquote p a:visited,
.singlePostTags,
.singleLinkPages a, .singleLinkPages a:visited, .singlePostTags a, .singlePostTags a:visited,
.singlePostWrap .su-list ul li,
.singlePostWrap .su-tabs-nav span,
.singlePostWrap .su-tabs-pane,
.singlePostWrap .su-label,
.logged-in-as,
.bypostauthor .comment-wrapper .uni-post-author,
.comment-metadata time,
.comment-content p, .comment-awaiting-moderation,
.comment-content p a, .comment-content p a:visited,
#commentform input[type="text"],
#commentform textarea,
.contactForm h3,
.contactFormDesc,
.contactForm .form-row input[type="text"], .contactForm .form-row input[type="email"], .contactForm .form-row textarea,
.contactForm .wpcf7-form p,
.wpcf7-form .wpcf7-quiz,
.wpcf7-form .wpcf7-text,
.wpcf7-form .wpcf7-range,
.wpcf7-form .wpcf7-date,
.wpcf7-form textarea,
.wpcf7-form select,
.miniCart span,
.miniCartItem h3 a, .miniCartItem h3 a:visited,
.miniCartItem .price,
.miniCartItem .quantity span,
.miniCartItem .quantity input[type="text"],
.miniCartSubTotal,
.miniCartItem dt,
.miniCartItem dd,
.miniCartEmpty p,
.pageTitle,
.categoryList span,
.categoryList ul li a, .categoryList ul li a:visited,
.eventItemDesc h3 a, .eventItemDesc h3 a:visited,
.eventItemDesc p,
.subscribeBox h3,
.subscribeBox p,
.backToBtn, .backToBtn:visited,
.nextEventBox h3,
.eventDetailItem p,
.eventRegistrationWrap h3,
.eventRegistrationForm .form-row input[type="text"],
.eventRegistrationForm .form-row input[type="email"],
.eventRegistrationForm .form-row textarea,
.cartPage table th,
.cartPage table td,
.cartProduct h4 a, .cartProduct h4 a:visited,
.cartPage .quantity input[type="number"],
.cartPage .woocommerce td.product-name dl.variation dt,
.cartPage .woocommerce td.product-name dl.variation dd p,
.coupon label,
.coupon input[type="text"],
.woocommerce-page .cart-collaterals .cart_totals h2,
.woocommerce-cart .cart-collaterals .cart_totals tr th,
.woocommerce-cart .cart-collaterals .cart_totals tr td,
.shipping-calculator-form .selectric p,
.shipping-calculator-form p input[type="text"],
.page.woocommerce-cart .cart-empty, .uni-wishlist-empty,
table.uni-wishlist-table td,
.uni-wishlist-item-title a, .uni-wishlist-item-title a:visited,
.uni-wishlist-item-availability span,
.uni-wishlist-item-title dl.variation dt,
.uni-wishlist-item-title dl.variation dd,
.checkoutPage h3,
.checkoutPage p .selectric p,
.checkoutPage .mcell p label,
.checkoutPage .fcell p label,
.checkoutPage .fcell p input[type="password"],
.checkoutPage .mcell p textarea,
.checkoutPage .mcell p input[type="text"],
.checkoutPage .fcell p input[type="text"],
.checkoutPage .woocommerce .scell table.shop_table tfoot th,
.checkoutPage .woocommerce .scell table.shop_table tfoot td,
.cartItem h4 a, .cartItem h4 a:visited,
.cartItem p,
.payment_methods li label,
body .woocommerce form.checkout_coupon p.form-row input.input-text,
body .woocommerce form.login p.form-row input.input-text,
body.page.woocommerce-account .singlePostWrap address,
.page.woocommerce-account form label,
.page.woocommerce-account form p.form-row input.input-text,
.page.woocommerce-account form p.form-row textarea,
.page.woocommerce-account form p.form-row .selectric p,
.productDesc h1,
.productDesc p.price,
.productDesc p,
.productDesc .product_meta > span,
.productDesc .product_meta > span span,
.productDesc .product_meta > span a, .productDesc .product_meta > span a:visited,
.uni_cpo_fields_label,
.uni_cpo_fields_container .selectric p,
.productDesc table.variations .selectric p,
.uni_cpo_fields_container .selectric .button,
.productDesc table.variations .selectric .button,
.variations select,
form.woocommerce-ordering select,
.single-product .woocommerce-tabs #tab-reviews p,
.single-product .woocommerce-tabs #tab-description p,
.woocommerce #tab-reviews #reviews #comments ol.commentlist li .comment-text p.meta strong,
.woocommerce-review-link, .woocommerce-review-link:hover,
.woocommerce #tab-additional_information table.shop_attributes th,
.woocommerce #tab-additional_information table.shop_attributes td,
.uni-wishlist-link, .uni-wishlist-link:visited,
.classesCallendar .fc-toolbar h2,
.classesCallendar .fc-day-header,
.classesCallendar .fc-event,
.classesCallendar td.fc-time,
.classesDescPopup h3,
.classesDescWrap p,
.classesInstructorWrap h4,
.page404Wrap p,
.thankYouBox h3,
.thankYouWrap .order_details li,
.thankYouWrap h2,
.thankYouWrap .order_details thead th,
.thankYouWrap .order_details tbody td,
.thankYouWrap .order_details tbody td strong, .thankYouWrap .order_details tbody td a, .thankYouWrap .order_details tbody td a:visited,
.thankYouWrap .order_details tfoot th, .thankYouWrap .order_details tfoot td,
.thankYouWrap .order_details tbody td .variation dt, .thankYouWrap .order_details tbody td .variation dd,
.customer_details dt,
.customer_details dd,
.thankYouWrap .col2-set.addresses h3,
.thankYouWrap .col2-set.addresses address,
.membershipCardItem h3,
.membershipCard,
.membershipCardItem p {font-family: '<?php echo $sFontNameOne; ?>';}

.woocommerce-breadcrumb,
.woocommerce-breadcrumb a, .woocommerce-breadcrumb a:visited,
.checkoutPage .woocommerce .scell table.shop_table tbody td.product-name,
.checkoutPage .woocommerce .scell table.shop_table tbody td.product-total,
.checkoutPage .woocommerce .scell table.shop_table tbody td .variation dd,
.checkoutPage .woocommerce .scell table.shop_table tbody td .variation dt,
#review_form #commentform p label,
#uni_popup, .contactForm .wpcf7-validation-errors,
.wpcf7-response-output.wpcf7-mail-sent-ok,
.woocommerce .woocommerce-info, .woocommerce .woocommerce-error, .woocommerce .woocommerce-message,
.woocommerce .woocommerce-message .button.wc-forward,
.woocommerce .woocommerce-info a, .woocommerce .woocommerce-info a:visited,
.woocommerce .woocommerce-message a, .woocommerce .woocommerce-message a:visited,
.woocommerce .woocommerce-error li {font-family: '<?php echo $sFontNameOne; ?>';}

/* headings */
.uni-bridallist-title-wrapper .uni-bridallist-title.uni-bridallist-editable button[type="submit"], 
.uni-bridallist-title-wrapper .uni-bridallist-title.uni-bridallist-editable button[type="cancel"],
.mainMenu > ul > li a,
.mainMenu > ul > li a:visited,
.learnMore, .learnMore:visited,
.mainItemDesc .viewMore, .mainItemDesc .viewMore:visited,
.gridItemDesc .viewMore,
.postItemTime,
.classesCategory,
.viewClasses, .viewClasses:visited,
.loadMoreItems, .loadMoreItems:visited,
.showAllItems, .showAllItems:visited,
.pagination ul li a, .pagination ul li a:visited,
.pagination ul li.current, .woocommerce-pagination ul li span.current,
.footerMenu li a, .footerMenu li a:visited,
.singleLinkPages,
.singlePostTags span,
.singlePostWrap .su-quote-cite a,
.singlePostWrap .su-divider a,
.singlePostWrap .su-heading-style-default .su-heading-inner,
.singlePostWrap .su-service-title,
.singlePostWrap .su-spoiler-title,
.singlePostWrap .su-carousel .su-carousel-slide-title,
.singlePostWrap .su-slider-slide-title,
.commentsBox h2, .commentsBox h3,
.commentsBox h3 a, .commentsBox h3 a:visited,
.comment-edit-link, .comment-edit-link:visited, .comment-reply-link, .comment-reply-link:visited,
.logged-in-as a, .logged-in-as a:visited,
.comment-wrapper cite,
.comment-wrapper cite a,
#commentform #submit,
.submitContactFormBtn,
.wpcf7-form input[type="submit"],
.productFilter li a, .productFilter li a:visited,
.miniCartPopupHead h3,
.btnViewCart, .btnViewCart:visited,
.btnCheckout, .btnCheckout:visited,
.eventItemTime,
.eventLearnMore, .eventLearnMore:visited,
.showMoreEvents, .showMoreEvents:visited,
.subscribeBox form input[type="text"],
.subscribeSubmit,
.nextEventBtn, .nextEventBtn:visited,
.singleEventJoinBtnWrap a, .singleEventJoinBtnWrap a:visited,
.submitEventRegistrationBtn,
.woocommerce-cart .wc-proceed-to-checkout .checkout-button,
.updateCartBtn,
.page.woocommerce-cart .return-to-shop a.button,
.page-template-templ-wishlist .return-to-shop a.button,
.button.product_type_, .button.add_to_cart_button,
#place_order,
body .woocommerce form.checkout_coupon p.form-row input.button,
body .woocommerce form.login p.form-row input.button,
body.page.woocommerce-account .shop_table.shop_table_responsive.my_account_orders .button.view,
.page.woocommerce-account form p input.button,
.productDesc form.cart .single_add_to_cart_button,
.page404Wrap a.homePage, .page404Wrap a.homePage:visited,
.membership-card-order, .membership-card-order:visited {font-family: '<?php echo $sFontNameTwo; ?>';}

.woocommerce-pagination ul li a, .woocommerce-pagination ul li a:visited,
.pagination ul li.threeDot,
.coupon input[name="apply_coupon"],
.actions input[name="update_cart"],
.shipping-calculator-form p button,
.single-product .woocommerce-tabs .tabs li a,
.single-product .woocommerce-tabs .tabs li a:visited,
#tab-additional_information h2,
#review_form_wrapper .comment-reply-title,
.single-product .woocommerce-tabs #tab-reviews h2,
.single-product .woocommerce-tabs #tab-description h2,
.woocommerce #tab-reviews #reviews #comments ol.commentlist li .comment-text p.meta time,
#review_form #commentform #submit {font-family: '<?php echo $sFontNameTwo; ?>';}
</style>
<?php } ?>

	</head>
<body <?php body_class(); ?>>
	<header id="header">
		<div class="headerWrap clear">  
			<a href="<?php if ( function_exists('icl_get_languages') ) { echo esc_url( icl_get_home_url() ); } else { echo esc_url( home_url() ); } ?>" class="logo">
            <?php
            $sLogoType = ot_get_option( 'uni_logo_type' );
            if ( empty($sLogoType) || $sLogoType == 'off' ) {
                if ( ot_get_option( 'uni_logo_a' ) ) {
                    $iLogoAAttachId = ot_get_option( 'uni_logo_a' );
                    $aLogoImageA = wp_get_attachment_image_src( $iLogoAAttachId, 'full' );
                    $sLogoImageA = $aLogoImageA[0];
                    echo '<img class="logo-black" src="'.esc_url($sLogoImageA).'" alt="'.esc_attr(get_bloginfo('description')).'">';
                } else {
                    echo '<img class="logo-black" src="'.get_template_directory_uri().'/images/logo-black.png" alt="'.esc_attr(get_bloginfo('description')).'">';
                }
                if ( ot_get_option( 'uni_logo_b' ) ) {
                    $iLogoBAttachId = ot_get_option( 'uni_logo_b' );
                    $aLogoImageB = wp_get_attachment_image_src( $iLogoBAttachId, 'full' );
                    $sLogoImageB = $aLogoImageB[0];
                    echo '<img class="logo-white" src="'.esc_url($sLogoImageB).'" alt="'.esc_attr(get_bloginfo('description')).'">';
                } else {
                    echo '<img class="logo-white" src="'.get_template_directory_uri().'/images/logo-white.png" alt="'.esc_attr(get_bloginfo('description')).'">';
                }
            } else if ( $sLogoType == 'on' ) {
                if ( ot_get_option( 'uni_logo_svg' ) ) {
                    echo ot_get_option( 'uni_logo_svg' );
                } else {
                ?>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="122px" height="32px" viewBox="0 0 122 32" enable-background="new 0 0 122 32" xml:space="preserve">
					<path fill="#333333" d="M31.426 11.797c-0.161-0.59-0.355-1.173-0.589-1.735c-0.449-1.152-1.045-2.225-1.748-3.223 c-0.442-0.643-0.938-1.245-1.467-1.808C24.695 1.9 20.6 0 16 0C11.413 0 7.3 1.9 4.4 5 C3.832 5.6 3.3 6.2 2.9 6.839c-0.703 0.998-1.292 2.07-1.748 3.223c-0.234 0.563-0.422 1.146-0.583 1.7 C0.188 13.1 0 14.5 0 15.997c0 0.7 0 1.4 0.1 2.109c0.014 0.1 0 0.2 0.1 0.4 c0.06 0.4 0.1 0.9 0.3 1.348c0.013 0 0 0.1 0 0.107h31.004c0.014-0.041 0.014-0.067 0.02-0.107 c0.121-0.436 0.214-0.885 0.275-1.341c0.02-0.12 0.04-0.241 0.047-0.361c0.114-0.697 0.154-1.4 0.154-2.109 C31.989 14.5 31.8 13.1 31.4 11.797z M1.983 18.106C1.909 17.7 1.9 17.3 1.8 16.9 c-0.02-0.295-0.034-0.603-0.034-0.903c0-0.302 0.014-0.616 0.034-0.904c0.067-1.119 0.268-2.197 0.569-3.223 c0.201-0.623 0.435-1.226 0.703-1.809C3.65 8.9 4.3 7.8 5.2 6.839c0.563-0.663 1.172-1.266 1.842-1.808 c2.438-2.017 5.573-3.223 8.982-3.223c3.396 0 6.5 1.2 9 3.223c0.676 0.5 1.3 1.1 1.8 1.8 c0.824 1 1.5 2.1 2.1 3.223c0.274 0.6 0.5 1.2 0.7 1.809c0.302 1 0.5 2.1 0.6 3.2 c0.02 0.3 0 0.6 0 0.904c0 0.301-0.007 0.608-0.027 0.903c-0.034 0.409-0.074 0.811-0.141 1.206H1.983z M8.607 30.2 C10.824 31.3 13.3 32 16 32c2.666 0 5.17-0.671 7.381-1.822H8.607z M30.75 22.133c-0.254 0.623-0.542 1.239-0.884 1.8 H2.124c-0.342-0.563-0.636-1.179-0.891-1.802H30.75z M28.338 26.159c-0.228 0.281-0.469 0.535-0.71 0.8 c-0.328 0.348-0.669 0.683-1.031 1.004H5.392c-0.362-0.321-0.704-0.656-1.038-1.004c-0.241-0.269-0.482-0.522-0.704-0.804H28.338z M55.652 23.104l-1.306-3.034h-5.894l-1.306 3.034h-3.342l6.075-14.061h3.041l6.075 14.061H55.652z M51.406 13.23l-1.768 4.1 h3.523L51.406 13.23z M64.814 11.851c-0.295 0.247-0.455 0.575-0.455 0.984c0 0.4 0.2 0.7 0.6 1 c0.361 0.2 1.2 0.5 2.5 0.852c1.34 0.3 2.4 0.8 3.1 1.479c0.75 0.7 1.1 1.6 1.1 2.9 c0 1.261-0.469 2.285-1.42 3.069c-0.951 0.783-2.184 1.172-3.737 1.172c-2.224 0-4.233-0.824-6.008-2.472l1.868-2.292 c1.514 1.3 2.9 2 4.2 1.99c0.575 0 1.031-0.12 1.353-0.375c0.335-0.247 0.509-0.582 0.509-1.005s-0.188-0.757-0.535-1.005 c-0.349-0.248-1.045-0.503-2.063-0.757c-1.634-0.389-2.84-0.891-3.59-1.514c-0.764-0.623-1.146-1.608-1.146-2.94 c0-1.333 0.482-2.365 1.44-3.089c0.964-0.723 2.156-1.085 3.59-1.085c0.938 0 1.9 0.2 2.8 0.5 c0.938 0.3 1.8 0.8 2.5 1.366l-1.594 2.298c-1.219-0.931-2.479-1.393-3.791-1.393 C65.538 11.5 65.1 11.6 64.8 11.851z M85.229 23.104l-1.313-3.034h-5.895l-1.313 3.034h-3.336l6.082-14.061h3.041 l6.068 14.061H85.229z M80.983 13.23l-1.782 4.079h3.537L80.983 13.23z M101.036 9.043h3.135v14.061h-3.135l-6.697-8.809v8.809 h-3.135V9.043h2.934l6.898 9.057V9.043z M118.665 23.104l-1.313-3.034h-5.894l-1.313 3.034h-3.322l6.068-14.061h3.04L122 23.1 H118.665z M114.418 13.23l-1.768 4.079h3.522L114.418 13.23z"/>
				</svg>
                <?php } ?>
            <?php } ?>
			</a>

			<?php wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'mainMenu', 'menu_class' => 'clear', 'theme_location' => 'primary', 'depth' => 3, 'fallback_cb'=> 'uni_nav_fallback' ) ); ?>

			<span class="showMobileMenu">
				<span></span>
				<span></span>
				<span></span>
				<span></span>
			</span>
		</div>
	</header>