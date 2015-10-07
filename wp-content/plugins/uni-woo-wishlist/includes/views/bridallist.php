<div class="woocommerce">
<?php wc_print_notices(); ?>
</div>

<?php do_action( 'uni_bridallist_before_table_action' ); ?>
<?php
global $wp_query;
$iUserId = absint( $wp_query->query_vars['list-id'] );
?>

<div class="uni-bridallist-title-wrapper">
    <h3 class="uni-bridallist-title"><?php echo uni_wc_bridallist_title( $iUserId ); ?></h3>
</div>

    <?php
    $aProducts = array();
    $aUsersBridallist = uni_wc_bridallist_items( $iUserId );
    if ( !empty($aUsersBridallist) ) {
        foreach( $aUsersBridallist as $iProductId => $aProductData ) {
            $aProducts[] = $iProductId;
        }
        echo '<input type="hidden" name="uni_bridallist_item_for_user" value="'.$iUserId.'" />';
    }
    ?>

<table class="uni-wishlist-table uni-bridallist-table" cellspacing="0">
	<thead>
		<tr>
			<th class="product-name">&nbsp;</th>
			<th class="product-price">&nbsp;</th>
			<th class="product-add-to-cart">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
    <?php
    if ( !empty($aUsersBridallist) ) {
    $i = 1;
    foreach ( $aUsersBridallist as $iProductId => $aBridallistItemData ) {
        if ( $aBridallistItemData['type'] == 'variable' && !empty($aBridallistItemData['variations']) ) {
            foreach ( $aBridallistItemData['variations'] as $iVariableProductId => $bIsVariableProductBought ) {
                $oProduct = new WC_Product_Variation( $iVariableProductId, $iProductId );
                if ( $oProduct->exists() ) {
                    include( UniWishlist()->plugin_path().'/includes/views/bridalitem-variation-table-row.php' );
                }
            }
        } else {
            $oProduct = new WC_Product( $iProductId );
            if ( $oProduct->exists() ) {
                include( UniWishlist()->plugin_path().'/includes/views/bridalitem-table-row.php' );
            }
        }
        $i++;
    }
    } else {
    ?>
        <tr><td colspan="3"><?php _e('The bridal list is empty.', 'uni-wishlist'); ?></td></tr>
    <?php
    } ?>
    </tbody>
</table>

<?php do_action( 'uni_bridallist_after_table_action' ); ?>