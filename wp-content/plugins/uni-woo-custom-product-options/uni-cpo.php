<?php

/*
Plugin Name: Uni Woo Custom Product Options
Plugin URI: http://moomoo.com.ua/cpo
Description: Creates ability to add custom options for products with the posibility to calculate product price based on chosen options and using custom math formula!
Author: Vitaliy 'mr.psiho' Kiyko
Version: 1.5.5
Author URI: http://moomoo.com.ua
*/

	/**
	*  Constants
    */
    if ( !defined( 'UNI_CPO_WP_PLUGIN_PATH' ) ) define( 'UNI_CPO_WP_PLUGIN_PATH', plugin_dir_path(__FILE__) );
    if ( !defined( 'UNI_CPO_WP_PLUGIN_URL' ) )  define( 'UNI_CPO_WP_PLUGIN_URL', plugin_dir_url(__FILE__) );
    if ( !defined( 'UNI_CPO_OPTIONS' ) )  define( 'UNI_CPO_OPTIONS', 'uni_cpo' );
    if ( !defined( 'UNI_CPO_VERSION' ) )  define( 'UNI_CPO_VERSION', '1.5.5' );

	/**
	*  Multilanguage support
    */
	function uni_cpo_load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'uni-cpo' );

		load_textdomain( 'uni-cpo', WP_LANG_DIR . '/uni-woo-custom-product-options/uni-cpo-' . $locale . '.mo' );
		load_plugin_textdomain( 'uni-cpo', false, plugin_basename( dirname( __FILE__ ) ) . "/languages" );
	}

	/**
	*  Js scripts
    */
    function uni_cpo_add_front_scripts() {

        wp_enqueue_style( 'uni-cpo-styles-front', UNI_CPO_WP_PLUGIN_URL.'css/uni-cpo-styles-front.css', false, UNI_CPO_VERSION, 'all');

        wp_enqueue_script('jquery');
        // spectrum
        wp_register_script('jquery-spectrum', UNI_CPO_WP_PLUGIN_URL.'js/spectrum.js', 'jquery', '1.7.0');
        wp_enqueue_script('jquery-spectrum');
        // parsley localization
        $sLocale = get_locale();
        $aLocale = explode('_',$sLocale);
        $sLangCode = $aLocale[0];
        if ( !file_exists( UNI_CPO_WP_PLUGIN_PATH . 'js/parsley/i18n/'.$sLangCode.'.js' ) ) {
            $sLangCode = 'en';
        }
        wp_register_script('parsley-localization', UNI_CPO_WP_PLUGIN_URL.'js/parsley/i18n/'.$sLangCode.'.js', array('jquery'), '2.2.0' );
        wp_enqueue_script('parsley-localization');
        // parsley.min
        wp_register_script('jquery-parsley', UNI_CPO_WP_PLUGIN_URL.'js/parsley.min.js', array('jquery'), '2.2.0' );
        wp_enqueue_script('jquery-parsley');
        // uni-cpo-front
        wp_register_script('uni-cpo-front', UNI_CPO_WP_PLUGIN_URL.'js/uni-cpo-front.js', 'jquery', UNI_CPO_VERSION);
        wp_enqueue_script('uni-cpo-front');

        $sUserDefinedSelector = get_option('uni_cpo_price_container');
        if ( empty($sUserDefinedSelector) ) {
            $sUserDefinedSelector = '';
        }

        if ( is_singular('product') ) {
            global $post;
            $sZeroPrice = uni_cpo_get_formatted_price( '0.00' );
            $params = array(
                'site_url'          => esc_url( get_bloginfo('url') ),
		        'ajax_url' 		    => esc_url( admin_url('admin-ajax.php') ),
                'price_selector'    => $sUserDefinedSelector,
                'locale'            => $sLangCode,
                'hide_price'        => ( ( get_post_meta($post->ID, '_uni_cpo_enable_custom_options_calc', true) == 'yes' ) ? 1 : 0 ),
                'zero_price'        => esc_attr($sZeroPrice),
                'text_after_zero_price' => __('(fill in all required fields to calculate the price)', 'uni-cpo'),
                'total_text_start'  => __('Total for', 'uni-cpo'),
                'total_text_end'    => __('is:', 'uni-cpo')
	        );
        } else {
            $params = array(
                'site_url'          => esc_url( get_bloginfo('url') ),
		        'ajax_url' 		    => esc_url( admin_url('admin-ajax.php') ),
                'price_selector'    => $sUserDefinedSelector,
                'locale'            => $sLangCode
	        );
        }

	    wp_localize_script( 'uni-cpo-front', 'unicpo', $params );

    }
    add_action('wp_enqueue_scripts', 'uni_cpo_add_front_scripts');

    // scripts in admin area
    function uni_cpo_admin_script( $hook ) {
        //print_r($hook);
        wp_enqueue_script('jquery');
        if ( ( $hook == 'post.php' && get_post_type() == 'product' ) ) {
            // uni-cpo-admin-product
            wp_register_script('uni-cpo-admin-product', UNI_CPO_WP_PLUGIN_URL . 'js/uni-cpo-admin-product.js', array('jquery'), UNI_CPO_VERSION );
            wp_enqueue_script('uni-cpo-admin-product');
        }
        if ( $hook == 'edit-tags.php' ) {
            // spectrum
            wp_register_script('jquery-spectrum', UNI_CPO_WP_PLUGIN_URL.'js/spectrum.js', 'jquery', '1.7.0');
            wp_enqueue_script('jquery-spectrum');
            // uni-cpo-admin-attr
            wp_register_script('uni-cpo-admin-attr', UNI_CPO_WP_PLUGIN_URL . 'js/uni-cpo-admin-attr.js', array('jquery'), UNI_CPO_VERSION );
            wp_enqueue_script('uni-cpo-admin-attr');
        }
        wp_register_style('uni-cpo-styles-admin', UNI_CPO_WP_PLUGIN_URL . 'css/uni-cpo-styles-admin.css', UNI_CPO_VERSION);
        wp_enqueue_style('uni-cpo-styles-admin');
    }
    add_action( 'admin_enqueue_scripts', 'uni_cpo_admin_script' );

    //ajax
    add_action('wp_ajax_uni_cpo_calculate_price_ajax', 'uni_cpo_calculate_price_ajax');
    add_action('wp_ajax_nopriv_uni_cpo_calculate_price_ajax', 'uni_cpo_calculate_price_ajax');

    //EvalMath class
    require_once('class/EvalMath.class.php');

//************************************ Plugin options ************************************
include_once('uni-cpo-admin-options.php');
//************************************ Additional tab on product edit screen *************
include_once('uni-cpo-product-options.php');
//***************************************** Custom options for attributes ****************
include_once('uni-cpo-attr-options.php');
//***************************************** Helpers **************************************
function uni_cpo_wc_version( $version ) {
    global $woocommerce;
    if ( isset($woocommerce) ) {
        if( version_compare( $woocommerce->version, $version, ">=" ) ) {
            return true;
        }
    } else {
        return false;
    }
}

//
function uni_cpo_get_attribute_taxonomies() {
            global $woocommerce;

            if ( ! isset( $woocommerce ) ) return array();

            $attributes = array();
            if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
                $attribute_taxonomies = wc_get_attribute_taxonomies();
            } else {
                $attribute_taxonomies = $woocommerce->get_attribute_taxonomies();
            }

            if( empty( $attribute_taxonomies ) ) return array();
            foreach( $attribute_taxonomies as $attribute ) {

                /* FIX TO WOOCOMMERCE 2.1 */
                if ( function_exists( 'wc_attribute_taxonomy_name' ) ) {
                    $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
                } else {
                    $taxonomy = $woocommerce->attribute_taxonomy_name( $attribute->attribute_name );
                }


                if ( taxonomy_exists( $taxonomy ) ) {
                    $attributes[] = $attribute->attribute_name;
                }
            }

            return $attributes;
}

//
function uni_cpo_get_product_attributes( $sId ) {
    return (array) maybe_unserialize( get_post_meta( $sId, '_uni_cpo_product_attributes', true ) );
}

//
function uni_cpo_get_formatted_price( $sPrice ) {

	$decimal_separator  = wc_get_price_decimal_separator();
	$thousand_separator = wc_get_price_thousand_separator();
	$decimals           = wc_get_price_decimals();
    $price_format       = get_woocommerce_price_format();

	$negative           = $sPrice < 0;
	$sPrice             = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $sPrice * -1 : $sPrice ) );
	$sPrice             = apply_filters( 'formatted_woocommerce_price', number_format( $sPrice, $decimals, $decimal_separator, $thousand_separator ), $sPrice, $decimals, $decimal_separator, $thousand_separator );

	if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
		$sPrice = wc_trim_zeros( $sPrice );
	}
    $formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, get_woocommerce_currency_symbol( '' ), $sPrice );
    return $formatted_price;
}

//
function uni_cpo_get_formatted_float( $sPrice ) {

	$decimal_separator  = wc_get_price_decimal_separator();
	$thousand_separator = wc_get_price_thousand_separator();
	$decimals           = wc_get_price_decimals();

	$negative           = $sPrice < 0;
	$sPrice             = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $sPrice * -1 : $sPrice ) );

    return $sPrice;
}

//
function uni_cpo_get_cart_price_crossed( $sPrice, $sDiscountedPrice, $sDiscountType = null ) {

    if ( $sDiscountedPrice < $sPrice ) {
	    $sOutput = '<span class="uni-item-price amount" style="color:#757575;text-decoration:line-through;margin-right:4px;">' . woocommerce_price( $sPrice ) . '</span><span class="uni-item-discounted-price amount" style="color:#111;font-weight:bold;"> ' . woocommerce_price( $sDiscountedPrice ) . '</span>';
        if ( isset($sDiscountType) ) {
            $sDiscountValue = $sPrice - $sDiscountedPrice;
            $sOutput .= '<small class="uni-item-discount amount" style="display:block;color:green;"> '.sprintf(__('You save %s!', 'uni-cpo'), woocommerce_price( $sDiscountValue )).'</small>';
        }
	} else {
	    $sOutput = woocommerce_price( $sPrice );
	}

    return $sOutput;

}

//
function uni_cpo_change_cart_item_price( $sProductPrice, $cart_item ) {

    if ( isset($cart_item['_uni_cpo_qty_discounts_calc']) && !empty($cart_item['_uni_cpo_qty_discounts_calc']) && $cart_item['_uni_cpo_qty_discounts_calc'] == 'yes' ) {
        if ( !empty($cart_item['_uni_cpo_data']) ) {
            $sPrice = $cart_item['_uni_cpo_data']['uni_cpo_price'];
            $sDiscountedPrice = $cart_item['data']->price;
            $sDiscountType = get_post_meta( $cart_item['product_id'], '_uni_cpo_discount_type', true);
            if ( isset($sDiscountType) ) {
                return uni_cpo_get_cart_price_crossed( $sPrice, $sDiscountedPrice, $sDiscountType );
            } else {
                return uni_cpo_get_cart_price_crossed( $sPrice, $sDiscountedPrice );
            }
        } else {
            return $sProductPrice;
        }
    } else {
        return $sProductPrice;
    }
}

//
function uni_get_registered_image_sizes( $size = '' ) {

        global $_wp_additional_image_sizes;

        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        // Create the full array with sizes and crop info
        foreach( $get_intermediate_image_sizes as $_size ) {

                if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

                        $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                        $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                        $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

                } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

                        $sizes[ $_size ] = array(
                                'width'     => $_wp_additional_image_sizes[ $_size ]['width'],
                                'height'    => $_wp_additional_image_sizes[ $_size ]['height'],
                                'crop'      =>  $_wp_additional_image_sizes[ $_size ]['crop']
                        );

                }

        }

        // Get only 1 size if found
        if ( $size ) {

                if( isset( $sizes[ $size ] ) ) {
                        return $sizes[ $size ];
                } else {
                        return false;
                }

        }

        return $sizes;
}

// Quantity input args filter
add_filter( 'woocommerce_quantity_input_args', 'uni_cpo_quantity_input_args', 10, 2 );
function uni_cpo_quantity_input_args( $args, $product ) {
    if ( is_singular('product') ) {
        $sMinQty    = ( get_post_meta( $product->id, '_uni_cpo_min_qty', true) ) ? get_post_meta( $product->id, '_uni_cpo_min_qty', true) : '1';
        $sMaxQty    = ( get_post_meta( $product->id, '_uni_cpo_max_qty', true) ) ? get_post_meta( $product->id, '_uni_cpo_max_qty', true) : '';
        $sStartQty  = ( get_post_meta( $product->id, '_uni_cpo_start_qty',true) ) ? get_post_meta( $product->id, '_uni_cpo_start_qty',true) : '1';
        $sStepQty   = ( get_post_meta( $product->id, '_uni_cpo_step_qty', true) ) ? get_post_meta( $product->id, '_uni_cpo_step_qty', true) : '1';

        $args['input_value']    = $sStartQty;
        $args['max_value']      = $sMaxQty;
        $args['min_value']      = $sMinQty;
        $args['step']           = $sStepQty;
    }
    return $args;

}

//************************************* Inputs on product page & Logic ***************************
add_action('init', 'uni_cpo_init');
function uni_cpo_init() {

    // Multilanguage support
    uni_cpo_load_plugin_textdomain();

    $bWc2Plus = uni_cpo_wc_version( 2.0 );
    if ( $bWc2Plus ) {
        add_filter('woocommerce_cart_item_price', 'uni_cpo_change_cart_item_price', 10, 2);
    } else {   // for WC 2+
        add_filter('woocommerce_cart_item_price_html', 'uni_cpo_change_cart_item_price', 10, 2);
    }
}

//
add_action( 'woocommerce_before_add_to_cart_button', 'uni_cpo_product_page_options', 10 );
function uni_cpo_product_page_options(){

    global $post;
    $aProductCustom = get_post_custom($post->ID);
    $aCustomOptions = uni_cpo_get_product_attributes( $post->ID );

    if( !empty($aProductCustom['_uni_cpo_enable_custom_options_calc'][0]) && $aProductCustom['_uni_cpo_enable_custom_options_calc'][0] == 'yes' ) {

        echo '<div class="uni_cpo_options_box">';

        echo '<input type="hidden" class="uni_cpo_product_id" name="uni_cpo_product_id" value="'.$post->ID.'" />';
        echo '<input type="hidden" class="uni_cpo_cart_item_id" name="uni_cpo_cart_item_id" value="'.current_time('timestamp').'" />';

        if ( $aCustomOptions ) :
            foreach ( $aCustomOptions as $aOption ) :

		    if ( empty( $aOption['is_visible'] ) || ( $aOption['is_taxonomy'] && ! taxonomy_exists( $aOption['name'] ) ) ) {
			    continue;
		    }

            switch ($aOption['cpo_type']) {
                case 'input':
                    echo '<div id="uni_cpo_container_'.$aOption['name'].'" class="uni_cpo_fields_container">';
                    do_action('uni_cpo_before_field_'.$aOption['name'].'_action');
                    echo '<label class="uni_cpo_fields_label">'.wc_attribute_label( $aOption['name'] ).':</label>';
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                echo '<label class="uni_cpo_option_label uni_cpo_text_option_label">'.$oTerm->name.( ($aOption['is_cpo_required']) ? ' <span class="uni-cpo-required">*</span>' : '' );
                                if ( $aOption['is_cpo_required'] == true ) {
                                    echo '<input type="text" name="uni_cpo_'.$sValue.'" class="uni-cpo-input uni-cpo-required" value="'.( ( !empty($aTermData['text_def_value']) ) ? esc_attr($aTermData['text_def_value']) : '' ).'" data-parsley-required="true" data-parsley-trigger="change focusout submit" />';
                                } else {
                                    echo '<input type="text" name="uni_cpo_'.$sValue.'" class="uni-cpo-input" value="'.( ( !empty($aTermData['text_def_value']) ) ? esc_attr($aTermData['text_def_value']) : '' ).'" />';
                                }
                                echo '</label>';
                            }
                        }
                    echo '<div class="uni-cpo-clear"></div>';
                    echo '</div>';
                    break;
                case 'input_number':
                    echo '<div id="uni_cpo_container_'.$aOption['name'].'" class="uni_cpo_fields_container">';
                    do_action('uni_cpo_before_field_'.$aOption['name'].'_action');
                    echo '<label class="uni_cpo_fields_label">'.wc_attribute_label( $aOption['name'] ).':</label>';
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                echo '<label class="uni_cpo_option_label uni_cpo_text_option_label">'.$oTerm->name.( ($aOption['is_cpo_required']) ? ' <span class="uni-cpo-required">*</span>' : '' );
                                if ( $aOption['is_cpo_required'] == true ) {
                                    echo '<input type="text" name="uni_cpo_'.$sValue.'" class="uni-cpo-input uni-cpo-required" value="'.( ( !empty($aTermData['text_def_value']) ) ? esc_attr($aTermData['text_def_value']) : '' ).'" data-parsley-required="true" data-parsley-trigger="change focusout submit" data-parsley-pattern="^(?!0*(\.0+)?$)(\d+|\d*\.\d+)$"'.( ( !empty($aTermData['text_min_value']) ) ? ' data-parsley-min="'.esc_attr($aTermData['text_min_value']).'"' : '' ).( ( !empty($aTermData['text_max_value']) ) ? ' data-parsley-max="'.esc_attr($aTermData['text_max_value']).'"' : '' ).' />';
                                } else {
                                    echo '<input type="text" name="uni_cpo_'.$sValue.'" class="uni-cpo-input" value="'.( ( !empty($aTermData['text_def_value']) ) ? esc_attr($aTermData['text_def_value']) : '' ).'" />';
                                }
                                echo '</label>';
                            }
                        }
                    echo '<div class="uni-cpo-clear"></div>';
                    echo '</div>';
                    break;
                case 'select':
                    echo '<div id="uni_cpo_container_'.$aOption['name'].'" class="uni_cpo_fields_container">';
                    do_action('uni_cpo_before_field_'.$aOption['name'].'_action');
                    echo '<label class="uni_cpo_fields_label uni_cpo_select_option_label">'.wc_attribute_label( $aOption['name'] ).':'.( ($aOption['is_cpo_required']) ? ' <span class="uni-cpo-required">*</span>' : '' ).'</label>';
                    if ( $aOption['is_cpo_required'] == true ) {
                        echo '<select name="uni_cpo_'.$aOption['name'].'" class="uni_cpo_'.$aOption['name'].' uni-cpo-select uni-cpo-required" data-parsley-required="true" data-parsley-trigger="change focusout submit">';
                    } else {
                        echo '<select name="uni_cpo_'.$aOption['name'].'" class="uni_cpo_'.$aOption['name'].' uni-cpo-select">';
                    }
                        echo '<option value="">'.__('Please, choose...', 'uni-cpo').'</option>';
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                echo '<option value="'.$sValue.'">'.$oTerm->name.'</option>';
                            }
                        }
                    echo '</select>';
                    echo '<div class="uni-cpo-clear"></div>';
                    echo '</div>';
                    break;
                case 'checkbox':
                    echo '<div id="uni_cpo_container_'.$aOption['name'].'" class="uni_cpo_fields_container">';
                    do_action('uni_cpo_before_field_'.$aOption['name'].'_action');
                    echo '<label class="uni_cpo_fields_label">'.wc_attribute_label( $aOption['name'] ).'</label>';
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aTermData['attr_image']) && !empty($aTermData['attr_image']) ) {
                                    echo '<label class="uni_cpo_option_label uni_cpo_checkbox_option_label uni_cpo_checkbox_option_label_image uni_cpo_checkbox_option_'.$aOption['name'].'_'.$sValue.'">';
                                    echo '<input type="checkbox" name="uni_cpo_'.$sValue.'" class="uni-cpo-checkbox" value="'.$sValue.'" />';
                                    echo '<span>'.$oTerm->name.( ($aOption['is_cpo_required']) ? ' <b class="uni-cpo-required">*</b>' : '' ).'</span>';
                                    $sThumbId = '';
                                    $sThumbId = $aTermData['attr_image'];
                                    $sImageSizeSlug = $aOption['cpo_image_size'];
                                    $aImageUrl = wp_get_attachment_image_src( $sThumbId, $sImageSizeSlug );
                                    $sImageUrl = $aImageUrl[0];
                                    echo '<img src="'.$sImageUrl.'" alt="'.htmlspecialchars($oTerm->name, ENT_QUOTES).'" />';
                                    echo '</label>';
                                } else {
                                    echo '<label class="uni_cpo_option_label uni_cpo_checkbox_option_label uni_cpo_checkbox_option_'.$aOption['name'].'_'.$sValue.'">';
                                    echo '<input type="checkbox" name="uni_cpo_'.$sValue.'" class="uni-cpo-checkbox" value="'.$sValue.'" />';
                                    echo '<span>'.$oTerm->name.( ($aOption['is_cpo_required']) ? ' <b class="uni-cpo-required">*</b>' : '' ).'</span>';
                                    echo '</label>';
                                }
                            }
                        }
                    echo '<div class="uni-cpo-clear"></div>';
                    echo '</div>';
                    break;
                case 'checkbox_multiple':
                    echo '<div id="uni_cpo_container_'.$aOption['name'].'" class="uni_cpo_fields_container">';
                    do_action('uni_cpo_before_field_'.$aOption['name'].'_action');
                    echo '<label class="uni_cpo_fields_label">'.wc_attribute_label( $aOption['name'] ).( ($aOption['is_cpo_required']) ? ' <span class="uni-cpo-required">*</span>' : '' ).':</label>';
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aTermData['attr_image']) && !empty($aTermData['attr_image']) ) {
                                    echo '<label class="uni_cpo_option_label uni_cpo_checkbox_option_label uni_cpo_checkbox_option_label_image uni_cpo_checkbox_option_'.$aOption['name'].'_'.$sValue.'">';
                                    if ( $aOption['is_cpo_required'] == true ) {
                                        echo '<input type="checkbox" name="uni_cpo_'.$aOption['name'].'[]" data-multiple="yes" class="uni-cpo-checkbox uni-cpo-required" value="'.$sValue.'" data-parsley-mincheck="1" data-parsley-required="true" data-parsley-trigger="change focusout submit" />';
                                    } else {
                                        echo '<input type="checkbox" name="uni_cpo_'.$aOption['name'].'[]" data-multiple="yes" class="uni-cpo-checkbox" value="'.$sValue.'" />';
                                    }
                                    echo '<span>'.$oTerm->name.'</span>';
                                    $sThumbId = '';
                                    $sThumbId = $aTermData['attr_image'];
                                    $sImageSizeSlug = $aOption['cpo_image_size'];
                                    $aImageUrl = wp_get_attachment_image_src( $sThumbId, $sImageSizeSlug );
                                    $sImageUrl = $aImageUrl[0];
                                    echo '<img src="'.$sImageUrl.'" alt="'.htmlspecialchars($oTerm->name, ENT_QUOTES).'" />';
                                    echo '</label>';
                                } else {
                                    echo '<label class="uni_cpo_option_label uni_cpo_checkbox_option_label uni_cpo_checkbox_option_'.$aOption['name'].'_'.$sValue.'">';
                                    if ( $aOption['is_cpo_required'] == true ) {
                                        echo '<input type="checkbox" name="uni_cpo_'.$aOption['name'].'[]" data-multiple="yes" class="uni-cpo-checkbox uni-cpo-required" value="'.$sValue.'" data-parsley-mincheck="1" data-parsley-required="true" data-parsley-trigger="change focusout submit" />';
                                    } else {
                                        echo '<input type="checkbox" name="uni_cpo_'.$aOption['name'].'[]" data-multiple="yes" class="uni-cpo-checkbox" value="'.$sValue.'" />';
                                    }
                                    echo '<span>'.$oTerm->name.'</span>';
                                    echo '</label>';
                                }
                            }
                        }
                    echo '<div class="uni-cpo-clear"></div>';
                    echo '</div>';
                    break;
                case 'radio':
                    echo '<div id="uni_cpo_container_'.$aOption['name'].'" class="uni_cpo_fields_container">';
                    do_action('uni_cpo_before_field_'.$aOption['name'].'_action');
                    echo '<label class="uni_cpo_fields_label">'.wc_attribute_label( $aOption['name'] ).( ($aOption['is_cpo_required']) ? ' <span class="uni-cpo-required">*</span>' : '' ).':</label>';
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aTermData['attr_image']) && !empty($aTermData['attr_image']) ) {
                                    echo '<label class="uni_cpo_option_label uni_cpo_radio_option_label uni_cpo_radio_option_label_image uni_cpo_radio_option_'.$aOption['name'].'_'.$sValue.'">';
                                    if ( $aOption['is_cpo_required'] == true ) {
                                        echo '<input type="radio" name="uni_cpo_'.$aOption['name'].'" class="uni-cpo-radio uni-cpo-required" value="'.$sValue.'" data-parsley-required="true" data-parsley-trigger="change focusout submit" />';
                                    } else {
                                        echo '<input type="radio" name="uni_cpo_'.$aOption['name'].'" class="uni-cpo-radio" value="'.$sValue.'" />';
                                    }
                                    echo '<span>'.$oTerm->name.'</span>';
                                    $sThumbId = '';
                                    $sThumbId = $aTermData['attr_image'];
                                    $sImageSizeSlug = ( isset($aOption['cpo_image_size']) ) ? $aOption['cpo_image_size'] : 'full';
                                    $aImageUrl = wp_get_attachment_image_src( $sThumbId, $sImageSizeSlug );
                                    $sImageUrl = $aImageUrl[0];
                                    echo '<img src="'.$sImageUrl.'" alt="'.htmlspecialchars($oTerm->name, ENT_QUOTES).'" />';
                                    echo '</label>';
                                } else {
                                    echo '<label class="uni_cpo_option_label uni_cpo_radio_option_label uni_cpo_radio_option_'.$aOption['name'].'_'.$sValue.'">';
                                    if ( $aOption['is_cpo_required'] == true ) {
                                        echo '<input type="radio" name="uni_cpo_'.$aOption['name'].'" class="uni-cpo-radio uni-cpo-required" value="'.$sValue.'" data-parsley-required="true" data-parsley-trigger="change focusout submit" />';
                                    } else {
                                        echo '<input type="radio" name="uni_cpo_'.$aOption['name'].'" class="uni-cpo-radio" value="'.$sValue.'" />';
                                    }
                                    echo '<span>'.$oTerm->name.'</span>';
                                    echo '</label>';
                                }
                            }
                        }
                    echo '<div class="uni-cpo-clear"></div>';
                    echo '</div>';
                    break;
                case 'file':
                    // for the future needs ;)
                    break;
                case 'color':
                    echo '<div id="uni_cpo_container_'.$aOption['name'].'" class="uni_cpo_fields_container">';
                    do_action('uni_cpo_before_field_'.$aOption['name'].'_action');
                    echo '<label class="uni_cpo_fields_label">'.wc_attribute_label( $aOption['name'] ).( ($aOption['is_cpo_required']) ? ' <span class="uni-cpo-required">*</span>' : '' ).':</label>';
                        if ( $aOption['is_cpo_required'] == true ) {
                            echo '<input type="text" name="uni_cpo_'.$aOption['name'].'" class="uni-cpo-color uni-cpo-required uni-cpo-color-'.$aOption['name'].'" value="" data-parsley-required="true" data-parsley-trigger="change focusout submit" />';
                        } else {
                            echo '<input type="text" name="uni_cpo_'.$aOption['name'].'" class="uni-cpo-color uni-cpo-color-'.$aOption['name'].'" value="" />';
                        }
                        if ( $aOption['value'] ) {
                            $sPalette = '[';
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                $sPalette .= "'".$aTermData['attr_color_code']."',";
                            }
                            $sPalette = rtrim($sPalette, ',');
                            $sPalette .= ']';
                                echo '<script>';
                                echo "
                                (function($) {
                                    $(document).ready(function(){
                                    $('.uni-cpo-color-".$aOption['name']."').spectrum({
                                        color: '',
                                        hideAfterPaletteSelect: true,
                                        showPaletteOnly: true,
                                        showPalette:true,
                                        allowEmpty:true,
                                        showInitial: true,
                                        preferredFormat: 'hex',
                                        palette: [".$sPalette."]
                                    });
                                    });
                                })(jQuery);
                                ";
                                echo '</script>';
                        }
                    echo '<div class="uni-cpo-clear"></div>';
                    echo '</div>';
                    break;
                case 'color_ext':
                    echo '<div id="uni_cpo_container_'.$aOption['name'].'" class="uni_cpo_fields_container">';
                    do_action('uni_cpo_before_field_'.$aOption['name'].'_action');
                    echo '<label class="uni_cpo_fields_label">'.wc_attribute_label( $aOption['name'] ).( ($aOption['is_cpo_required']) ? ' <span class="uni-cpo-required">*</span>' : '' ).':</label>';
                        if ( $aOption['is_cpo_required'] == true ) {
                            echo '<input type="text" name="uni_cpo_'.$aOption['name'].'" class="uni-cpo-color uni-cpo-required uni-cpo-color-'.$aOption['name'].'" value="" data-parsley-required="true" data-parsley-trigger="change focusout submit" />';
                        } else {
                            echo '<input type="text" name="uni_cpo_'.$aOption['name'].'" class="uni-cpo-color uni-cpo-color-'.$aOption['name'].'" value="" />';
                        }
                        if ( $aOption['value'] ) {
                                echo '<script>';
                                echo "
                                (function($) {
                                    $(document).ready(function(){
                                    $('.uni-cpo-color-".$aOption['name']."').spectrum({
                                        color: '',
                                        allowEmpty:true,
                                        showInitial: true,
                                        showInput: true,
                                        preferredFormat: 'hex',
                                        clickoutFiresChange: true,
                                        chooseText: '".__('Choose', 'uni-cpo')."',
                                        cancelText: '".__('Cancel', 'uni-cpo')."'
                                    });
                                    });
                                })(jQuery);
                                ";
                                echo '</script>';
                        }
                    echo '<div class="uni-cpo-clear"></div>';
                    echo '</div>';
                    break;
                case 'textarea':
                    echo '<div id="uni_cpo_container_'.$aOption['name'].'" class="uni_cpo_fields_container">';
                    do_action('uni_cpo_before_field_'.$aOption['name'].'_action');
                    echo '<label class="uni_cpo_fields_label">'.wc_attribute_label( $aOption['name'] ).':</label>';
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                echo '<label class="uni_cpo_option_label uni_cpo_textarea_option_label">'.$oTerm->name.( ($aOption['is_cpo_required']) ? ' <span class="uni-cpo-required">*</span>' : '' ).'</label>';
                                if ( $aOption['is_cpo_required'] == true ) {
                                    echo '<textarea name="uni_cpo_'.$sValue.'" class="uni-cpo-textarea uni-cpo-required" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>';
                                } else {
                                    echo '<textarea name="uni_cpo_'.$sValue.'" class="uni-cpo-textarea"></textarea>';
                                }
                            }
                        }
                    echo '<div class="uni-cpo-clear"></div>';
                    echo '</div>';
                    break;
            }

            endforeach;
        endif;

        echo '<div style="clear:both;"></div>';
        echo '<p class="uni_cpo_alert"></p>';

        echo '</div>';

    }

}

// before add to cart validation
add_action( 'woocommerce_add_to_cart_validation', 'uni_cpo_before_add_to_cart_validation', 1, 3 );
function uni_cpo_before_add_to_cart_validation( $passed, $product_id, $quantity ) {

    global $woocommerce;

    $sProductId = ( !empty($_POST['uni_cpo_product_id']) ) ? $_POST['uni_cpo_product_id'] : '';
    $aProductCustom = get_post_custom( $sProductId );

    if( $aProductCustom['_uni_cpo_enable_custom_options_calc'][0] == 'yes' && $sProductId == $product_id ) {
        $aPostGlobalVar = $_POST;
        $aResult = uni_cpo_before_cart_validate( $aPostGlobalVar );

        if ( $aResult['status'] == 'error' ) {
            // For WC 2.1+
            if ( function_exists( 'wc_add_notice' ) ) {
                	wc_add_notice( $aResult['message'], 'error' );
            } else {
                $woocommerce->add_error( $aResult['message'] );
            }
        } else if ( $aResult['status'] == 'success' )  {
            return true;
        }
    } else {
        return true;
    }

}

// customer tries to add product to cart from loop page? let's check if it possible to do!
add_filter( 'woocommerce_loop_add_to_cart_link', 'uni_cpo_add_to_cart_button', 10, 2 );
function uni_cpo_add_to_cart_button( $link, $product ){

    $aProductCustom = get_post_custom( $product->id );

    if( !empty($aProductCustom['_uni_cpo_enable_custom_options_calc'][0]) && $aProductCustom['_uni_cpo_enable_custom_options_calc'][0] == 'yes' ) {
        $link = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button product_type_%s">%s</a>',
            esc_url( get_permalink( $product->id ) ),
            esc_attr( $product->id ),
            esc_attr( $product->get_sku() ),
            esc_attr( isset( $quantity ) ? $quantity : 1 ),
            esc_attr( $product->product_type ),
            esc_html( __( 'Select options', 'woocommerce' ) )
        );
    }
    return $link;
}

// before add to cart validation logic
function uni_cpo_before_cart_validate( $aPostGlobalVar ) {
        //print_r($aPostGlobalVar);
        $sProductId = $aPostGlobalVar['uni_cpo_product_id'];
        $aCustomOptions = uni_cpo_get_product_attributes( $sProductId );
        $aResult['status'] = 'success';
        $bRequiredNotFilled = false;

        if ( $aCustomOptions ) :
            foreach ( $aCustomOptions as $aOption ) :

		    if ( empty( $aOption['is_visible'] ) || ( $aOption['is_taxonomy'] && ! taxonomy_exists( $aOption['name'] ) ) ) {
			    continue;
		    }

            switch ($aOption['cpo_type']) {
                case 'input':
                        if ( isset($aOption['value']) && !empty($aOption['value']) ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $bRequired = ( $aOption['is_cpo_required'] ) ? true : false;
                                if ( empty($aPostGlobalVar['uni_cpo_'.$sValue]) && $bRequired ) {
                                    $bRequiredNotFilled = true;
                                }
                            }
                        }
                    break;
                case 'input_number':
                        if ( isset($aOption['value']) && !empty($aOption['value']) ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $bRequired = ( $aOption['is_cpo_required'] ) ? true : false;
                                if ( empty($aPostGlobalVar['uni_cpo_'.$sValue]) && $bRequired ) {
                                    $bRequiredNotFilled = true;
                                }
                            }
                        }
                    break;
                case 'select':
                                $sParentTermSlug = $aOption['name'];
                                $sFieldValue = $aPostGlobalVar['uni_cpo_'.$sParentTermSlug];
                                $bRequired = ( $aOption['is_cpo_required'] ) ? true : false;
                                if ( empty($sFieldValue) && $bRequired ) {
                                    $bRequiredNotFilled = true;
                                }
                    break;
                case 'checkbox':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $bRequired = ( $aOption['is_cpo_required'] ) ? true : false;
                                if ( empty($aPostGlobalVar['uni_cpo_'.$sValue]) && $bRequired ) {
                                    $bRequiredNotFilled = true;
                                }
                            }
                        }
                    break;
                case 'checkbox_multiple':
                                $sParentTermSlug = $aOption['name'];
                                //$sFieldValue = $aPostGlobalVar['uni_cpo_'.$sParentTermSlug];
                                $bRequired = ( $aOption['is_cpo_required'] ) ? true : false;
                                if ( empty($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) && $bRequired ) {
                                    $bRequiredNotFilled = true;
                                }
                    break;
                case 'radio':
                                $sParentTermSlug = $aOption['name'];
                                //$sFieldValue = $aPostGlobalVar['uni_cpo_'.$sParentTermSlug];
                                $bRequired = ( $aOption['is_cpo_required'] ) ? true : false;
                                if ( empty($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) && $bRequired ) {
                                    $bRequiredNotFilled = true;
                                }
                    break;
                case 'color':
                                $sParentTermSlug = $aOption['name'];
                                //$sFieldValue = $aPostGlobalVar['uni_cpo_'.$sParentTermSlug];
                                $bRequired = ( $aOption['is_cpo_required'] ) ? true : false;
                                if ( empty($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) && $bRequired ) {
                                    $bRequiredNotFilled = true;
                                }
                    break;
                case 'color_ext':
                                $sParentTermSlug = $aOption['name'];
                                //$sFieldValue = $aPostGlobalVar['uni_cpo_'.$sParentTermSlug];
                                $bRequired = ( $aOption['is_cpo_required'] ) ? true : false;
                                if ( empty($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) && $bRequired ) {
                                    $bRequiredNotFilled = true;
                                }
                    break;
                case 'textarea':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $bRequired = ( $aOption['is_cpo_required'] ) ? true : false;
                                if ( empty($aPostGlobalVar['uni_cpo_'.$sValue]) && $bRequired ) {
                                    $bRequiredNotFilled = true;
                                }
                            }
                        }
                    break;
            }

            endforeach;
        endif;

        if ( $bRequiredNotFilled ) {
            $aResult['status'] = 'error';
            $aResult['message'] = __('Please, fill in all required fields!', 'uni-cpo');
            return $aResult;
        } else {
            return $aResult;
        }

}

// calculate price before add to cart
add_filter( 'woocommerce_get_price', 'uni_cpo_before_cart_price', 10, 2);
function uni_cpo_before_cart_price( $sPrice, $product ) {

    if ( $_POST && isset($_POST['uni_cpo_product_id']) ) {
        $sProductId         = $_POST['uni_cpo_product_id'];
        $aProductCustom     = get_post_custom( $sProductId );

        if( $aProductCustom['_uni_cpo_enable_custom_options_calc'][0] == 'yes' && $sProductId == $product->id ) {
            $aPostGlobalVar = $_POST;
            $sPrice         = uni_cpo_before_cart_calc_price( $product, $aPostGlobalVar );
            $sPrice         = uni_cpo_get_formatted_float( $sPrice );
            return $sPrice;
        } else {
            return $sPrice;
        }
    } else {
        return $sPrice;
    }
}

// calculate price before add to cart logic
function uni_cpo_before_cart_calc_price( $product, $aPostGlobalVar ) {

    if( $product->is_type( array('simple') ) ) {  // it is essential that product have to be 'simple'

        $sProductId = $aPostGlobalVar['uni_cpo_product_id'];
        $aProductCustom = get_post_custom( $sProductId );
        $aCustomOptions = uni_cpo_get_product_attributes( $sProductId );
        $aArr = array();

        if ( $aCustomOptions ) :
            foreach ( $aCustomOptions as $aOption ) :

		    if ( empty( $aOption['is_visible'] ) || ( $aOption['is_taxonomy'] && ! taxonomy_exists( $aOption['name'] ) ) ) {
			    continue;
		    }

            switch ($aOption['cpo_type']) {
                case 'input':
                        if ( isset($aOption['value']) && !empty($aOption['value']) ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aPostGlobalVar['uni_cpo_'.$sValue]) ) {
                                    $aPostGlobalVar['uni_cpo_'.$sValue] = floatval(str_replace(',', '.', $aPostGlobalVar['uni_cpo_'.$sValue]));
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $aPostGlobalVar['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'input_number':
                        if ( isset($aOption['value']) && !empty($aOption['value']) ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aPostGlobalVar['uni_cpo_'.$sValue]) ) {
                                    $aPostGlobalVar['uni_cpo_'.$sValue] = floatval(str_replace(',', '.', $aPostGlobalVar['uni_cpo_'.$sValue]));
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $aPostGlobalVar['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'select':
                                $sParentTermSlug = $aOption['name'];
                                //$sFieldValue = $aPostGlobalVar['uni_cpo_'.$sParentTermSlug];
                                if ( isset($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) && !empty($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) ) {
                                    $oTerm = get_term_by('slug', $aPostGlobalVar['uni_cpo_'.$sParentTermSlug], $aOption['name']);
                                    $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = $aTermData['attr_price'];
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'radio':
                                $sParentTermSlug = $aOption['name'];
                                //$sFieldValue = $aPostGlobalVar['uni_cpo_'.$sParentTermSlug];
                                if ( isset($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) && !empty($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) ) {
                                    $oTerm = get_term_by('slug', $aPostGlobalVar['uni_cpo_'.$sParentTermSlug], $aOption['name']);
                                    $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = $aTermData['attr_price'];
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'checkbox':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aPostGlobalVar['uni_cpo_'.$sValue]) ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $aPostGlobalVar['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'checkbox_multiple':
                        $aCheckboxed = ( isset($aPostGlobalVar['uni_cpo_'.$aOption['name']]) ) ? $aPostGlobalVar['uni_cpo_'.$aOption['name']] : '';
                        if ( isset($aCheckboxed) && !empty($aCheckboxed) ) {
                            foreach ( $aCheckboxed as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aTermData['attr_price']) ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $aTermData['attr_price'];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'color':
                                $sParentTermSlug = $aOption['name'];
                                //$sFieldValue = $aPostGlobalVar['uni_cpo_'.$sParentTermSlug];
                                if ( isset($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) && !empty($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) ) {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                    foreach ( $aOption['value'] as $sValue ) {
                                        $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                        $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                        if ( $aTermData['attr_color_code'] == $aPostGlobalVar['uni_cpo_'.$sParentTermSlug] ) {
                                            $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = $aTermData['attr_price'];
                                        }
                                    }
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'color_ext':
                                $sParentTermSlug = $aOption['name'];
                                //$sFieldValue = $aPostGlobalVar['uni_cpo_'.$sParentTermSlug];
                                if ( isset($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) && !empty($aPostGlobalVar['uni_cpo_'.$sParentTermSlug]) ) {
                                    foreach ( $aOption['value'] as $sValue ) {
                                        $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                        $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                        $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = $aTermData['attr_price'];
                                    }
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'textarea':
                        /*
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( $aPostGlobalVar['uni_cpo_'.$sValue] ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $aPostGlobalVar['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                        */
                    break;
            }

            endforeach;
        endif;


        $sGlobalFormula         = $aProductCustom['_uni_cpo_formula'][0];

        // conditions
        if ( isset($aProductCustom['_uni_cpo_conditions_enable'][0]) && !empty($aProductCustom['_uni_cpo_conditions_enable'][0]) ) {
            $aConditions = maybe_unserialize($aProductCustom['_uni_cpo_conditions_data'][0]);

            if ( isset($aConditions) && !empty( $aConditions ) ) {

                $aPostData          = $aPostGlobalVar;
                $sConditionFormula  = uni_cpo_conditions_logic( $aConditions, $aPostData, false );
                if ( isset($sConditionFormula) && !empty($sConditionFormula) ) {
                    $sGlobalFormula = $sConditionFormula;
                }

            }

        }

        if ( !$aProductCustom['_sale_price'][0] ) {
            $sProductPrice = $aProductCustom['_regular_price'][0];
        } else {
            //sale price
            $sProductPrice = $aProductCustom['_sale_price'][0];
        }

        $sGlobalFormula	        = str_replace("{uni_cpo_price}", $sProductPrice, $sGlobalFormula);
        if ( $aArr ) {
            foreach ( $aArr as $Key => $Value ) {
                if ( is_array($Value) ) {
                    if ( !empty($Value) ) {
                        foreach ( $Value as $ChildKey => $ChildValue ) {
                            $ChildKey       = '{'.$ChildKey.'}';
                            $sSearch        = "/($ChildKey)/";
                            $sGlobalFormula = preg_replace($sSearch, $ChildValue, $sGlobalFormula);
                        }
                    }
                } else {
                    $sSearch                = "/($Key)/";
                    $sGlobalFormula         = preg_replace($sSearch, $Value, $sGlobalFormula);
                }
            }
        }

        // change all forgotten cpo vars to zero
        $search             = "/{([^}]*)}/";
        $sGlobalFormula     = preg_replace($search,'0',$sGlobalFormula);

        //print_r($sGlobalFormula);
        $m                  = new EvalMath;
        $m->suppress_errors = true;
        $sOrderPrice        = $m->evaluate($sGlobalFormula);
        $fOrderPrice        = floatval( $sOrderPrice );
        $fMinPrice          = floatval( $aProductCustom['_uni_cpo_min_price'][0] );
        //print_r(' | price is:'.$fOrderPrice);
        //die();

        if ( !empty($fMinPrice) && ( $fOrderPrice < $fMinPrice ) ) {
           return $aProductCustom['_uni_cpo_min_price'][0];
        } else {
            return $fOrderPrice;
        }

    }

}

// something new?
//do_action( 'woocommerce_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data );
//do_action( 'woocommerce_after_cart_item_quantity_update', $cart_item_key, $quantity, $old_quantity );
//add_action( 'woocommerce_before_calculate_totals', 'uni_cpo_before_calculate_totals', 10, 1 );
//function uni_cpo_before_calculate_totals( $oCart ) {
    /*
    $aCartContent = $oCart->get_cart()
			foreach ( $aCartContent as $cart_item_key => $values ) {
			    $iProductId = $values['product_id'];
                $aProductCustom = get_post_custom( $iProductId );
			    if ( $values['_uni_cpo_calc_option'] == 'yes' && $aProductCustom[''] ) {

			    }
			}
    */
//}

// associate with order's meta
add_filter('woocommerce_add_cart_item_data', 'uni_cpo_add_cart_item_data', 10, 2);
add_filter('woocommerce_get_cart_item_from_session', 'uni_cpo_get_cart_item_from_session', 10, 2);
add_filter('woocommerce_get_item_data', 'uni_cpo_get_item_data', 10, 2);
add_filter('woocommerce_add_cart_item', 'uni_cpo_add_cart_item', 10, 1);
add_action( 'woocommerce_add_order_item_meta', 'uni_cpo_add_order_item_meta', 10, 3 );

function uni_cpo_add_cart_item_data($cart_item_meta, $sProductId) {
        global $woocommerce;

        $aProductCustom = get_post_custom( $sProductId );

        if ( $aProductCustom['_uni_cpo_enable_custom_options_calc'][0] == 'yes' ) {
            $cart_item_meta['_uni_cpo_calc_option'] = $aProductCustom['_uni_cpo_enable_custom_options_calc'][0];
            $cart_item_meta['_uni_cpo_cart_item_id'] = $_POST['uni_cpo_cart_item_id'];

            $aCustomOptions = uni_cpo_get_product_attributes( $sProductId );

            if ( $aCustomOptions ) :
                foreach ( $aCustomOptions as $aOption ) :

		        if ( empty( $aOption['is_visible'] ) || ( $aOption['is_taxonomy'] && ! taxonomy_exists( $aOption['name'] ) ) ) {
			        continue;
		        }

                switch ($aOption['cpo_type']) {
                    case 'input':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( !empty( $_POST['uni_cpo_'.$sValue] ) ) {
                                    if ( is_numeric($_POST['uni_cpo_'.$sValue]) ) {
                                        $cart_item_meta['_uni_cpo_data']['uni_cpo_'.$sValue] = floatval(str_replace(',', '.', $_POST['uni_cpo_'.$sValue]));
                                    } else if ( is_string($_POST['uni_cpo_'.$sValue]) ) {
                                        $cart_item_meta['_uni_cpo_data']['uni_cpo_'.$sValue] = $_POST['uni_cpo_'.$sValue];
                                    }
                                } else {
                                    if ( is_numeric($_POST['uni_cpo_'.$sValue]) ) {
                                        //$cart_item_meta['_uni_cpo_data']['uni_cpo_'.$sValue] = '0';
                                    } else if ( is_string($_POST['uni_cpo_'.$sValue]) ) {
                                        //$cart_item_meta['_uni_cpo_data']['uni_cpo_'.$sValue] = '';
                                    }
                                }
                            }
                        }
                        break;
                    case 'input_number':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                $cart_item_meta['_uni_cpo_data']['uni_cpo_'.$sValue] = ( isset( $_POST['uni_cpo_'.$sValue] ) && $_POST['uni_cpo_'.$sValue] != '') ? floatval(str_replace(',', '.', $_POST['uni_cpo_'.$sValue])) : '0';
                            }
                        }
                        break;
                    case 'select':
                                $sParentTermSlug = $aOption['name'];
                                $cart_item_meta['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug] = ( isset( $_POST['uni_cpo_'.$sParentTermSlug] ) && $_POST['uni_cpo_'.$sParentTermSlug] != '') ? $_POST['uni_cpo_'.$sParentTermSlug] : '0';
                        break;
                    case 'radio':
                                $sParentTermSlug = $aOption['name'];
                                $cart_item_meta['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug] = ( isset( $_POST['uni_cpo_'.$sParentTermSlug] ) && $_POST['uni_cpo_'.$sParentTermSlug] != '') ? $_POST['uni_cpo_'.$sParentTermSlug] : '0';
                        break;
                    case 'checkbox':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                $cart_item_meta['_uni_cpo_data']['uni_cpo_'.$sValue] = ( isset( $_POST['uni_cpo_'.$sValue] ) && $_POST['uni_cpo_'.$sValue] != '') ? $_POST['uni_cpo_'.$sValue] : '0';
                            }
                        }
                        break;
                    case 'checkbox_multiple':
                        $aCheckboxed = ( isset($_POST['uni_cpo_'.$aOption['name']]) ) ? $_POST['uni_cpo_'.$aOption['name']] : '';
                        if ( isset($aCheckboxed) && !empty($aCheckboxed) ) {
                            $cart_item_meta['_uni_cpo_data']['uni_cpo_'.$aOption['name']] = $aCheckboxed;
                        } else {
                            $cart_item_meta['_uni_cpo_data']['uni_cpo_'.$aOption['name']] = '0';
                        }
                        break;
                    case 'color':
                                $sParentTermSlug = $aOption['name'];
                                $cart_item_meta['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug] = ( isset( $_POST['uni_cpo_'.$sParentTermSlug] ) && $_POST['uni_cpo_'.$sParentTermSlug] != '') ? $_POST['uni_cpo_'.$sParentTermSlug] : '0';
                        break;
                    case 'color_ext':
                                $sParentTermSlug = $aOption['name'];
                                $cart_item_meta['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug] = ( isset( $_POST['uni_cpo_'.$sParentTermSlug] ) && $_POST['uni_cpo_'.$sParentTermSlug] != '') ? $_POST['uni_cpo_'.$sParentTermSlug] : '0';
                        break;
                    case 'textarea':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                $cart_item_meta['_uni_cpo_data']['uni_cpo_'.$sValue] = ( isset( $_POST['uni_cpo_'.$sValue] ) && $_POST['uni_cpo_'.$sValue] != '') ? $_POST['uni_cpo_'.$sValue] : '';
                            }
                        }
                        break;
                }

                endforeach;
            endif;

            $sPrice                                             = uni_cpo_calculate_price_in_cart($cart_item_meta['_uni_cpo_data'], $sProductId);
            $sPrice                                             = uni_cpo_get_formatted_float( $sPrice );
            $cart_item_meta['_uni_cpo_data']['uni_cpo_price']   = $sPrice;
        }

        if ( $aProductCustom['_uni_cpo_enable_qty_discounts_calc'][0] == 'yes' ) {
            $cart_item_meta['_uni_cpo_qty_discounts_calc']  = $aProductCustom['_uni_cpo_enable_qty_discounts_calc'][0];
            $cart_item_meta['_uni_cpo_cart_item_id']        = ( !empty($_POST['uni_cpo_cart_item_id']) ? $_POST['uni_cpo_cart_item_id'] : '' );
        }

        return $cart_item_meta;
}

function uni_cpo_get_cart_item_from_session($cart_item, $values) {

        if (isset($values['_uni_cpo_calc_option'])) {
            $cart_item['_uni_cpo_calc_option'] = $values['_uni_cpo_calc_option'];
        }

        if (isset($values['_uni_cpo_cart_item_id'])) {
            $cart_item['_uni_cpo_cart_item_id'] = $values['_uni_cpo_cart_item_id'];
        }

        if (isset($values['_uni_cpo_data'])) {
            $cart_item['_uni_cpo_data'] = $values['_uni_cpo_data'];
        }

        if (isset($values['_uni_cpo_qty_discounts_calc'])) {
            $cart_item['_uni_cpo_qty_discounts_calc'] = $values['_uni_cpo_qty_discounts_calc'];
        }

        if (isset($cart_item['_uni_cpo_data'])) {
            uni_cpo_add_cart_item($cart_item);
        }
        return $cart_item;
}

function uni_cpo_get_item_data($other_data, $cart_item) {

        if ( !empty($cart_item['_uni_cpo_calc_option']) ) {
            if ( !empty($cart_item['_uni_cpo_data']) ) {

            $sProductId = $cart_item['product_id'];
            $aCustomOptions = uni_cpo_get_product_attributes( $sProductId );

            if ( $aCustomOptions ) :
                foreach ( $aCustomOptions as $aOption ) :

		        if ( empty( $aOption['is_visible'] ) || ( $aOption['is_taxonomy'] && ! taxonomy_exists( $aOption['name'] ) ) ) {
			        continue;
		        }

                switch ($aOption['cpo_type']) {
                    case 'input':
                            if ( isset($aOption['value']) && !empty($aOption['value']) ) {
                                foreach ( $aOption['value'] as $sValue ) {
                                    $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                    $sAttrCartValue = ( isset($cart_item['_uni_cpo_data']['uni_cpo_'.$sValue]) && !empty($cart_item['_uni_cpo_data']['uni_cpo_'.$sValue]) ) ? $cart_item['_uni_cpo_data']['uni_cpo_'.$sValue] : '';
                                    $other_data[] = array('name' => $oTerm->name, 'value' => $sAttrCartValue);
                                }
                            }
                        break;
                    case 'input_number':
                            if ( isset($aOption['value']) && !empty($aOption['value']) ) {
                                foreach ( $aOption['value'] as $sValue ) {
                                    $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                    $sAttrCartValue = ( isset($cart_item['_uni_cpo_data']['uni_cpo_'.$sValue]) && !empty($cart_item['_uni_cpo_data']['uni_cpo_'.$sValue]) ) ? $cart_item['_uni_cpo_data']['uni_cpo_'.$sValue] : '';
                                    $other_data[] = array('name' => $oTerm->name, 'value' => $sAttrCartValue);
                                }
                            }
                        break;
                    case 'select':
                                $sParentTermSlug = $aOption['name'];
                                if ( isset($cart_item['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug]) ) {
                                    $sFieldValue = $cart_item['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug];
                                    $oTerm = get_term_by('slug', $sFieldValue, $sParentTermSlug);
                                    if ( $sFieldValue ) {
                                        $other_data[] = array('name' => wc_attribute_label( $aOption['name'] ), 'value' => $oTerm->name);
                                    }
                                }
                        break;
                    case 'radio':
                                $sParentTermSlug = $aOption['name'];
                                if ( isset($cart_item['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug]) ) {
                                    $sFieldValue = $cart_item['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug];
                                    $oTerm = get_term_by('slug', $sFieldValue, $sParentTermSlug);
                                    if ( $sFieldValue ) {
                                        $other_data[] = array('name' => wc_attribute_label( $aOption['name'] ), 'value' => $oTerm->name);
                                    }
                                }
                        break;
                    case 'checkbox':
                            if ( $aOption['value'] ) {
                                foreach ( $aOption['value'] as $sValue ) {
                                    $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                    if ( isset($cart_item['_uni_cpo_data']['uni_cpo_'.$sValue]) && !empty($cart_item['_uni_cpo_data']['uni_cpo_'.$sValue]) ) {
                                        $other_data[] = array('name' => $oTerm->name, 'value' => __('Checked', 'uni-cpo'));
                                    }
                                }
                            }
                        break;
                    case 'checkbox_multiple':
                        if ( isset($cart_item['_uni_cpo_data']['uni_cpo_'.$aOption['name']]) && !empty($cart_item['_uni_cpo_data']['uni_cpo_'.$aOption['name']]) ) {
                            $aCheckboxedTermsName = array();
                                foreach ( $cart_item['_uni_cpo_data']['uni_cpo_'.$aOption['name']] as $sValue ) {
                                    $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                    $aCheckboxedTermsName[] = $oTerm->name;
                                }
                                if ( isset($aCheckboxedTermsName) && !empty($aCheckboxedTermsName) ) {
                                    $sCheckboxedTermsName = implode(', ', $aCheckboxedTermsName);
                                    $other_data[] = array('name' => wc_attribute_label( $aOption['name'] ), 'value' => $sCheckboxedTermsName);
                                }
                        }
                        break;
                    case 'color':
                                $sParentTermSlug = $aOption['name'];
                                $sFieldValue = $cart_item['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug];
                                if ( $sFieldValue ) {
                                    foreach ( $aOption['value'] as $sValue ) {
                                        $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                        $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                        if ( $aTermData['attr_color_code'] == $sFieldValue ) {
                                            $other_data[] = array('name' => wc_attribute_label( $aOption['name'] ), 'value' => $oTerm->name);
                                        }
                                    }
                                }
                        break;
                    case 'color_ext':
                                $sParentTermSlug = $aOption['name'];
                                $sFieldValue = $cart_item['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug];
                                if ( $sFieldValue ) {
                                    foreach ( $aOption['value'] as $sValue ) {
                                        $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                        $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                        $other_data[] = array('name' => wc_attribute_label( $aOption['name'] ), 'value' => $sFieldValue);
                                    }
                                }
                        break;
                    case 'textarea':
                            if ( $aOption['value'] ) {
                                foreach ( $aOption['value'] as $sValue ) {
                                    $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                    if ( isset($cart_item['_uni_cpo_data']['uni_cpo_'.$sValue]) )
                                        $other_data[] = array('name' => $oTerm->name, 'value' => $cart_item['_uni_cpo_data']['uni_cpo_'.$sValue]);
                                }
                            }
                        break;
                }

                endforeach;
            endif;

            }
        }

        return $other_data;
}

function uni_cpo_add_cart_item( $cart_item ) {
        global $woocommerce;

        $sEnableCalcFrontend = ( !empty($cart_item['_uni_cpo_calc_option']) ) ? $cart_item['_uni_cpo_calc_option'] : '';
        $sEnableCalcDiscounts = ( isset($cart_item['_uni_cpo_qty_discounts_calc']) && !empty($cart_item['_uni_cpo_qty_discounts_calc']) ) ? $cart_item['_uni_cpo_qty_discounts_calc'] : '';

        if ( $sEnableCalcFrontend == 'yes' && ($sEnableCalcDiscounts == 'no' || empty($sEnableCalcDiscounts) ) ) {
            if ( isset($cart_item['_uni_cpo_data']) ) {
                foreach ( $cart_item['_uni_cpo_data'] as $sKey => $sValue ) {
                    $aCustomPostArray[$sKey] = $sValue;
                }
                $aCustomPostArray['quantity'] = $cart_item['quantity'];

                $sPrice = uni_cpo_calculate_price_in_cart($aCustomPostArray, $cart_item['product_id']);
                $sPrice = uni_cpo_get_formatted_float( $sPrice );
                $cart_item['_uni_cpo_data']['uni_cpo_price'] = $sPrice;

                $cart_item['data']->set_price($cart_item['_uni_cpo_data']['uni_cpo_price']);
            }
        } else if ( $sEnableCalcDiscounts == 'yes' && $sEnableCalcFrontend == 'no' ) {

        } else if ( $sEnableCalcFrontend == 'yes' && $sEnableCalcDiscounts == 'yes' ) {

            if (isset($cart_item['_uni_cpo_data'])) {
                foreach ( $cart_item['_uni_cpo_data'] as $sKey => $sValue ) {
                    $aCustomPostArray[$sKey] = $sValue;
                }
                $aCustomPostArray['quantity'] = $cart_item['quantity'];

                $sPrice = uni_cpo_calculate_price_in_cart($aCustomPostArray, $cart_item['product_id']);
                $sPrice = uni_cpo_get_formatted_float( $sPrice );
                $cart_item['_uni_cpo_data']['uni_cpo_price'] = $sPrice;

                $cart_item['data']->set_price($cart_item['_uni_cpo_data']['uni_cpo_price']);
            }

            $sCpoPrice = $cart_item['_uni_cpo_data']['uni_cpo_price'];

            $aDiscounts = get_post_meta( $cart_item['product_id'], '_uni_cpo_discount_data', true);
            $sDiscountType = get_post_meta( $cart_item['product_id'], '_uni_cpo_discount_type', true);
            if ( !empty( $aDiscounts ) ) {
                $sDiscountsCount = count($aDiscounts);
                $i = 1;
                foreach ( $aDiscounts as $aDiscount ) {
                    if ( ( $cart_item['quantity'] >= $aDiscount['min'] && $cart_item['quantity'] <= $aDiscount['max'] ) || ( $i == $sDiscountsCount && $cart_item['quantity'] > $aDiscount['max'] ) ) {
                        if ( isset($sDiscountType) && $sDiscountType == 'fixed' ) {
                            $sCpoPrice = $sCpoPrice - $aDiscount['value'];
                            $sCpoPrice = number_format($sCpoPrice, 2,'.',',');
                        } else if ( isset($sDiscountType) && $sDiscountType == 'percent' ) {
                            $sCpoPrice = $sCpoPrice - $sCpoPrice*($aDiscount['value']/100);
                            $sCpoPrice = number_format($sCpoPrice, 2,'.',',');
                        }
                    }
                    $i++;
                }
            }

            $cart_item['data']->set_price($sCpoPrice);
        }

        return $cart_item;
}

function uni_cpo_add_order_item_meta($item_id, $values, $cart_item_key) {
        $sEnableCalcFrontend = $values['_uni_cpo_calc_option'];

        if ( isset($sEnableCalcFrontend) ) {
            if (isset($values['_uni_cpo_data'])) {

            $sProductId = $values['product_id'];
            $aCustomOptions = uni_cpo_get_product_attributes( $sProductId );

            if ( $aCustomOptions ) :
                foreach ( $aCustomOptions as $aOption ) :

		        if ( empty( $aOption['is_visible'] ) || ( $aOption['is_taxonomy'] && ! taxonomy_exists( $aOption['name'] ) ) ) {
			        continue;
		        }

                switch ($aOption['cpo_type']) {
                    case 'input':
                            if ( $aOption['value'] ) {
                                foreach ( $aOption['value'] as $sValue ) {
                                    $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                    if ( !empty($values['_uni_cpo_data']['uni_cpo_'.$sValue]) ) {
                                        woocommerce_add_order_item_meta( $item_id, $oTerm->name, $values['_uni_cpo_data']['uni_cpo_'.$sValue] );
                                    }
                                }
                            }
                        break;
                    case 'input_number':
                            if ( $aOption['value'] ) {
                                foreach ( $aOption['value'] as $sValue ) {
                                    $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                    woocommerce_add_order_item_meta( $item_id, $oTerm->name, $values['_uni_cpo_data']['uni_cpo_'.$sValue] );
                                }
                            }
                        break;
                    case 'select':
                                $sParentTermSlug = $aOption['name'];
                                $sFieldValue = $values['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug];
                                $oTerm = get_term_by('slug', $sFieldValue, $sParentTermSlug);
                                if ( $sFieldValue ) {
                                    woocommerce_add_order_item_meta( $item_id, wc_attribute_label( $aOption['name'] ), $oTerm->name );
                                }
                        break;
                    case 'radio':
                                $sParentTermSlug = $aOption['name'];
                                $sFieldValue = $values['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug];
                                $oTerm = get_term_by('slug', $sFieldValue, $sParentTermSlug);
                                if ( $sFieldValue ) {
                                    woocommerce_add_order_item_meta( $item_id, wc_attribute_label( $aOption['name'] ), $oTerm->name );
                                }
                        break;
                    case 'checkbox':
                            if ( $aOption['value'] ) {
                                foreach ( $aOption['value'] as $sValue ) {
                                    $sFieldValue = $values['_uni_cpo_data']['uni_cpo_'.$sValue];
                                    $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                    if ( $sFieldValue ) {
                                        woocommerce_add_order_item_meta( $item_id, $oTerm->name, __('Checked', 'uni-cpo') );
                                    }
                                }
                            }
                        break;
                    case 'checkbox_multiple':
                        if ( isset($values['_uni_cpo_data']['uni_cpo_'.$aOption['name']]) && !empty($values['_uni_cpo_data']['uni_cpo_'.$aOption['name']]) ) {
                            $aCheckboxedTermsName = array();
                                foreach ( $values['_uni_cpo_data']['uni_cpo_'.$aOption['name']] as $sValue ) {
                                    $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                    $aCheckboxedTermsName[] = $oTerm->name;
                                }
                                if ( isset($aCheckboxedTermsName) && !empty($aCheckboxedTermsName) ) {
                                    $sCheckboxedTermsName = implode(', ', $aCheckboxedTermsName);
                                    woocommerce_add_order_item_meta( $item_id, wc_attribute_label( $aOption['name'] ), $sCheckboxedTermsName );
                                }
                        }
                        break;
                    case 'color':
                                $sParentTermSlug = $aOption['name'];
                                $sFieldValue = $values['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug];
                                if ( $sFieldValue ) {
                                    foreach ( $aOption['value'] as $sValue ) {
                                        $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                        $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                        if ( $aTermData['attr_color_code'] == $sFieldValue ) {
                                            woocommerce_add_order_item_meta( $item_id, wc_attribute_label( $aOption['name'] ), $oTerm->name );
                                        }
                                    }
                                }
                        break;
                    case 'color_ext':
                                $sParentTermSlug = $aOption['name'];
                                $sFieldValue = $values['_uni_cpo_data']['uni_cpo_'.$sParentTermSlug];
                                if ( $sFieldValue ) {
                                    foreach ( $aOption['value'] as $sValue ) {
                                        $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                        $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                        woocommerce_add_order_item_meta( $item_id, wc_attribute_label( $aOption['name'] ), $sFieldValue );
                                    }
                                }
                        break;
                    case 'textarea':
                            if ( $aOption['value'] ) {
                                foreach ( $aOption['value'] as $sValue ) {
                                    $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                    woocommerce_add_order_item_meta( $item_id, $oTerm->name, $values['_uni_cpo_data']['uni_cpo_'.$sValue] );
                                }
                            }
                        break;
                }

                endforeach;
            endif;

            }
        }

}

// calculate and recalc price in cart
function uni_cpo_calculate_price_in_cart( $aData, $sProductId ) {

        $aProductCustom = get_post_custom( $sProductId );
        $aCustomOptions = uni_cpo_get_product_attributes( $sProductId );
        $aArr = array();

        if ( $aCustomOptions ) :
            foreach ( $aCustomOptions as $aOption ) :

		    if ( empty( $aOption['is_visible'] ) || ( $aOption['is_taxonomy'] && ! taxonomy_exists( $aOption['name'] ) ) ) {
			    continue;
		    }

            switch ($aOption['cpo_type']) {
                case 'input':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aData['uni_cpo_'.$sValue]) && !empty($aData['uni_cpo_'.$sValue]) ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $aData['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'input_number':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aData['uni_cpo_'.$sValue]) && !empty($aData['uni_cpo_'.$sValue]) ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $aData['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'select':
                                $sParentTermSlug = $aOption['name'];
                                if ( isset($aData['uni_cpo_'.$sParentTermSlug]) && !empty($aData['uni_cpo_'.$sParentTermSlug]) ) {
                                    $oTerm = get_term_by('slug', $aData['uni_cpo_'.$sParentTermSlug], $aOption['name']);
                                    $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = $aTermData['attr_price'];
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'radio':
                                $sParentTermSlug = $aOption['name'];
                                if ( isset($aData['uni_cpo_'.$sParentTermSlug]) && !empty($aData['uni_cpo_'.$sParentTermSlug]) ) {
                                    $oTerm = get_term_by('slug', $aData['uni_cpo_'.$sParentTermSlug], $aOption['name']);
                                    $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = $aTermData['attr_price'];
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'checkbox':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aData['uni_cpo_'.$sValue]) && !empty($aData['uni_cpo_'.$sValue]) ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $aData['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'checkbox_multiple':
                        $aCheckboxed = ( isset($aData['uni_cpo_'.$aOption['name']]) ) ? $aData['uni_cpo_'.$aOption['name']] : '';
                        if ( isset($aCheckboxed) && !empty($aCheckboxed) ) {
                            foreach ( $aCheckboxed as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aTermData['attr_price']) ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $aTermData['attr_price'];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'color':
                                $sParentTermSlug = $aOption['name'];
                                if ( isset($aData['uni_cpo_'.$sParentTermSlug]) && !empty($aData['uni_cpo_'.$sParentTermSlug]) ) {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                    foreach ( $aOption['value'] as $sValue ) {
                                        $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                        $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                        if ( $aTermData['attr_color_code'] == $aData['uni_cpo_'.$sParentTermSlug] ) {
                                            $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = $aTermData['attr_price'];
                                        }
                                    }
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'color_ext':
                                $sParentTermSlug = $aOption['name'];
                                if ( isset($aData['uni_cpo_'.$sParentTermSlug]) && !empty($aData['uni_cpo_'.$sParentTermSlug]) ) {
                                    foreach ( $aOption['value'] as $sValue ) {
                                        $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                        $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                        $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = $aTermData['attr_price'];
                                    }
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'textarea':
                        /*
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( $aData['uni_cpo_'.$sValue] ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $aData['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                        */
                    break;
            }

            endforeach;
        endif;


        $sGlobalFormula         = $aProductCustom['_uni_cpo_formula'][0];

        // conditions
        if ( isset($aProductCustom['_uni_cpo_conditions_enable'][0]) && !empty($aProductCustom['_uni_cpo_conditions_enable'][0]) ) {
            $aConditions = maybe_unserialize($aProductCustom['_uni_cpo_conditions_data'][0]);

            if ( isset($aConditions) && !empty( $aConditions ) ) {

                if ( isset( $aData ) && !empty( $aData ) ) {
                    $aPostData = $aData;
                } else {
                    $aPostData = $_POST;
                }
                $sConditionFormula = uni_cpo_conditions_logic( $aConditions, $aPostData, false );
                if ( isset($sConditionFormula) && !empty($sConditionFormula) ) {
                    $sGlobalFormula = $sConditionFormula;
                }

            }

        }

        if ( !$aProductCustom['_sale_price'][0] ) {
            $sProductPrice = $aProductCustom['_regular_price'][0];
        } else {
            //sale price
            $sProductPrice = $aProductCustom['_sale_price'][0];
        }

        $sGlobalFormula	        = str_replace("{uni_cpo_price}", $sProductPrice, $sGlobalFormula);
        if ( $aArr ) {
            foreach ( $aArr as $Key => $Value ) {
                $sSearch = "/($Key)/";
                $sGlobalFormula         = preg_replace($sSearch, $Value, $sGlobalFormula);
            }
        }

        // change all forgotten cpo vars to zero
        $search = "/{([^}]*)}/";
        $sGlobalFormula = preg_replace($search,'0',$sGlobalFormula);

        $m = new EvalMath;
        $m->suppress_errors = true;
        $sOrderPrice = $m->evaluate($sGlobalFormula);
        $fOrderPrice = floatval( $sOrderPrice );
        $fMinPrice = floatval( $aProductCustom['_uni_cpo_min_price'][0] );

        if ( !empty($fMinPrice) && ( $fOrderPrice < $fMinPrice ) ) {
           return $aProductCustom['_uni_cpo_min_price'][0];
        } else {
            return $fOrderPrice;
        }

}

// ajax calculate and recalc price when dimmensions changed
function uni_cpo_calculate_price_ajax() {

    $aResult['status'] = 'error';
    $sProductId = $_POST['uni_cpo_product_id'];
    $aProductCustom = get_post_custom( $sProductId );

    if( $aProductCustom['_uni_cpo_enable_custom_options_calc'][0] == 'yes' ) {

        $aCustomOptions = uni_cpo_get_product_attributes( $sProductId );
        $aArr = array();

        if ( $aCustomOptions ) :
            foreach ( $aCustomOptions as $aOption ) :

		    if ( empty( $aOption['is_visible'] ) || ( $aOption['is_taxonomy'] && ! taxonomy_exists( $aOption['name'] ) ) ) {
			    continue;
		    }

            switch ($aOption['cpo_type']) {
                case 'input':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($_POST['uni_cpo_'.$sValue]) && !empty($_POST['uni_cpo_'.$sValue]) ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $_POST['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'input_number':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($_POST['uni_cpo_'.$sValue]) && !empty($_POST['uni_cpo_'.$sValue]) ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $_POST['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'select':
                                $sParentTermSlug = $aOption['name'];
                                //$sFieldValue = $_POST['uni_cpo_'.$sParentTermSlug];
                                if ( isset($_POST['uni_cpo_'.$sParentTermSlug]) && !empty($_POST['uni_cpo_'.$sParentTermSlug]) ) {
                                    $oTerm = get_term_by('slug', $_POST['uni_cpo_'.$sParentTermSlug], $aOption['name']);
                                    $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = floatval( $aTermData['attr_price'] );
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'checkbox':
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($_POST['uni_cpo_'.$sValue]) ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $_POST['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'checkbox_multiple':
                        $aCheckboxed = ( isset($_POST['uni_cpo_'.$aOption['name']]) ) ? $_POST['uni_cpo_'.$aOption['name']] : '';
                        if ( isset($aCheckboxed) && !empty($aCheckboxed) ) {
                            foreach ( $aCheckboxed as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( isset($aTermData['attr_price']) ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = floatval( $aTermData['attr_price'] );
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                    break;
                case 'radio':
                                $sParentTermSlug = $aOption['name'];
                                //$sFieldValue = $_POST['uni_cpo_'.$sParentTermSlug];
                                if ( isset($_POST['uni_cpo_'.$sParentTermSlug]) && !empty($_POST['uni_cpo_'.$sParentTermSlug]) ) {
                                    $oTerm = get_term_by('slug', $_POST['uni_cpo_'.$sParentTermSlug], $aOption['name']);
                                    $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = floatval( $aTermData['attr_price'] );
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'color':
                                $sParentTermSlug = $aOption['name'];
                                //$sFieldValue = $_POST['uni_cpo_'.$sParentTermSlug];
                                if ( isset($_POST['uni_cpo_'.$sParentTermSlug]) && !empty($_POST['uni_cpo_'.$sParentTermSlug]) ) {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                    foreach ( $aOption['value'] as $sValue ) {
                                        $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                        $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                        if ( $aTermData['attr_color_code'] == $_POST['uni_cpo_'.$sParentTermSlug] ) {
                                            $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = floatval( $aTermData['attr_price'] );
                                        }
                                    }
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'color_ext':
                                $sParentTermSlug = $aOption['name'];
                                $sFieldValue = $_POST['uni_cpo_'.$sParentTermSlug];
                                if ( isset($_POST['uni_cpo_'.$sParentTermSlug]) && !empty($_POST['uni_cpo_'.$sParentTermSlug]) ) {
                                    foreach ( $aOption['value'] as $sValue ) {
                                        $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                        $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                        $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = floatval( $aTermData['attr_price'] );
                                    }
                                } else {
                                    $aArr['{uni_cpo_'.$sParentTermSlug.'}'] = '0';
                                }
                    break;
                case 'textarea':
                        /*
                        if ( $aOption['value'] ) {
                            foreach ( $aOption['value'] as $sValue ) {
                                $oTerm = get_term_by('slug', $sValue, $aOption['name']);
                                $aTermData = get_option('uni_cpo_attr_'.$oTerm->term_id.'_data');
                                if ( $_POST['uni_cpo_'.$sValue] ) {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = $_POST['uni_cpo_'.$sValue];
                                } else {
                                    $aArr['{uni_cpo_'.$sValue.'}'] = '0';
                                }
                            }
                        }
                        */
                    break;
            }

            endforeach;
        endif;

        $sGlobalFormula         = $aProductCustom['_uni_cpo_formula'][0];

        // conditions
        if ( isset($aProductCustom['_uni_cpo_conditions_enable'][0]) && !empty($aProductCustom['_uni_cpo_conditions_enable'][0]) ) {
            $aConditions = maybe_unserialize($aProductCustom['_uni_cpo_conditions_data'][0]);

            if ( isset($aConditions) && !empty( $aConditions ) ) {

                $aPostData = $_POST;
                $sConditionFormula = uni_cpo_conditions_logic( $aConditions, $aPostData, true );
                if ( isset($sConditionFormula) && !empty($sConditionFormula) ) {
                    $sGlobalFormula = $sConditionFormula;
                }

            }

        }
        //print_r(' || '.$sGlobalFormula);
        if ( !$aProductCustom['_sale_price'][0] ) {
            $sProductPrice = $aProductCustom['_regular_price'][0];
        } else {
            //sale price
            $sProductPrice = $aProductCustom['_sale_price'][0];
        }

        $sGlobalFormula	        = str_replace("{uni_cpo_price}", $sProductPrice, $sGlobalFormula);
        //print_r($sGlobalFormula);
        if ( $aArr ) {
            foreach ( $aArr as $Key => $Value ) {
                if ( is_array($Value) ) {
                    if ( !empty($Value) ) {
                        foreach ( $Value as $ChildKey => $ChildValue ) {
                            $ChildKey = '{'.$ChildKey.'}';
                            $sSearch = "/($ChildKey)/";
                            $sGlobalFormula         = preg_replace($sSearch, $ChildValue, $sGlobalFormula);
                        }
                    }
                } else {
                    $sSearch = "/($Key)/";
                    $sGlobalFormula         = preg_replace($sSearch, $Value, $sGlobalFormula);
                }
            }
        }

        // change all forgotten cpo vars to zero
        $search = "/{([^}]*)}/";
        $sGlobalFormula = preg_replace($search,'0',$sGlobalFormula);
        //print_r($sGlobalFormula);
        $m = new EvalMath;
        $m->suppress_errors = true;
        $sOrderPrice = $m->evaluate($sGlobalFormula);
        $fOrderPrice = floatval( $sOrderPrice );
        $fMinPrice = floatval( $aProductCustom['_uni_cpo_min_price'][0] );

        $aResult['status'] = 'success';
        if ( !empty($fMinPrice) && ( $fOrderPrice < $fMinPrice ) ) {
           $aResult['message'] = uni_cpo_get_formatted_price( $fMinPrice );
           $aResult['total'] = uni_cpo_get_formatted_price( $fMinPrice*$_POST['uni_quantity'] );
        } else {
            $aResult['message'] = uni_cpo_get_formatted_price( $fOrderPrice );
            $aResult['total'] = uni_cpo_get_formatted_price( $fOrderPrice*$_POST['uni_quantity'] );
        }

        echo json_encode($aResult);
        die();

    } else {
        echo json_encode($aResult);
        die();
    }

}

function uni_cpo_conditions_logic( $aConditions, $aPostData, $bIsAjax ) {

                $sConditionFormula = '';
                $bConditionReached = false;
                foreach ( $aConditions as $sKey => $aValue ) {

                    if ( $bIsAjax == false ) {
                        if ( isset($aValue['left_var']) && $aValue['left_var'] == 'uni_quantity' ) {
                            $aValue['left_var'] = 'quantity';
                        }
                        if ( isset($aValue['right_var']) && $aValue['right_var'] == 'uni_quantity' ) {
                            $aValue['right_var'] = 'quantity';
                        }
                    }
                    //print_r($aPostData);
                    $sLeftVar = $aValue['left_var'];
                    $sRightVar = ( isset($aValue['right_var']) ) ? $aValue['right_var'] : '';
                    //print_r($aPostData[$sLeftVar].' | ');
                    if ( isset($aPostData[$sLeftVar]) && !empty($aPostData[$sLeftVar]) && $bConditionReached == false ) {

                        if ( isset($aValue['conj_operator']) && !empty($aValue['conj_operator']) && isset($aPostData[$sRightVar]) && !empty($aPostData[$sRightVar])
                                && $aValue['conj_operator'] == 'and' ) {

                            if ( $aValue['left_operator'] == 'less' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) && $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) && $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'less_equal' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) && $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) && $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'equal' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula      = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) && $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) && $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'greater_equal' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) && $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) && $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'greater' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) && floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) && $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) && $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'is' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] && floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] && floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] && floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] && floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] && floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] && $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] && $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'isnot' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] && floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] && floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] && floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] && floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] && floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] && $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] && $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            }

                        } else if ( isset($aValue['conj_operator']) && !empty($aValue['conj_operator']) && isset($aPostData[$sRightVar]) && !empty($aPostData[$sRightVar])
                                && $aValue['conj_operator'] == 'or' ) {

                            if ( $aValue['left_operator'] == 'less' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) || $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) || $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'less_equal' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) || $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) || $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'equal' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) || $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) || $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'greater_equal' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) || $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) || $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'greater' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) || floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) || $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) || $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'is' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] || floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] || floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] || floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] || floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] || floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] || $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( $aPostData[$sLeftVar] == $aValue['left_value'] || $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            } else if ( $aValue['left_operator'] == 'isnot' ) {
                                if ( $aValue['right_operator'] == 'less' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] || floatval($aPostData[$sRightVar]) < floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'less_equal' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] || floatval($aPostData[$sRightVar]) <= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'equal' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] || floatval($aPostData[$sRightVar]) == floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater_equal' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] || floatval($aPostData[$sRightVar]) >= floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'greater' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] || floatval($aPostData[$sRightVar]) > floatval($aValue['right_value']) ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'is' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] || $aPostData[$sRightVar] == $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                } else if ( $aValue['right_operator'] == 'isnot' ) {
                                    if ( $aPostData[$sLeftVar] != $aValue['left_value'] || $aPostData[$sRightVar] != $aValue['right_value'] ) {
                                        $sConditionFormula         = $aValue['formula'];
                                        $bConditionReached      = true;
                                    }
                                }
                            }

                        } else {
                            //print_r($aPostData[$sLeftVar]);
                            if ( $aValue['left_operator'] == 'less' ) {
                                if ( floatval($aPostData[$sLeftVar]) < floatval($aValue['left_value']) ) {
                                    $sConditionFormula         = $aValue['formula'];
                                    $bConditionReached      = true;
                                }
                            } else if ( $aValue['left_operator'] == 'less_equal' ) {
                                if ( floatval($aPostData[$sLeftVar]) <= floatval($aValue['left_value']) ) {
                                    $sConditionFormula         = $aValue['formula'];
                                    $bConditionReached      = true;
                                }
                            } else if ( $aValue['left_operator'] == 'equal' ) {
                                if ( floatval($aPostData[$sLeftVar]) == floatval($aValue['left_value']) ) {
                                    $sConditionFormula         = $aValue['formula'];
                                    $bConditionReached      = true;
                                }
                            } else if ( $aValue['left_operator'] == 'greater_equal' ) {
                                if ( floatval($aPostData[$sLeftVar]) >= floatval($aValue['left_value']) ) {
                                    $sConditionFormula         = $aValue['formula'];
                                    $bConditionReached      = true;
                                }
                            } else if ( $aValue['left_operator'] == 'greater' ) {
                                if ( floatval($aPostData[$sLeftVar]) > floatval($aValue['left_value']) ) {
                                    $sConditionFormula         = $aValue['formula'];
                                    $bConditionReached      = true;
                                }
                            } else if ( $aValue['left_operator'] == 'is' ) {
                                if ( $aPostData[$sLeftVar] == $aValue['left_value'] ) {
                                    $sConditionFormula         = $aValue['formula'];
                                    $bConditionReached      = true;
                                }
                            } else if ( $aValue['left_operator'] == 'isnot' ) {
                                if ( $aPostData[$sLeftVar] != $aValue['left_value'] ) {
                                    $sConditionFormula         = $aValue['formula'];
                                    $bConditionReached      = true;
                                }
                            }
                        }

                    }
                    //} else if ( !isset($aPostData[$sLeftVar]) || empty($aPostData[$sLeftVar]) && $bConditionReached == false ) {

                }
                return $sConditionFormula;

}

?>