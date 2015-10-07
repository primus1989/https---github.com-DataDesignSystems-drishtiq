<div class="woocommerce">
<?php wc_print_notices(); ?>
</div>

<?php do_action( 'uni_bridallist_before_table_action' ); ?>
<?php
$iUserId = get_current_user_id();
?>

<div class="uni-bridallist-title-wrapper">
    <i class="fa fa-pencil-square-o"></i>
    <h3 class="uni-bridallist-title uni-bridallist-editable" data-id="<?php echo esc_attr( $iUserId ); ?>"><?php echo uni_wc_bridallist_title( $iUserId ); ?></h3>
    <div style="clear:both;"></div>
    <span>
        <?php
        if ( get_option('permalink_structure') ) {
            global $post;
            $sCurrentListUri = untrailingslashit( get_permalink( $post->ID ) ) . '/list-id/' . $iUserId;
        } else {
            global $post;
            $sCurrentListUri = add_query_arg( array( 'list-id' => $iUserId ), untrailingslashit( get_permalink( $post->ID ) ) );
        }
        $sCurrentListLink = sprintf( '<a href="%s">%s</a>', $sCurrentListUri, $sCurrentListUri );
        echo sprintf( __('The URI of this list is %s. You can give this link to your guests.', 'uni-wishlist'), $sCurrentListLink); ?>
    </span>
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
            <th class="product-remove">&nbsp;</th>
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
                    include( UniWishlist()->plugin_path().'/includes/views/bridalitem-variation-table-row-my.php' );
                }
            }
        } else {
            $oProduct = new WC_Product( $iProductId );
            if ( $oProduct->exists() ) {
                include( UniWishlist()->plugin_path().'/includes/views/bridalitem-table-row-my.php' );
            }
        }
        $i++;
    }
    } else {
    ?>
        <tr><td colspan="4"><?php _e('The bridal list is empty.', 'uni-wishlist'); ?></td></tr>
    <?php
    } ?>
    </tbody>
</table>

<?php do_action( 'uni_bridallist_after_table_action' ); ?>