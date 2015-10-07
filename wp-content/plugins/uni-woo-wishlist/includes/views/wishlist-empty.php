<p class="uni-wishlist-empty"><?php _e( 'Your wishlist is currently empty.', 'uni-wishlist' ) ?></p>

<?php do_action( 'uni_wishlist_is_empty_action' ); ?>

<p class="return-to-shop"><a class="button wc-backward" href="<?php echo apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ); ?>"><?php _e( 'Return To Shop', 'uni-wishlist' ) ?></a></p>
