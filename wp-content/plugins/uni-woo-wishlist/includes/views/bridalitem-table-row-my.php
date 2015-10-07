<?php
$iProductId = $oProduct->id;
$aProductCustom = get_post_custom( $oProduct->id );
?>
        <tr class="uni-wishlist-table-row<?php if ( $i == 1 ) { echo ' row-odd'; } else { echo ' row-even'; } ?>">
            <td>
			    <div class="uni-wishlist-item-image">
                    <?php echo $oProduct->get_image( apply_filters( 'uni_wc_wishlist_table_image_size', 'shop_thumbnail' ) ) ?>
                </div>
                <div class="uni-wishlist-item-details">
                    <h4 class="uni-wishlist-item-title">
                    <?php if ( ! $oProduct->is_visible() ) { ?>
                        <?php echo get_the_title($oProduct->id) ?>
                    <?php } else { ?>
				        <a href="<?php echo get_permalink($oProduct->id) ?>">
					        <?php echo get_the_title($oProduct->id) ?>
					    </a>
                    <?php } ?>
                    </h4>
                    <div class="uni-wishlist-item-availability">
					<?php
					    $aAvailability = $oProduct->get_availability();
						echo '<span class="uni-wishlist-item-avail-'.$aAvailability['class'].'">'.$aAvailability['availability'].'</span>';
					?>
                    </div>
                </div>
            </td>
            <td>
                <?php echo $oProduct->get_price_html() ?>
            </td>
            <td>
                <?php
                if ( empty($aBridallistItemData['is_bought']) ) {
                if ( $oProduct->is_in_stock() ) :

if( !empty($aProductCustom['_uni_cpo_enable_custom_options_calc'][0]) && $aProductCustom['_uni_cpo_enable_custom_options_calc'][0] == 'yes' ) {
        echo sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button product_type_%s">%s</a>',
            esc_url( get_permalink( $oProduct->id ) ),
            esc_attr( $oProduct->id ),
            esc_attr( $oProduct->get_sku() ),
            esc_attr( 1 ),
            esc_attr( $oProduct->product_type ),
            esc_html( __( 'Select options', 'woocommerce' ) )
        );
} else {
        echo apply_filters( 'woocommerce_loop_add_to_cart_link',
	        sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s">%s</a>',
		        esc_url( $oProduct->add_to_cart_url() ),
		        esc_attr( $oProduct->id ),
		        esc_attr( $oProduct->get_sku() ),
		        esc_attr( 1 ),
		        $oProduct->is_purchasable() && $oProduct->is_in_stock() ? 'add_to_cart_button' : '',
		        esc_attr( 'simple' ),
		        esc_html( $oProduct->is_purchasable() && $oProduct->is_in_stock() ? __('Add to Cart', 'uni-wishlist') : __('Read More', 'uni-wishlist') )
	        ),
        $oProduct );
}

                endif;
                } else {
                    _e('Already bought', 'uni-wishlist');
                }
                ?>
            </td>
            <td>
                <?php
                if ( empty($aBridallistItemData['is_bought']) ) {
                    $sRemoveItemTitle = apply_filters( 'uni_wc_wishlist_table_remove_item_link', __('Delete', 'uni-wishlist'), 'not-variable' );
                    echo '<a href="#" class="uni-wishlist-table-remove-link" data-pid="'.$iProductId.'" data-action="uni_bridallist_delete_from_list">'.$sRemoveItemTitle.'</a>';
                }
                ?>
            </td>
        </tr>