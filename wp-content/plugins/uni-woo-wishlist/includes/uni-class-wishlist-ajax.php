<?php
/*
*   Class UniWishlistAjax
*
*/

if ( !class_exists( 'UniWishlistAjax' ) ) {

class UniWishlistAjax {

    protected $sNonceInputName      = 'uni_auth_nonce';
    protected $sNonce               = 'uni_authenticate_nonce';

	/**
	*  Construct
	*/
	public function __construct() {
        $this->_init();
	}

	/**
	*   Ajax
	*/
	protected function _init() {

        $aAjaxEvents = array(
                    'uni_wishlist_add' => true,
                    'uni_wishlist_delete' => true,
                    'uni_wc_wishlist_variation_chosen' => true,
                    'uni_wishlist_delete_from_list' => true,
                    'uni_bridallist_add_to_cart' => true,
                    'uni_bridallist_add' => false,
                    'uni_bridallist_delete' => false,
                    'uni_wc_wishlist_bridal_title_inline_edit' => false,
                    'uni_bridallist_delete_from_list' => false

        );

		foreach ( $aAjaxEvents as $sAjaxEvent => $bPriv ) {
			add_action( 'wp_ajax_' . $sAjaxEvent, array(&$this, $sAjaxEvent) );

			if ( $bPriv ) {
				add_action( 'wp_ajax_nopriv_' . $sAjaxEvent, array(&$this, $sAjaxEvent) );
			}
		}

	}

	/**
	*   _r()
    */
    protected function _r() {
        $aResult = array(
		    'status' 	=> 'error',
			'message' 	=> __('Error!', 'uni-calendar'),
			'redirect'	=> ''
		);
        return $aResult;
    }

	/**
	*   _valid_email
    */
	protected function _valid_email( $email ) {
			$regex_pattern	= '/^[_a-zA-Z0-9-]+(\.[_A-Za-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';
			$validation 	= preg_match($regex_pattern, $email);
			if ( ! empty( $email ) && $validation ) {
				return true;
			} else {
				return false;
			}
    }

	/**
	*   _auth
    */
	protected function _auth( $user_id, $cookie = true ) {
			wp_set_current_user($user_id);
			if ( $cookie ) {
				wp_set_auth_cookie($user_id, true);
			}
    }

    /*
    *  uni_wc_wishlist_variation_chosen
    */
    function uni_wc_wishlist_variation_chosen() {

        $aResult        = $this->_r();
        $iProductId     = ( isset($_POST['pid']) && !empty($_POST['pid']) ) ? (int)$_POST['pid'] : '';
        $iVariationId   = ( isset($_POST['vid']) && !empty($_POST['vid']) ) ? (int)$_POST['vid'] : '';
        $bIsInWishlist  = false;

        if ( isset($iProductId) && !empty($iProductId) && isset($iVariationId) && !empty($iVariationId) ) {

            // wish list link
            $bIsInWishlist = UniWishlist()->is_in_wishlist( $iProductId, $iVariationId );

            if ( $bIsInWishlist ) {
                $aResult['status']      = 'success';
                $aResult['output'] = UniWishlist()->wishlist_link_raw( $iProductId, $iVariationId, 'added', 'variable' );
            } else {
                $aResult['status']      = 'success';
                $aResult['output'] = UniWishlist()->wishlist_link_raw( $iProductId, $iVariationId, 'not-added', 'variable' );
            }

            // bridal list link
            if ( get_option('uni_bridallist_enable') && is_user_logged_in() ) {
                $bIsInBridallist = UniWishlist()->is_in_bridallist( $iProductId, $iVariationId );

                if ( $bIsInBridallist ) {
                    $aResult['status']      = 'success';
                    $aResult['output_bridal'] = UniWishlist()->bridallist_link_raw( $iProductId, $iVariationId, 'added', 'variable' );
                } else {
                    $aResult['status']      = 'success';
                    $aResult['output_bridal'] = UniWishlist()->bridallist_link_raw( $iProductId, $iVariationId, 'not-added', 'variable' );
                }
            }

        } else {
            $aResult['message'] = __('Error: the product and variation IDs are not defined!', 'uni-wishlist');
        }

        wp_send_json( $aResult );
    }

    /*
    *  uni_wishlist_add
    */
    function uni_wishlist_add() {

        $aResult        = $this->_r();
        $iProductId     = ( isset($_POST['pid']) && !empty($_POST['pid']) ) ? (int)$_POST['pid'] : '';
        $iVariationId   = ( isset($_POST['vid']) && !empty($_POST['vid']) ) ? (int)$_POST['vid'] : null;
        $bIsInWishlist  = false;

        if ( isset($iProductId) && !empty($iProductId) ) {

            UniWishlist()->wishlist_add( $iProductId, $iVariationId );

            $bIsInWishlist = UniWishlist()->is_in_wishlist( $iProductId, $iVariationId );

            if ( $bIsInWishlist && $iVariationId == null ) {
                $aResult['output'] = UniWishlist()->wishlist_link_raw( $iProductId, null, 'added', 'not-variable' );
            } else if ( $bIsInWishlist && $iVariationId != null ) {
                $aResult['output'] = UniWishlist()->wishlist_link_raw( $iProductId, $iVariationId, 'added', 'variable' );
            }

            if ( $bIsInWishlist ) {
                $aResult['status']      = 'success';
                $aResult['message']     = __('Successfully added to your Wish List!', 'uni-wishlist');
            } else {
                $aResult['message'] = __('Error: unknown reason', 'uni-wishlist');
            }

        } else {
            $aResult['message'] = __('Error: the product ID is not defined!', 'uni-wishlist');
        }

        wp_send_json( $aResult );
    }

    /*
    *  uni_wishlist_delete
    */
    function uni_wishlist_delete() {

        $aResult        = $this->_r();
        $iProductId     = ( isset($_POST['pid']) && !empty($_POST['pid']) ) ? (int)$_POST['pid'] : '';
        $iVariationId   = ( isset($_POST['vid']) && !empty($_POST['vid']) ) ? (int)$_POST['vid'] : null;
        $bIsInWishlist  = false;

        if ( isset($iProductId) && !empty($iProductId) ) {

            UniWishlist()->wishlist_delete( $iProductId, $iVariationId );

            $bIsInWishlist = UniWishlist()->is_in_wishlist( $iProductId, $iVariationId );

            if ( !$bIsInWishlist && $iVariationId == null ) {
                $aResult['output'] = UniWishlist()->wishlist_link_raw( $iProductId, null, 'not-added', 'not-variable' );
            } else if ( !$bIsInWishlist && $iVariationId != null ) {
                $aResult['output'] = UniWishlist()->wishlist_link_raw( $iProductId, $iVariationId, 'not-added', 'variable' );
            }

            if ( !$bIsInWishlist ) {
                $aResult['status']      = 'success';
                $aResult['message']     = __('Successfully deleted from your Wish List!', 'uni-wishlist');
            } else {
                $aResult['message'] = __('Error: unknown reason', 'uni-wishlist');
            }

        } else {
            $aResult['message'] = __('Error: the product ID is not defined!', 'uni-wishlist');
        }

        wp_send_json( $aResult );
    }

    /*
    *  uni_wishlist_delete_from_list
    */
    function uni_wishlist_delete_from_list() {

        $aResult        = $this->_r();
        $iProductId     = ( isset($_POST['pid']) && !empty($_POST['pid']) ) ? (int)$_POST['pid'] : '';
        $iVariationId   = ( isset($_POST['vid']) && !empty($_POST['vid']) ) ? (int)$_POST['vid'] : null;
        $bIsInWishlist  = false;

        if ( isset($iProductId) && !empty($iProductId) ) {

            UniWishlist()->wishlist_delete( $iProductId, $iVariationId );

            $bIsInWishlist = UniWishlist()->is_in_wishlist( $iProductId, $iVariationId );

            if ( !$bIsInWishlist ) {
                $aResult['status']      = 'success';
                $aResult['message']     = __('Successfully deleted from your Wish List!', 'uni-wishlist');
            } else {
                $aResult['message'] = __('Error: unknown reason', 'uni-wishlist');
            }

        } else {
            $aResult['message'] = __('Error: the product ID is not defined!', 'uni-wishlist');
        }

        wp_send_json( $aResult );
    }

    //*************

	/**
	 * uni_bridallist_add_to_cart
	 */
	public static function uni_bridallist_add_to_cart() {
		ob_start();

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );

        if ( isset($_POST['uni_bridallist_item_for_user']) && !empty($_POST['uni_bridallist_item_for_user']) ) {
            add_filter('woocommerce_add_cart_item_data', 'uni_bridallist_add_cart_item_data', 10, 2);
        }

		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
				wc_add_to_cart_message( $product_id );
			}

			// Return fragments
			self::get_refreshed_fragments();

		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
			);

			wp_send_json( $data );

		}

		die();
	}

	/**
	 * Get a refreshed cart fragment
	 */
	public static function get_refreshed_fragments() {

		// Get mini cart
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		// Fragments and mini cart are returned
		$data = array(
			'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart() ? md5( json_encode( WC()->cart->get_cart() ) ) : '', WC()->cart->get_cart() )
		);

		wp_send_json( $data );

	}

    /*
    *  uni_bridallist_add
    */
    function uni_bridallist_add() {

        $aResult        = $this->_r();
        $iProductId     = ( isset($_POST['pid']) && !empty($_POST['pid']) ) ? (int)$_POST['pid'] : '';
        $iVariationId   = ( isset($_POST['vid']) && !empty($_POST['vid']) ) ? (int)$_POST['vid'] : null;
        $bIsInBridallist  = false;

        if ( isset($iProductId) && !empty($iProductId) ) {

            UniWishlist()->bridallist_add( $iProductId, $iVariationId );

            $bIsInBridallist = UniWishlist()->is_in_bridallist( $iProductId, $iVariationId );

            if ( $bIsInBridallist && $iVariationId == null ) {
                $aResult['output'] = UniWishlist()->bridallist_link_raw( $iProductId, null, 'added', 'not-variable' );
            } else if ( $bIsInBridallist && $iVariationId != null ) {
                $aResult['output'] = UniWishlist()->bridallist_link_raw( $iProductId, $iVariationId, 'added', 'variable' );
            }

            if ( $bIsInBridallist ) {
                $aResult['status']      = 'success';
                $aResult['message']     = __('Successfully added to your bridal List!', 'uni-wishlist');
            } else {
                $aResult['message'] = __('Error: unknown reason', 'uni-wishlist');
            }

        } else {
            $aResult['message'] = __('Error: the product ID is not defined!', 'uni-wishlist');
        }

        wp_send_json( $aResult );
    }

    /*
    *  uni_bridallist_delete
    */
    function uni_bridallist_delete() {

        $aResult        = $this->_r();
        $iProductId     = ( isset($_POST['pid']) && !empty($_POST['pid']) ) ? (int)$_POST['pid'] : '';
        $iVariationId   = ( isset($_POST['vid']) && !empty($_POST['vid']) ) ? (int)$_POST['vid'] : null;
        $bIsInBridallist  = false;

        if ( isset($iProductId) && !empty($iProductId) ) {

            UniWishlist()->bridallist_delete( $iProductId, $iVariationId );

            $bIsInBridallist = UniWishlist()->is_in_bridallist( $iProductId, $iVariationId );

            if ( !$bIsInBridallist && $iVariationId == null ) {
                $aResult['output'] = UniWishlist()->bridallist_link_raw( $iProductId, null, 'not-added', 'not-variable' );
            } else if ( !$bIsInBridallist && $iVariationId != null ) {
                $aResult['output'] = UniWishlist()->bridallist_link_raw( $iProductId, $iVariationId, 'not-added', 'variable' );
            }

            if ( !$bIsInBridallist ) {
                $aResult['status']      = 'success';
                $aResult['message']     = __('Successfully deleted from your bridal List!', 'uni-wishlist');
            } else {
                $aResult['message'] = __('Error: unknown reason', 'uni-wishlist');
            }

        } else {
            $aResult['message'] = __('Error: the product ID is not defined!', 'uni-wishlist');
        }

        wp_send_json( $aResult );
    }

    /*
    *  uni_wc_wishlist_bridal_title_inline_edit
    */
    function uni_wc_wishlist_bridal_title_inline_edit() {

        $aResult        = $this->_r();

        $iUserId        = ( isset($_POST['uid']) && !empty($_POST['uid']) ) ? (int)$_POST['uid'] : '';
        $sTitleText     = ( isset($_POST['value']) && !empty($_POST['value']) ) ? wp_kses($_POST['value'], array()) : '';

        if ( isset($iUserId) && !empty($iUserId) && isset($sTitleText) && !empty($sTitleText) ) {

            update_user_meta( $iUserId, '_uni_wc_bridallist_title', $sTitleText );

            wp_send_json( $sTitleText );

        }
    }

    /*
    *  uni_bridallist_delete_from_list
    */
    function uni_bridallist_delete_from_list() {

        $aResult        = $this->_r();
        $iProductId     = ( isset($_POST['pid']) && !empty($_POST['pid']) ) ? (int)$_POST['pid'] : '';
        $iVariationId   = ( isset($_POST['vid']) && !empty($_POST['vid']) ) ? (int)$_POST['vid'] : null;
        $bIsInBridallist  = false;

        if ( isset($iProductId) && !empty($iProductId) ) {

            UniWishlist()->bridallist_delete( $iProductId, $iVariationId );

            $bIsInBridallist = UniWishlist()->is_in_bridallist( $iProductId, $iVariationId );

            if ( !$bIsInBridallist ) {
                $aResult['status']      = 'success';
                $aResult['message']     = __('Successfully deleted from your Bridal List!', 'uni-wishlist');
            } else {
                $aResult['message'] = __('Error: unknown reason', 'uni-wishlist');
            }

        } else {
            $aResult['message'] = __('Error: the product ID is not defined!', 'uni-wishlist');
        }

        wp_send_json( $aResult );
    }

}

}

?>