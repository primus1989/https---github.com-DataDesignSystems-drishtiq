<?php do_action( 'uni_bridallist_before_table_action' ); ?>

<div class="uni-bridallist-title-wrapper">
    <h3 class="uni-bridallist-title"><?php echo uni_wc_bridallist_title( null ); ?></h3>
</div>

<p><?php echo sprintf( __('Bridal lists are available for registered users only. <a href="%s">Register now!</a>', 'uni-wishlist'), get_permalink( get_page_by_path('my-account')->ID ) ) ?></p>

<p class="return-to-shop"><a class="button wc-backward" href="<?php echo apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ); ?>"><?php _e( 'Return To Shop', 'uni-wishlist' ) ?></a></p>

<?php do_action( 'uni_bridallist_after_table_action' ); ?>