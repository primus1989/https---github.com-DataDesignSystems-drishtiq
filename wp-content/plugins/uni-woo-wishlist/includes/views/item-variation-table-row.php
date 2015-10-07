<?php
$iProductId = $oProduct->id;
$iProductVariationId = $oProduct->variation_id;
$aVarAttrs = $oProduct->get_variation_attributes();
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
                    <div class="uni-wishlist-variation-details">
                        <?php echo wc_get_formatted_variation( $aVarAttrs ); ?>
                    </div>
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
                <?php if ( $oProduct->is_in_stock() ) :

echo apply_filters( 'woocommerce_loop_add_to_cart_link',
	sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s">%s</a>',
		esc_url( $oProduct->add_to_cart_url() ),
		esc_attr( $oProduct->id ),
		esc_attr( $oProduct->get_sku() ),
		esc_attr( 1 ),
		$oProduct->is_purchasable() && $oProduct->is_in_stock() ? 'add_to_cart_button' : '',
		esc_attr( $oProduct->product_type ),
		esc_html( $oProduct->add_to_cart_text() )
	),
$oProduct );

                endif; ?>
            </td>
            <td>
                <?php
                $sRemoveItemTitle = apply_filters( 'uni_wc_wishlist_table_remove_item_link', __('Delete', 'uni-wishlist'), 'not-variable' );
                echo '<a href="#" class="uni-wishlist-table-remove-link" data-pid="'.$iProductId.'" data-vid="'.$iProductVariationId.'" data-action="uni_wishlist_delete_from_list">'.$sRemoveItemTitle.'</a>';
                ?>
            </td>
        </tr>