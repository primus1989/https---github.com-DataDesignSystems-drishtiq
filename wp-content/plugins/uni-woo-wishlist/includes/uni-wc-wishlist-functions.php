<?php
// displays wishlist link
function uni_wc_wishlist_link() {
    echo UniWishlist()->wishlist_link();
}

// gets wishlist items
function uni_wc_wishlist_items( $iUserId = null ) {
    return UniWishlist()->wishlist_get_items( $iUserId );
}

// counts wishlist items
function uni_wc_wishlist_count_items( $iUserId = null ) {
    return UniWishlist()->wishlist_count_items( $iUserId );
}

// link position
add_action('init', 'uni_wc_wishlist_link_position');
function uni_wc_wishlist_link_position(){
    $sChosenPosition = get_option('uni_wishlist_link_position');
    $aHooks = UniWishlist()->woo_hooks();
    if ( !empty($aHooks) && !empty($sChosenPosition) ) {
        foreach ( $aHooks as $sOptionName => $aHook ) {
            if ( $sOptionName == $sChosenPosition && $sOptionName != 'none' ) {
                add_action( $aHook['name'], 'uni_wc_wishlist_link', $aHook['priority'] );
                add_action( $aHook['name'], 'uni_wc_bridallist_link', $aHook['priority'] );
            }
        }
    }
}

//************

// displays bridallist link
function uni_wc_bridallist_link() {
    echo UniWishlist()->bridallist_link();
}

// gets bridallist items
function uni_wc_bridallist_items( $iUserId = null ) {
    return UniWishlist()->bridallist_get_items( $iUserId );
}

// counts bridallist items
function uni_wc_bridallist_count_items( $iUserId = null ) {
    return UniWishlist()->bridallist_count_items( $iUserId );
}

// gets bridal list title
function uni_wc_bridallist_title( $iUserId = null ) {
    $iCurrentUserId = get_current_user_id();

    if ( !empty($iUserId) && $iUserId != null && $iCurrentUserId != $iUserId ) {
        $sTitle = get_user_meta( $iUserId, '_uni_wc_bridallist_title', true );

        if ( !empty($sTitle) ) {
            return $sTitle;
        } else if ( empty($sTitle) ) {
            return sprintf( __('Bridal list of user #%d', 'uni-wishlist'), $iUserId );
        }
    } else if ( !empty($iUserId) && $iUserId != null && $iCurrentUserId != 0 && $iCurrentUserId == $iUserId ) {
        $sTitle = get_user_meta( $iUserId, '_uni_wc_bridallist_title', true );

        if ( !empty($sTitle) ) {
            return $sTitle;
        } else {
            return sprintf( __('You haven\'t added title yet. Click on the edit icon next to this field to edit it.', 'uni-wishlist'), $iUserId );
        }
    } else if ( $iUserId == null || empty($iUserId) ) {
        $sTitle = get_user_meta( $iCurrentUserId, '_uni_wc_bridallist_title', true );
        return $sTitle;
    }
}

// adds cart item meta with an user ID whose bridal list was redirected from
add_filter( 'woocommerce_add_cart_item_data', 'uni_bridallist_add_cart_item_data', 10, 2 );
function uni_bridallist_add_cart_item_data($cart_item_meta, $iProductId) {
    if ( isset($_GET['bridallist_id']) && !empty($_GET['bridallist_id']) ) {
        $cart_item_meta['_uni_bridallist_item_for_user'] = $_GET['bridallist_id'];
    }
    if ( isset($_POST['uni_bridallist_item_for_user']) && !empty($_POST['uni_bridallist_item_for_user']) ) {
        $cart_item_meta['_uni_bridallist_item_for_user'] = $_POST['uni_bridallist_item_for_user'];
    }
    return $cart_item_meta;
}

//
add_filter('woocommerce_get_item_data', 'uni_bridallist_get_item_data', 10, 2);
function uni_bridallist_get_item_data($other_data, $cart_item) {
    $sAttrCartValue = ( isset($cart_item['_uni_bridallist_item_for_user']) && !empty($cart_item['_uni_bridallist_item_for_user']) ) ? $cart_item['_uni_bridallist_item_for_user'] : '';
    $other_data[] = array('name' => __('This product is added from bridal list: #', 'uni-wishlist'), 'value' => $sAttrCartValue);

    return $other_data;
}

// adds the same meta to the order meta data
add_action( 'woocommerce_add_order_item_meta', 'uni_bridallist_add_order_item_data', 10, 3 );
function uni_bridallist_add_order_item_data($item_id, $values, $cart_item_key) {
    if ( isset($values['_uni_bridallist_item_for_user']) ) {
        woocommerce_add_order_item_meta( $item_id, '_product_from_bridal_list', $values['_uni_bridallist_item_for_user'] );
    }
}

// marks as bought
add_action( 'woocommerce_order_status_completed', 'uni_wishlist_if_bridal_item_bought_check');
function uni_wishlist_if_bridal_item_bought_check( $iOrderId ){
    $oOrder = new WC_Order( $iOrderId );
    $aItems = $oOrder->get_items();
    foreach ( $aItems as $iItemId => $aItemData ) {
        if ( isset($aItemData['product_from_bridal_list']) && !empty($aItemData['product_from_bridal_list']) && !empty($aItemData['variation_id']) ) {
            UniWishlist()->change_item_bought_status( $aItemData['product_from_bridal_list'], $aItemData['product_id'], true, $aItemData['variation_id'] );
        } else if ( isset($aItemData['product_from_bridal_list']) && !empty($aItemData['product_from_bridal_list']) && empty($aItemData['variation_id']) ) {
            UniWishlist()->change_item_bought_status( $aItemData['product_from_bridal_list'], $aItemData['product_id'], true );
        }
    }
}

// unmarks as bought
add_action( 'woocommerce_order_status_pending', 'uni_wishlist_if_bridal_item_not_bought_check');
add_action( 'woocommerce_order_status_failed', 'uni_wishlist_if_bridal_item_not_bought_check');
add_action( 'woocommerce_order_status_on-hold', 'uni_wishlist_if_bridal_item_not_bought_check');
add_action( 'woocommerce_order_status_processing', 'uni_wishlist_if_bridal_item_not_bought_check');
add_action( 'woocommerce_order_status_refunded', 'uni_wishlist_if_bridal_item_not_bought_check');
add_action( 'woocommerce_order_status_cancelled', 'uni_wishlist_if_bridal_item_not_bought_check');
function uni_wishlist_if_bridal_item_not_bought_check( $iOrderId ){
    $oOrder = new WC_Order( $iOrderId );
    $aItems = $oOrder->get_items();
    foreach ( $aItems as $iItemId => $aItemData ) {
        if ( isset($aItemData['product_from_bridal_list']) && !empty($aItemData['product_from_bridal_list']) && !empty($aItemData['variation_id']) ) {
            UniWishlist()->change_item_bought_status( $aItemData['product_from_bridal_list'], $aItemData['product_id'], false, $aItemData['variation_id'] );
        } else if ( isset($aItemData['product_from_bridal_list']) && !empty($aItemData['product_from_bridal_list']) && empty($aItemData['variation_id']) ) {
            UniWishlist()->change_item_bought_status( $aItemData['product_from_bridal_list'], $aItemData['product_id'], false );
        }
    }
}

//*******************

// wishlist link predefined styles
add_filter( 'uni_wc_wishlist_link_title', 'uni_predefined_wishlist_link_style', 10, 2 );
function uni_predefined_wishlist_link_style( $sTitle, $sState ) {
    if ( get_option('uni_wishlist_style') != 'default' ) {
        if ( $sState == 'not-added' ) {
            if ( get_option('uni_wishlist_style') != 'custom' ) {
                if ( get_option('uni_wishlist_style') == 'heart-and-gift' ) {
                    return '<i class="fa fa-heart-o"></i>'.$sTitle;
                } else if ( get_option('uni_wishlist_style') == 'star-and-gift' ) {   
                    return '<i class="fa fa-star-o"></i>'.$sTitle;
                } else if ( get_option('uni_wishlist_style') == 'heart-and-venus-mars' ) {
                    return '<i class="fa fa-heart-o"></i>'.$sTitle;
                }
            } else {
                return '<i class="fa '.get_option('uni_wishlist_fa_tag_na').'"></i>'.$sTitle;
            }
        } else if ( $sState == 'added' ) {
            if ( get_option('uni_wishlist_style') != 'custom' ) {
                if ( get_option('uni_wishlist_style') == 'heart-and-gift' ) {
                    return '<i class="fa fa-heart"></i>'.$sTitle;
                } else if ( get_option('uni_wishlist_style') == 'star-and-gift' ) {
                    return '<i class="fa fa-star"></i>'.$sTitle;
                } else if ( get_option('uni_wishlist_style') == 'heart-and-venus-mars' ) {
                    return '<i class="fa fa-heart"></i>'.$sTitle;
                }
            } else {
                return '<i class="fa '.get_option('uni_wishlist_fa_tag').'"></i>'.$sTitle;
            }
        }
    } else {
        return $sTitle;
    }
}

// bridallist link predefined styles
add_filter( 'uni_wc_bridallist_link_title', 'uni_predefined_bridallist_link_style', 10, 2 );
function uni_predefined_bridallist_link_style( $sTitle, $sState ) {
    if ( get_option('uni_wishlist_style') != 'default' ) {
        if ( $sState == 'not-added' ) {
            if ( get_option('uni_wishlist_style') != 'custom' ) {
                if ( get_option('uni_wishlist_style') == 'heart-and-gift' ) {
                    return '<i class="fa fa-gift"></i>'.$sTitle;
                } else if ( get_option('uni_wishlist_style') == 'star-and-gift' ) {
                    return '<i class="fa fa-gift"></i>'.$sTitle;
                } else if ( get_option('uni_wishlist_style') == 'heart-and-venus-mars' ) {
                    return '<i class="fa fa-venus-mars"></i>'.$sTitle;
                }
            } else {
                return '<i class="fa '.get_option('uni_bridallist_fa_tag_na').'"></i>'.$sTitle;
            }
        } else if ( $sState == 'added' ) {
            if ( get_option('uni_wishlist_style') != 'custom' ) {
                if ( get_option('uni_wishlist_style') == 'heart-and-gift' ) {
                    return '<i class="fa fa-gift"></i>'.$sTitle;
                } else if ( get_option('uni_wishlist_style') == 'star-and-gift' ) {
                    return '<i class="fa fa-gift"></i>'.$sTitle;
                } else if ( get_option('uni_wishlist_style') == 'heart-and-venus-mars' ) {
                    return '<i class="fa fa-venus-mars"></i>'.$sTitle;
                }
            } else {
                return '<i class="fa '.get_option('uni_bridallist_fa_tag').'"></i>'.$sTitle;
            }
        }
    } else {
        return $sTitle;
    }
}

?>