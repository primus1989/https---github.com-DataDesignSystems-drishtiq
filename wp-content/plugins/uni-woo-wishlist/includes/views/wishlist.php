<div class="woocommerce">
<?php wc_print_notices(); ?>
</div>

<?php do_action( 'uni_wishlist_before_table_action' ); ?>

<table class="uni-wishlist-table" cellspacing="0">
	<thead>
		<tr>
			<th class="product-name">&nbsp;</th>
			<th class="product-price">&nbsp;</th>
			<th class="product-add-to-cart">&nbsp;</th>
            <th class="product-remove">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
    <?php
    $i = 1;
    foreach ( $aUsersWishlist as $iProductId => $aWshlistItemData ) {
        if ( $aWshlistItemData['type'] == 'variable' && !empty($aWshlistItemData['vid']) ) {
            foreach ( $aWshlistItemData['vid'] as $iVariableProductId ) {
                $oProduct = new WC_Product_Variation( $iVariableProductId, $iProductId );
                if ( $oProduct->exists() ) {
                    include( UniWishlist()->plugin_path().'/includes/views/item-variation-table-row.php' );
                }
            }
        } else {
            $oProduct = new WC_Product( $iProductId );
            if ( $oProduct->exists() ) {
                include( UniWishlist()->plugin_path().'/includes/views/item-table-row.php' );
            }
        }
        $i++;
    } ?>
    </tbody>
</table>

<?php do_action( 'uni_wishlist_after_table_action' ); ?>