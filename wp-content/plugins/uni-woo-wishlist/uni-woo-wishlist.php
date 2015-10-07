<?php
/*
Plugin Name: Uni Woo Wish & Bridal Lists
Plugin URI: http://moomoo.agency
Description: A comprehensive, modern and flexible Wish and Bridal lists for WooCommerce.
Version: 1.1.0
Author: MooMoo Web Studio
Author URI: http://moomoo.agency
License: GPL2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uni_WC_Wishlist' ) ) :

/**
 * Uni_WC_Wishlist Class
 */
final class Uni_WC_Wishlist {

	public $version = '1.1.0';

	protected static $_instance = null;
    public $wishlist_ajax = null;

	/**
	 * Uni_WC_Wishlist Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();         
		}
		return self::$_instance;
	}

	/**
	 * Uni_WC_Wishlist Constructor.
	 */
	public function __construct() {
		$this->includes();
		$this->init_hooks();

        // shortcodes
        add_shortcode( 'uni-wishlist', array( $this, 'wishlist_shortcode' ) );
        add_shortcode( 'uni-bridallist', array( $this, 'bridallist_shortcode' ) );

        //Activation and Deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'uni_plugin_activate') );
        register_deactivation_hook( __FILE__, array( $this, 'uni_plugin_deactivate') );
	}

    private function includes() {
        include_once( 'includes/uni-wc-wishlist-functions.php' );
        include_once( 'includes/uni-class-wishlist-ajax.php' );
    }

    private function init_hooks() {
        add_action( 'init', array( $this, 'init' ), 0 );
        add_action( 'wp', array( $this, 'uni_bridallist_404_if_no_user' ) );
    }

	/**
	 * Init
	 */
	public function init() {

        // start session
        if (!session_id()) {
            session_start();   
        }

        add_action( 'init', array( $this, 'admin_options_init' ), 10 );
        add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ), 10 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 10 );

        $this->wishlist_ajax = new UniWishlistAjax();

        $this->bridallist_add_page();

        add_action( 'wp_ajax_uni_bridallist_add_to_cart', array( $this, 'add_to_cart' ) );
        add_action( 'wp_ajax_nopriv_uni_bridallist_add_to_cart', array( $this, 'add_to_cart' ) );
        add_action( 'wp_ajax_uni_bridallist_add', array( $this, 'bridallist_add_ajax' ) );
        add_action( 'wp_ajax_uni_bridallist_delete', array( $this, 'bridallist_delete_ajax' ) );
        add_action( 'wp_ajax_uni_wc_wishlist_bridal_title_inline_edit', array( $this, 'bridallist_title_inline_edit' ) );

        //add_action( 'show_user_profile', array( $this, 'uni_bridallist_user_fields') );
        //add_action( 'edit_user_profile', array( $this, 'uni_bridallist_user_fields') );
        //add_action( 'personal_options_update', array( $this, 'uni_bridallist_user_fields_save') );
        //add_action( 'edit_user_profile_update', array( $this, 'uni_bridallist_user_fields_save') );

		// Multilanguage support
		$this->load_plugin_textdomain();

	}

	/**
	 * plugin options page
	 */
    function admin_options_init() {
        add_action('admin_menu', array( $this, 'create_menu') );
    }

	/**
	 * plugin's menu
	 */
    function create_menu() {
	    add_submenu_page( 'woocommerce', __('Uni Wish & Bridal Lists', 'uni-wishlist'), __('Uni Wish & Bridal Lists', 'uni-wishlist'), 'manage_woocommerce', 'uni-wc-wishlist-options', array( $this, 'options_function' ) );
	    add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

	/**
	 *
	 */
    function register_settings() {
        register_setting( 'uni-wishlist-settings-group', 'uni_wishlist_link_position' );
        register_setting( 'uni-wishlist-settings-group', 'uni_bridallist_enable' );

        register_setting( 'uni-wishlist-settings-group', 'uni_wishlist_style' );
        register_setting( 'uni-wishlist-settings-group', 'uni_wishlist_fa_tag' );
        register_setting( 'uni-wishlist-settings-group', 'uni_wishlist_fa_tag_na' );
        register_setting( 'uni-wishlist-settings-group', 'uni_bridallist_fa_tag' );
        register_setting( 'uni-wishlist-settings-group', 'uni_bridallist_fa_tag_na' );
    }

	/**
	 *
	 */
    function options_function() {
    ?>
        <div class="wrap">
            <h2><?php _e('Uni Wish & Bridal Lists Plugin Options', 'uni-wishlist') ?></h2>

            <form method="post" action="options.php">
            <?php settings_fields( 'uni-wishlist-settings-group' ); ?>
            <?php do_settings_sections( 'uni-wishlist-settings-group' ); ?>

            <h3><?php _e('General settings', 'uni-wishlist') ?></h3>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <?php _e('"Add to wishlist" link position', 'uni-wishlist') ?>
                    </th>
                    <td>
                    <?php
                    $aPositionOptions = $this->woo_hooks();
                    ?>
                        <select name="uni_wishlist_link_position">
                        <?php if ( !empty($aPositionOptions) ) {
                            foreach ( $aPositionOptions as $sSlug => $aPosition ) {
                        ?>
                            <option value="<?php echo esc_attr( $sSlug ) ?>"<?php echo selected( get_option('uni_wishlist_link_position'), $sSlug ); ?>><?php echo esc_html( $aPosition['title'] ) ?></option>
                        <?php
                            }
                        }
                        ?>
                        </select>
                        <p class="description"><?php _e('Choose a position for "Add to Wish List" link. If you decide to use "bridal list" functionality, "Add to Bridal List" link will be added next to "Add to Wish List" link.', 'uni-wishlist') ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Enable/disable "bridal list" functionality', 'uni-wishlist') ?>
                    </th>
                    <td>
                        <input type="checkbox" name="uni_bridallist_enable" value="1"<?php checked('1', get_option('uni_bridallist_enable')) ?> />
                        <p class="description"><?php _e('You can enable/disable "bridal list" functionality here.', 'uni-wishlist') ?></p>
                    </td>
                </tr>
            </table>

            <h3><?php _e('Visual appearance', 'uni-wishlist') ?></h3>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Style of Wish and Bridal lists links (both states)', 'uni-wishlist') ?>
                    </th>
                    <td>
                        <select name="uni_wishlist_style">
                            <option value="default"<?php echo selected( get_option('uni_wishlist_style'), 'default' ); ?>><?php _e('Default (no icons)', 'uni-wishlist') ?></option>
                            <option value="heart-and-gift"<?php echo selected( get_option('uni_wishlist_style'), 'heart-and-gift' ); ?>><?php _e('Heart and Gift icons', 'uni-wishlist') ?></option>
                            <option value="star-and-gift"<?php echo selected( get_option('uni_wishlist_style'), 'star-and-gift' ); ?>><?php _e('Star and Gift icons', 'uni-wishlist') ?></option>
                            <option value="heart-and-venus-mars"<?php echo selected( get_option('uni_wishlist_style'), 'heart-and-venus-mars' ); ?>><?php _e('Heart and Venus-Mars icons', 'uni-wishlist') ?></option>
                            <option value="custom"<?php echo selected( get_option('uni_wishlist_style'), 'custom' ); ?>><?php _e('Custom (define custom fontawesome icons)', 'uni-wishlist') ?></option>
                        </select>
                        <p class="description"><?php echo sprintf( __('Choose one of prepared styles (icons) or choose "Custom" and define your fontawesome icons (%s a full list of fontawesome icons here %s).', 'uni-wishlist'), '<a href="http://fortawesome.github.io/Font-Awesome/icons/">', '</a>') ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Custom fontawesome icon for Wish list link (added)', 'uni-wishlist') ?>
                    </th>
                    <td>
                        <input type="text" name="uni_wishlist_fa_tag" value="<?php echo get_option('uni_wishlist_fa_tag') ?>" />
                        <p class="description"><?php _e('Example: "fa-amazon". Another example: "fa-gift"', 'uni-wishlist') ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Custom fontawesome icon for Wish list link (not added)', 'uni-wishlist') ?>
                    </th>
                    <td>
                        <input type="text" name="uni_wishlist_fa_tag_na" value="<?php echo get_option('uni_wishlist_fa_tag_na') ?>" />
                        <p class="description"><?php _e('Example: "fa-amazon". Another example: "fa-gift"', 'uni-wishlist') ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Custom fontawesome icon for Bridal list link (added)', 'uni-wishlist') ?>
                    </th>
                    <td>
                        <input type="text" name="uni_bridallist_fa_tag" value="<?php echo get_option('uni_bridallist_fa_tag') ?>" />
                        <p class="description"><?php _e('Example: "fa-amazon". Another example: "fa-gift"', 'uni-wishlist') ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <?php _e('Custom fontawesome icon for Bridal list link (not added)', 'uni-wishlist') ?>
                    </th>
                    <td>
                        <input type="text" name="uni_bridallist_fa_tag_na" value="<?php echo get_option('uni_bridallist_fa_tag_na') ?>" />
                        <p class="description"><?php _e('Example: "fa-amazon". Another example: "fa-gift"', 'uni-wishlist') ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>

            </form>
        </div>
    <?php
    }

	/**
	 *
	 */
    function woo_hooks() {
        $aHooks = apply_filters( 'uni_wishlist_link_positions',
                    array(
                        'none' => array( 'title' => __('None (insert by php function by myself)', 'uni-wishlist'), 'name' => '', 'priority' => 0 ),
                        'after_add_to_cart' => array( 'title' => __('After Add to Cart', 'uni-wishlist'), 'name' => 'woocommerce_single_product_summary', 'priority' => 30 ),
                        'after_thumb' => array( 'title' => __('After Thumbnail', 'uni-wishlist'), 'name' => 'woocommerce_product_thumbnails', 'priority' => 40 ),
                        'after_product_summary' => array( 'title' => __('After Product Summary', 'uni-wishlist'), 'name' => 'woocommerce_single_product_summary', 'priority' => 40 )
                    )
        );
        return $aHooks;
    }

	/**
	 * load_plugin_textdomain()
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'uni-wishlist' );

		load_textdomain( 'uni-wishlist', WP_LANG_DIR . '/uni-woo-wishlist/uni-wishlist-' . $locale . '.mo' );
		load_plugin_textdomain( 'uni-wishlist', false, plugin_basename( dirname( __FILE__ ) ) . "/languages" );
	}

	/**
	*  front_scripts()
    */
    function front_scripts() {

        wp_enqueue_script( 'jquery' );
        global $wp_scripts;

       /* $wp_scripts->registered[ 'wc-add-to-cart' ]->src = $this->plugin_url().'/assets/js/uni-add-to-cart.js';
*/
        wp_register_script( 'jquery-jeditable', $this->plugin_url().'/assets/js/jquery.jeditable.mini.js', array('jquery'), '1.7.3' );
        wp_enqueue_script( 'jquery-jeditable' );
        wp_register_script( 'uni-wc-wishlist', $this->plugin_url().'/assets/js/uni-wc-wishlist.js', array('jquery', 'jquery-blockui'), $this->version);
        wp_enqueue_script( 'uni-wc-wishlist' );

        if ( get_option('uni_bridallist_enable') ) {
            $params = array(
                'site_url'          => get_bloginfo('url'),
		        'ajax_url' 		    => $this->ajax_url(),
                'loader'            => $this->plugin_url().'/assets/images/preloader.gif',
                'bridal_enabled'    => true,
                'indicator_text'    => __( 'Saving...', 'uni-wishlist' ),
                'tooltip_text'      => __( 'Click to edit the title', 'uni-wishlist' ),
                'cancel_text'       => __( 'Cancel', 'uni-wishlist' ),
                'save_text'         => __( 'Save', 'uni-wishlist' )
	        );
        } else {
            $params = array(
                'site_url'          => get_bloginfo('url'),
		        'ajax_url' 		    => $this->ajax_url(),
                'loader'            => $this->plugin_url().'/assets/images/preloader.gif',
                'bridal_enabled'    => false
	        );
        }
	    wp_localize_script( 'uni-wc-wishlist', 'uniwcwishlist', $params );

        wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
        wp_enqueue_style( 'uni-wc-wishlist-styles', $this->plugin_url().'/assets/css/uni-wc-wishlist-styles.css', array('font-awesome'), $this->version, 'all');

    }

	/**
	*  admin_scripts()
    */
    function admin_scripts() {

        wp_enqueue_script( 'jquery' );

        wp_register_script( 'uni-wc-wishlist-admin', $this->plugin_url().'/assets/js/uni-wc-wishlist-admin.js', array('jquery', 'jquery-blockui'), $this->version);
        wp_enqueue_script( 'uni-wc-wishlist-admin' );

        wp_enqueue_style( 'uni-wc-wishlist-styles-admin', $this->plugin_url().'/assets/css/uni-wc-wishlist-styles-admin.css', false, $this->version, 'all');

    }

	/**
	 * plugin_url()
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * plugin_path()
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * ajax_url()
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php' );
	}

	/**
	 * is_in_wishlist
	 */
    function is_in_wishlist( $iProductId, $iVariationId = null ) {
        // for registered and authorized users
        if ( is_user_logged_in() ) {

            $iUserId        = get_current_user_id();
            $aWishlistData  = ( get_user_meta($iUserId, '_uni_wc_wishlist_data', true) ) ? get_user_meta($iUserId, '_uni_wc_wishlist_data', true) : array();

            // not a variable product
            if ( $iVariationId == null ) {
                if ( !empty($aWishlistData) && !empty($aWishlistData[$iProductId]) ) {
                    return true;
                } else {
                    return false;
                }

            // a variable product
            } else {
                if ( !empty($aWishlistData) && !empty($aWishlistData[$iProductId]) && in_array($iVariationId, $aWishlistData[$iProductId]['vid']) ) {
                    return true;
                } else {
                    return false;
                }
            }

        // for guests
        } else {

            $aWishlistData  = ( isset($_SESSION['uni_wc_wishlist_data']) ) ? $_SESSION['uni_wc_wishlist_data'] : array();

            // not a variable product
            if ( $iVariationId == null ) {
                if ( !empty($aWishlistData) && !empty($aWishlistData[$iProductId]) ) {
                    return true;
                } else {
                    return false;
                }

            // a variable product
            } else {
                if ( !empty($aWishlistData) && !empty($aWishlistData[$iProductId]) && in_array($iVariationId, $aWishlistData[$iProductId]['vid']) ) {
                    return true;
                } else {
                    return false;
                }
            }

        }

    }

	/**
	 * wishlist_link_raw
	 */
    function wishlist_link_raw( $iProductId, $iVariationId = null, $sState, $sType ) {

        $sWishlistLinkRaw = '';

        if ( $iVariationId == null && $sState == 'not-added' && $sType == 'not-variable' ) {

            $aWishlistLinkClasses = apply_filters( 'uni_wc_wishlist_link_classes', array('uni-wishlist-link'), 'not-added', 'not-variable' );
            $sWishlistLinkClasses = implode(' ', $aWishlistLinkClasses);
            $sWishlistLinkTitle = apply_filters( 'uni_wc_wishlist_link_title', __('Add to Wish List', 'uni-wishlist'), 'not-added', 'not-variable' );

            $sWishlistLinkRaw = '<a href="#" data-pid="'.$iProductId.'" class="'.$sWishlistLinkClasses.'" data-action="uni_wishlist_add">'.$sWishlistLinkTitle.'</a>';

        } else if ( $iVariationId == null && $sState == 'added' && $sType == 'not-variable' ) {

            $aWishlistLinkAddedClasses = apply_filters( 'uni_wc_wishlist_link_classes', array('uni-wishlist-link', 'uni-wishlist-link-added'), 'added', 'not-variable' );
            $sWishlistLinkAddedClasses = implode(' ', $aWishlistLinkAddedClasses);
            $sWishlistLinkAddedTitle = apply_filters( 'uni_wc_wishlist_link_title', __('Added to Wish List', 'uni-wishlist'), 'added', 'not-variable' );

            $sWishlistLinkRaw = '<a href="#" data-pid="'.$iProductId.'" class="'.$sWishlistLinkAddedClasses.'" data-action="uni_wishlist_delete">'.$sWishlistLinkAddedTitle.'</a>';

        } else if ( isset($iVariationId) && !empty($iVariationId) && $sState == 'not-added' && $sType == 'variable' ) {

            $aWishlistLinkVariableClasses = apply_filters( 'uni_wc_wishlist_link_classes', array('uni-wishlist-link', 'uni-wishlist-link-variable'), 'not-added', 'variable' );
            $sWishlistLinkVariableClasses = implode(' ', $aWishlistLinkVariableClasses);
            $sWishlistLinkTitle = apply_filters( 'uni_wc_wishlist_link_title', __('Add to Wish List', 'uni-wishlist'), 'not-added', 'variable' );

            $sWishlistLinkRaw = '<a href="#" data-pid="'.$iProductId.'" data-vid="'.$iVariationId.'" class="'.$sWishlistLinkVariableClasses.'" data-action="uni_wishlist_add">'.$sWishlistLinkTitle.'</a>';

        } else if ( isset($iVariationId) && !empty($iVariationId) && $sState == 'added' && $sType == 'variable' ) {

            $aWishlistLinkAddedVariableClasses = apply_filters( 'uni_wc_wishlist_link_classes', array('uni-wishlist-link', 'uni-wishlist-link-added', 'uni-wishlist-link-variable'), 'added', 'variable' );
            $sWishlistLinkAddedVariableClasses = implode(' ', $aWishlistLinkAddedVariableClasses);
            $sWishlistLinkAddedTitle = apply_filters( 'uni_wc_wishlist_link_title', __('Added to Wish List', 'uni-wishlist'), 'added', 'variable' );

            $sWishlistLinkRaw = '<a href="#" data-pid="'.$iProductId.'" data-vid="'.$iVariationId.'" class="'.$sWishlistLinkAddedVariableClasses.'" data-action="uni_wishlist_delete">'.$sWishlistLinkAddedTitle.'</a>';

        }

        return $sWishlistLinkRaw;
    }

	/**
	 * wishlist_link
	 */
    function wishlist_link() {

        global $product;

        // NOT a variable
	    if( $product->product_type != 'variable' ) {

            $bAlreadyInWishlist = $this->is_in_wishlist( $product->id, null );
            $sWishlistLink = '';

            $aWishlistLinkContainerClasses = apply_filters( 'uni_wc_wishlist_link_container_classes', array(), 'not-added', 'not-variable' );
            if ( empty($aWishlistLinkContainerClasses) ) {
                $sWishlistLinkContainerClasses = 'uni-wishlist-link-container';
            } else {
                $sWishlistLinkContainerClasses = implode(' ', $aWishlistLinkContainerClasses);
                $sWishlistLinkContainerClasses .= ' uni-wishlist-link-container';
            }

            $sWishlistLink = '<div class="'.$sWishlistLinkContainerClasses.'">';
            $sWishlistLink .= $this->wishlist_link_raw( $product->id, null, 'not-added', 'not-variable' );
            $sWishlistLink .= '</div>';

		    if( $bAlreadyInWishlist ) {

                $aWishlistLinkAddedContainerClasses = apply_filters( 'uni_wc_wishlist_link_container_classes', array('uni-wishlist-link-added-container'), 'added', 'not-variable' );
                if ( empty($aWishlistLinkAddedContainerClasses) ) {
                    $sWishlistLinkAddedContainerClasses = 'uni-wishlist-link-container';
                } else {
                    $sWishlistLinkAddedContainerClasses = implode(' ', $aWishlistLinkAddedContainerClasses);
                    $sWishlistLinkAddedContainerClasses .= ' uni-wishlist-link-container';
                }

                $sWishlistLink = '<div class="'.$sWishlistLinkAddedContainerClasses.'">';
                $sWishlistLink .= $this->wishlist_link_raw( $product->id, null, 'added', 'not-variable' );
                $sWishlistLink .= '</div>';
		    }

	    } else {

            $bAlreadyInWishlist = false;
            $sWishlistLink = '';

            $aWishlistLinkVariableContainerClasses = apply_filters( 'uni_wc_wishlist_link_container_classes', array('uni-wishlist-link-variable-container'), 'not-added', 'variable' );
            if ( empty($aWishlistLinkVariableContainerClasses) ) {
                $sWishlistLinkVariableContainerClasses = 'uni-wishlist-link-container';
            } else {
                $sWishlistLinkVariableContainerClasses = implode(' ', $aWishlistLinkVariableContainerClasses);
                $sWishlistLinkVariableContainerClasses .= ' uni-wishlist-link-container';
            }

            $sWishlistLink = '<div class="'.$sWishlistLinkVariableContainerClasses.'">';
            $sWishlistLink .= $this->wishlist_link_raw( $product->id, null, 'not-added', 'variable' );
            $sWishlistLink .= '</div>';

        }

        return $sWishlistLink;

    }

	/**
	 * wishlist_add
	 */
    function wishlist_add( $iProductId, $iVariationId = null ) {

        $iUserId        = get_current_user_id();
        $aNewItem       = array();
        $oProduct       = wc_get_product($iProductId);

        // registered and authorized user
        if ( $iUserId != 0 ) {
            $aUsersWishlist = ( get_user_meta($iUserId, '_uni_wc_wishlist_data', true) ) ? get_user_meta($iUserId, '_uni_wc_wishlist_data', true) : array();

            // not a variable product
            if ( !$oProduct->is_type('variable') ) {
                $aNewItem[$iProductId] = array(
                                    'type' => 'nonvariable',
                                    'vid' => array()
                                );
                if ( empty($aUsersWishlist) ) {
                    update_user_meta( $iUserId, '_uni_wc_wishlist_data', $aNewItem );
                    return true;
                } else {
                    $aNewItem = $aUsersWishlist + $aNewItem;
                    update_user_meta( $iUserId, '_uni_wc_wishlist_data', $aNewItem );
                    return true;
                }
            // a variable product
            } else if ( $oProduct->is_type('variable') && $iVariationId != null ) {
                $aNewItem[$iProductId] = array(
                                    'type' => 'variable',
                                    'vid' => array($iVariationId)
                                );
                if ( empty($aUsersWishlist) ) {
                    update_user_meta( $iUserId, '_uni_wc_wishlist_data', $aNewItem );
                    return true;
                } else if ( !empty($aUsersWishlist) && empty($aUsersWishlist[$iProductId]) ) {
                    $aNewItem = $aUsersWishlist + $aNewItem;
                    update_user_meta( $iUserId, '_uni_wc_wishlist_data', $aNewItem );
                    return true;
                } else if ( !empty($aUsersWishlist) && !empty($aUsersWishlist[$iProductId]) ) {
                    $aArrayOfVariations = ( !empty($aUsersWishlist[$iProductId]['vid']) ) ? $aUsersWishlist[$iProductId]['vid'] : array();
                    $aArrayOfVariations[] = $iVariationId;
                    $aUsersWishlist[$iProductId]['vid'] = array_unique($aArrayOfVariations);
                    update_user_meta( $iUserId, '_uni_wc_wishlist_data', $aUsersWishlist );
                    return true;
                }
            }

        // for guests
        } else {

            $aUsersWishlist = ( isset($_SESSION['uni_wc_wishlist_data']) ) ? $_SESSION['uni_wc_wishlist_data'] : array();

            // not a variable product
            if ( !$oProduct->is_type('variable') ) {
                $aNewItem[$iProductId] = array(
                                    'type' => 'nonvariable',
                                    'vid' => array()
                                );
                if ( empty($aUsersWishlist) ) {
                    $_SESSION['uni_wc_wishlist_data'] = $aNewItem;
                    return true;
                } else {
                    $aNewItem = $aUsersWishlist + $aNewItem;
                    $_SESSION['uni_wc_wishlist_data'] = $aNewItem;
                    return true;
                }
            // a variable product
            } else if ( $oProduct->is_type('variable') && $iVariationId != null ) {
                $aNewItem[$iProductId] = array(
                                    'type' => 'variable',
                                    'vid' => array($iVariationId)
                                );
                if ( empty($aUsersWishlist) ) {
                    $_SESSION['uni_wc_wishlist_data'] = $aNewItem;
                    return true;
                } else if ( !empty($aUsersWishlist) && empty($aUsersWishlist[$iProductId]) ) {
                    $aNewItem = $aUsersWishlist + $aNewItem;
                    $_SESSION['uni_wc_wishlist_data'] = $aNewItem;
                    return true;
                } else if ( !empty($aUsersWishlist) && !empty($aUsersWishlist[$iProductId]) ) {
                    $aArrayOfVariations = ( !empty($aUsersWishlist[$iProductId]['vid']) ) ? $aUsersWishlist[$iProductId]['vid'] : array();
                    $aArrayOfVariations[] = $iVariationId;
                    $aUsersWishlist[$iProductId]['vid'] = array_unique($aArrayOfVariations);
                    $_SESSION['uni_wc_wishlist_data'] = $aUsersWishlist;
                    return true;
                }
            }

        }

    }

	/**
	 * wishlist_delete
	 */
    function wishlist_delete( $iProductId, $iVariationId = null ) {

        $iUserId        = get_current_user_id();
        $oProduct       = wc_get_product($iProductId);

        // registered and authorized user
        if ( $iUserId != 0 ) {
            $aUsersWishlist = ( get_user_meta($iUserId, '_uni_wc_wishlist_data', true) ) ? get_user_meta($iUserId, '_uni_wc_wishlist_data', true) : array();

            if ( isset($aUsersWishlist) && !empty($aUsersWishlist) ) :
            // not a variable product
            if ( !$oProduct->is_type('variable') ) {
                unset($aUsersWishlist[$iProductId]);
                update_user_meta( $iUserId, '_uni_wc_wishlist_data', $aUsersWishlist );
                return true;

            // a variable product
            } else if ( $oProduct->is_type('variable') && $iVariationId != null ) {

                $aVariations = $aUsersWishlist[$iProductId]['vid'];
                if ( !empty($aVariations) ) {
                    if(($key = array_search($iVariationId, $aVariations)) !== false) {
                        unset($aVariations[$key]);
                    }
                    if ( empty($aVariations) ) {
                        unset($aUsersWishlist[$iProductId]);
                    } else {
                        $aUsersWishlist[$iProductId]['vid'] = $aVariations;
                    }
                    update_user_meta( $iUserId, '_uni_wc_wishlist_data', $aUsersWishlist );
                    return true;
                }

            }
            else
                return false;
            endif;

        // for guests
        } else {

            $aUsersWishlist = ( isset($_SESSION['uni_wc_wishlist_data']) ) ? $_SESSION['uni_wc_wishlist_data'] : array();

            if ( isset($aUsersWishlist) && !empty($aUsersWishlist) ) :
            // not a variable product
            if ( !$oProduct->is_type('variable') ) {
                unset($aUsersWishlist[$iProductId]);
                $_SESSION['uni_wc_wishlist_data'] = $aUsersWishlist;
                return true;

            // a variable product
            } else if ( $oProduct->is_type('variable') && $iVariationId != null ) {

                $aVariations = $aUsersWishlist[$iProductId]['vid'];

                if ( !empty($aVariations) ) {
                    if(($key = array_search($iVariationId, $aVariations)) !== false) {
                        unset($aVariations[$key]);
                    }
                    if ( empty($aVariations) ) {
                        unset($aUsersWishlist[$iProductId]);
                    }
                    $_SESSION['uni_wc_wishlist_data'] = $aUsersWishlist;
                    return true;
                }

            }
            else
                return false;
            endif;
        }

    }

    /*
    *  get_items
    */
    function wishlist_get_items( $iUserId = null ) {

        $iCurrentUserId = get_current_user_id();
        $aUsersWishlist = array();

        if ( is_user_logged_in() ) {

            if ( $iUserId != null ) {
                $aUsersWishlist = get_user_meta($iUserId, '_uni_wc_wishlist_data', true);
            } else if ( $iUserId == null && $iCurrentUserId != 0 ) {
                $aUsersWishlist = get_user_meta($iCurrentUserId, '_uni_wc_wishlist_data', true);
            }

        } else {
            $aUsersWishlist = ( isset($_SESSION['uni_wc_wishlist_data']) ) ? $_SESSION['uni_wc_wishlist_data'] : array();
        }

        return $aUsersWishlist;
    }

    /*
    *  count_items
    */
    function wishlist_count_items( $iUserId = null ) {

        $iCurrentUserId = get_current_user_id();
        $aUsersWishlist = array();
        $iItemsCount = 0;

        if ( is_user_logged_in() ) {

            if ( $iUserId != null ) {

                $aUsersWishlist = get_user_meta($iUserId, '_uni_wc_wishlist_data', true);
                if ( !empty($aUsersWishlist) ) $iItemsCount = count($aUsersWishlist);

            } else if ( $iUserId == null && $iCurrentUserId != 0 ) {

                $aUsersWishlist = get_user_meta($iCurrentUserId, '_uni_wc_wishlist_data', true);
                if ( !empty($aUsersWishlist) ) $iItemsCount = count($aUsersWishlist);

            }

        } else {

            $aUsersWishlist = ( isset($_SESSION['uni_wc_wishlist_data']) ) ? $_SESSION['uni_wc_wishlist_data'] : array();
            if ( !empty($aUsersWishlist) ) $iItemsCount = count($aUsersWishlist);

        }

        return $iItemsCount;
    }

	/**
	 * wishlist_shortcode()
	 */
    public function wishlist_shortcode( $atts, $content = null ) {
        $aAttr = shortcode_atts( array(), $atts );
        $aUsersWishlist = uni_wc_wishlist_items();

        if ( !empty($aUsersWishlist) ) {
	        ob_start();
	        include( UniWishlist()->plugin_path().'/includes/views/wishlist.php' );
	        return ob_get_clean();
        } else {
	        ob_start();
	        include( UniWishlist()->plugin_path().'/includes/views/wishlist-empty.php' );
	        return ob_get_clean();
        }

    }

    //*************** Bridal List ********************************************************

	/**
	 * uni_bridallist_404_if_no_user()
	 */
    function uni_bridallist_404_if_no_user() {
        global $wp_query;
        $iUserId = ( !empty($wp_query->query_vars['list-id']) ) ? absint( $wp_query->query_vars['list-id'] ) : 0;

        if ( !empty($iUserId) ) {
            $oUser = get_user_by( 'id', $iUserId );
            if ( $oUser == false ) {
                $wp_query->set_404();
                status_header(404);
                get_template_part( 404 );
                exit();
            }
        }
    }

	/**
	 * bridallist_add_page()
	 */
    public function bridallist_add_page() {
        if ( get_option('uni_bridallist_enable') ) {
            add_rewrite_endpoint( 'list-id', EP_PAGES );
            add_filter( 'query_vars', array( $this, 'uni_bridallist_add_query_vars' ) );
        }
    }

	/**
	 * uni_bridallist_add_query_vars()
	 */
    public function uni_bridallist_add_query_vars( $public_query_vars ) {
        $public_query_vars[] .= 'list-id';
	    return $public_query_vars;
    }

	/**
	 * bridallist_shortcode()
	 */
    public function bridallist_shortcode( $atts, $content = null ) {
        $aAttr = shortcode_atts( array(), $atts );

        if ( get_option('uni_bridallist_enable') ) {

        global $wp_query;
        $iUserId = ( !empty($wp_query->query_vars['list-id']) ) ? absint( $wp_query->query_vars['list-id'] ) : 0;
        $iCurrentUserId = get_current_user_id();

        if ( ( is_user_logged_in() || !is_user_logged_in() ) && is_page() && !empty( $iUserId ) && $iCurrentUserId != $iUserId ) {
	        ob_start();
	        include( UniWishlist()->plugin_path().'/includes/views/bridallist.php' );
	        return ob_get_clean();
        } else if ( is_user_logged_in() && is_page() && ( !empty( $iUserId ) || empty( $iUserId ) ) ) {
	        ob_start();
	        include( UniWishlist()->plugin_path().'/includes/views/bridallist-current.php' );
	        return ob_get_clean();
        } else if ( !is_user_logged_in() && is_page() && empty( $iUserId ) ) {
	        ob_start();
	        include( UniWishlist()->plugin_path().'/includes/views/bridallist-nonreg.php' );
	        return ob_get_clean();
        }

        }

    }

	/**
	 * is_in_bridallist
	 */
    function is_in_bridallist( $iProductId, $iVariationId = null ) {
        // for registered and authorized users
        if ( is_user_logged_in() ) {

            $iUserId        = get_current_user_id();
            $aBridallistData  = ( get_user_meta($iUserId, '_uni_wc_bridallist_data', true) ) ? get_user_meta($iUserId, '_uni_wc_bridallist_data', true) : array();

            // not a variable product
            if ( $iVariationId == null ) {
                if ( !empty($aBridallistData) && !empty($aBridallistData[$iProductId]) ) {
                    return true;
                } else {
                    return false;
                }

            // a variable product
            } else {
                if ( !empty($aBridallistData) && !empty($aBridallistData[$iProductId]) && isset($aBridallistData[$iProductId]['variations'][$iVariationId]) ) {
                    return true;
                } else {
                    return false;
                }
            }

        }

    }

	/**
	 * bridallist_link_raw
	 */
    function bridallist_link_raw( $iProductId, $iVariationId = null, $sState, $sType ) {

        $sBridallistLinkRaw = '';

        if ( $iVariationId == null && $sState == 'not-added' && $sType == 'not-variable' ) {

            $aBridallistLinkClasses = apply_filters( 'uni_wc_bridallist_link_classes', array('uni-bridallist-link'), 'not-added', 'not-variable' );
            $sBridallistLinkClasses = implode(' ', $aBridallistLinkClasses);
            $sBridallistLinkTitle = apply_filters( 'uni_wc_bridallist_link_title', __('Add to Bridal List', 'uni-wishlist'), 'not-added', 'not-variable' );

            $sBridallistLinkRaw = '<a href="#" data-pid="'.$iProductId.'" class="'.$sBridallistLinkClasses.'" data-action="uni_bridallist_add">'.$sBridallistLinkTitle.'</a>';

        } else if ( $iVariationId == null && $sState == 'added' && $sType == 'not-variable' ) {

            $aBridallistLinkAddedClasses = apply_filters( 'uni_wc_bridallist_link_classes', array('uni-bridallist-link', 'uni-bridallist-link-added'), 'added', 'not-variable' );
            $sBridallistLinkAddedClasses = implode(' ', $aBridallistLinkAddedClasses);
            $sBridallistLinkAddedTitle = apply_filters( 'uni_wc_bridallist_link_title', __('Added to Bridal List', 'uni-wishlist'), 'added', 'not-variable' );

            $sBridallistLinkRaw = '<a href="#" data-pid="'.$iProductId.'" class="'.$sBridallistLinkAddedClasses.'" data-action="uni_bridallist_delete">'.$sBridallistLinkAddedTitle.'</a>';

        } else if ( isset($iVariationId) && !empty($iVariationId) && $sState == 'not-added' && $sType == 'variable' ) {

            $aBridallistLinkVariableClasses = apply_filters( 'uni_wc_bridallist_link_classes', array('uni-bridallist-link', 'uni-bridallist-link-variable'), 'not-added', 'variable' );
            $sBridallistLinkVariableClasses = implode(' ', $aBridallistLinkVariableClasses);
            $sBridallistLinkTitle = apply_filters( 'uni_wc_bridallist_link_title', __('Add to Bridal List', 'uni-wishlist'), 'not-added', 'variable' );

            $sBridallistLinkRaw = '<a href="#" data-pid="'.$iProductId.'" data-vid="'.$iVariationId.'" class="'.$sBridallistLinkVariableClasses.'" data-action="uni_bridallist_add">'.$sBridallistLinkTitle.'</a>';

        } else if ( isset($iVariationId) && !empty($iVariationId) && $sState == 'added' && $sType == 'variable' ) {

            $aBridallistLinkAddedVariableClasses = apply_filters( 'uni_wc_bridallist_link_classes', array('uni-bridallist-link', 'uni-bridallist-link-added', 'uni-bridallist-link-variable'), 'added', 'variable' );
            $sBridallistLinkAddedVariableClasses = implode(' ', $aBridallistLinkAddedVariableClasses);
            $sBridallistLinkAddedTitle = apply_filters( 'uni_wc_bridallist_link_title', __('Added to Bridal List', 'uni-wishlist'), 'added', 'variable' );

            $sBridallistLinkRaw = '<a href="#" data-pid="'.$iProductId.'" data-vid="'.$iVariationId.'" class="'.$sBridallistLinkAddedVariableClasses.'" data-action="uni_bridallist_delete">'.$sBridallistLinkAddedTitle.'</a>';

        }

        return $sBridallistLinkRaw;
    }

	/**
	 * bridallist_link
	 */
    function bridallist_link() {

        if ( is_user_logged_in() ) {

	    global $product;

        // NOT a variable
	    if( $product->product_type != 'variable' ) {

            $bAlreadyInBridallist = $this->is_in_bridallist( $product->id, null );
            $sBridallistLink = '';

            $aBridallistLinkContainerClasses = apply_filters( 'uni_wc_bridallist_link_container_classes', array(), 'not-added', 'not-variable' );
            if ( empty($aBridallistLinkContainerClasses) ) {
                $sBridallistLinkContainerClasses = 'uni-bridallist-link-container';
            } else {
                $sBridallistLinkContainerClasses = implode(' ', $aBridallistLinkContainerClasses);
                $sBridallistLinkContainerClasses .= ' uni-bridallist-link-container';
            }

            $sBridallistLink = '<div class="'.$sBridallistLinkContainerClasses.'">';
            $sBridallistLink .= $this->bridallist_link_raw( $product->id, null, 'not-added', 'not-variable' );
            $sBridallistLink .= '</div>';

		    if( $bAlreadyInBridallist ) {

                $aBridallistLinkAddedContainerClasses = apply_filters( 'uni_wc_bridallist_link_container_classes', array('uni-bridallist-link-added-container'), 'added', 'not-variable' );
                if ( empty($aBridallistLinkAddedContainerClasses) ) {
                    $sBridallistLinkAddedContainerClasses = 'uni-bridallist-link-container';
                } else {
                    $sBridallistLinkAddedContainerClasses = implode(' ', $aBridallistLinkAddedContainerClasses);
                    $sBridallistLinkAddedContainerClasses .= ' uni-bridallist-link-container';
                }

                $sBridallistLink = '<div class="'.$sBridallistLinkAddedContainerClasses.'">';
                $sBridallistLink .= $this->bridallist_link_raw( $product->id, null, 'added', 'not-variable' );
                $sBridallistLink .= '</div>';
		    }

	    } else {

            $bAlreadyInBridallist = false;
            $sBridallistLink = '';

            $aBridallistLinkVariableContainerClasses = apply_filters( 'uni_wc_bridallist_link_container_classes', array('uni-bridallist-link-variable-container'), 'not-added', 'variable' );
            if ( empty($aBridallistLinkVariableContainerClasses) ) {
                $sBridallistLinkVariableContainerClasses = 'uni-bridallist-link-container';
            } else {
                $sBridallistLinkVariableContainerClasses = implode(' ', $aBridallistLinkVariableContainerClasses);
                $sBridallistLinkVariableContainerClasses .= ' uni-bridallist-link-container';
            }

            $sBridallistLink = '<div class="'.$sBridallistLinkVariableContainerClasses.'">';
            $sBridallistLink .= $this->wishlist_link_raw( $product->id, null, 'not-added', 'variable' );
            $sBridallistLink .= '</div>';

        }

        return $sBridallistLink;

        }

    }

	/**
	 * bridallist_add
	 */
    function bridallist_add( $iProductId, $iVariationId = null ) {

        $iUserId        = get_current_user_id();
        $aNewItem       = array();
        $oProduct       = wc_get_product($iProductId);

        // registered and authorized user
        if ( $iUserId != 0 ) {
            $aUsersBridallist = ( get_user_meta($iUserId, '_uni_wc_bridallist_data', true) ) ? get_user_meta($iUserId, '_uni_wc_bridallist_data', true) : array();

            // not a variable product
            if ( !$oProduct->is_type('variable') ) {
                $aNewItem[$iProductId] = array(
                                    'type' => 'nonvariable',
                                    'is_bought' => false,
                                    'variations' => array()
                                );
                if ( empty($aUsersBridallist) ) {
                    update_user_meta( $iUserId, '_uni_wc_bridallist_data', $aNewItem );
                    return true;
                } else {
                    $aNewItem = $aUsersBridallist + $aNewItem;
                    update_user_meta( $iUserId, '_uni_wc_bridallist_data', $aNewItem );
                    return true;
                }

            // a variable product
            } else if ( $oProduct->is_type('variable') && $iVariationId != null ) {
                $aNewItem[$iProductId] = array(
                                    'type' => 'variable',
                                    'variations' => array(
                                                $iVariationId => false,
                                            )
                                );
                if ( empty($aUsersBridallist) ) {
                    update_user_meta( $iUserId, '_uni_wc_bridallist_data', $aNewItem );
                    return true;
                } else if ( !empty($aUsersBridallist) && empty($aUsersBridallist[$iProductId]) ) {
                    $aNewItem = $aUsersBridallist + $aNewItem;
                    update_user_meta( $iUserId, '_uni_wc_bridallist_data', $aNewItem );
                    return true;
                } else if ( !empty($aUsersBridallist) && !empty($aUsersBridallist[$iProductId]) ) {
                    $aArrayOfVariations = ( !empty($aUsersBridallist[$iProductId]['variations']) ) ? $aUsersBridallist[$iProductId]['variations'] : array();
                    if ( !isset($aUsersBridallist[$iProductId]['variations'][$iVariationId]) ) {
                        $aArrayOfVariations[$iVariationId] = false;
                        $aUsersBridallist[$iProductId]['variations'] = $aArrayOfVariations;
                        update_user_meta( $iUserId, '_uni_wc_bridallist_data', $aUsersBridallist );
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }

    }

	/**
	 * bridallist_delete
	 */
    function bridallist_delete( $iProductId, $iVariationId = null ) {

        $iUserId        = get_current_user_id();
        $oProduct       = wc_get_product($iProductId);

        // registered and authorized user
        if ( $iUserId != 0 ) {
            $aUsersBridallist = ( get_user_meta($iUserId, '_uni_wc_bridallist_data', true) ) ? get_user_meta($iUserId, '_uni_wc_bridallist_data', true) : array();

            if ( isset($aUsersBridallist) && !empty($aUsersBridallist) ) :
            // not a variable product
            if ( !$oProduct->is_type('variable') ) {
                unset($aUsersBridallist[$iProductId]);
                update_user_meta( $iUserId, '_uni_wc_bridallist_data', $aUsersBridallist );
                return true;

            // a variable product
            } else if ( $oProduct->is_type('variable') && $iVariationId != null ) {

                $aVariations = $aUsersBridallist[$iProductId]['variations'];
                if ( !empty($aVariations) ) {
                    if( isset($aVariations[$iVariationId]) ) {
                        unset($aUsersBridallist[$iProductId]['variations'][$iVariationId]);
                    }
                    if ( empty($aVariations) ) {
                        unset($aUsersBridallist[$iProductId]);
                    }
                    update_user_meta( $iUserId, '_uni_wc_bridallist_data', $aUsersBridallist );
                    return true;
                }

            }
            else
                return false;
            endif;
        }

    }

    /*
    *  bridallist get_items
    */
    function bridallist_get_items( $iUserId = null ) {

        $iCurrentUserId = get_current_user_id();
        $aUsersBridallist = array();

            if ( $iUserId != null ) {
                $aUsersBridallist = ( get_user_meta($iUserId, '_uni_wc_bridallist_data', true) ) ? get_user_meta($iUserId, '_uni_wc_bridallist_data', true) : array();
            } else if ( $iUserId == null && $iCurrentUserId != 0 ) {
                $aUsersBridallist = ( get_user_meta($iCurrentUserId, '_uni_wc_bridallist_data', true) ) ? get_user_meta($iCurrentUserId, '_uni_wc_bridallist_data', true) : array();
            }

        return $aUsersBridallist;
    }

    /*
    *  bridallist count_items
    TODO: add support of variable products
    */
    function bridallist_count_items( $iUserId = null ) {

        $iCurrentUserId = get_current_user_id();
        $aUsersBridallist = array();
        $iItemsCount = 0;

        if ( is_user_logged_in() ) {

            if ( $iUserId != null ) {
                $aUsersBridallist = get_user_meta($iUserId, '_uni_wc_bridallist_data', true);
                $iItemsCount = count($aUsersBridallist);
            } else if ( $iUserId == null && $iCurrentUserId != 0 ) {
                $aUsersBridallist = get_user_meta($iCurrentUserId, '_uni_wc_bridallist_data', true);
                $iItemsCount = count($aUsersBridallist);
            }

        }

        return $iItemsCount;
    }

    /*
    *  change_item_bought_status
    *  @param  bool $sStatus
    */
    function change_item_bought_status( $iUserId, $iProductId, $sStatus, $iVariationId = null ) {

        $aUserBridallist    = $this->bridallist_get_items( $iUserId );

        if ( !empty($aUserBridallist) ) {
        if ( $iVariationId == null || $iVariationId == '' ) {
            if ( $sStatus ) {
                $aUserBridallist[$iProductId]['is_bought'] = true;
                update_user_meta( $iUserId, '_uni_wc_bridallist_data', $aUserBridallist );
            } else {
                $aUserBridallist[$iProductId]['is_bought'] = false;
                update_user_meta( $iUserId, '_uni_wc_bridallist_data', $aUserBridallist );
            }
        // a variable product
        } else {
            if ( $sStatus ) {
                $aUserBridallist[$iProductId]['variations'][$iVariationId] = true;
                update_user_meta( $iUserId, '_uni_wc_bridallist_data', $aUserBridallist );
            } else {
                $aUserBridallist[$iProductId]['variations'][$iVariationId] = false;
                update_user_meta( $iUserId, '_uni_wc_bridallist_data', $aUserBridallist );
            }
        }
        }

    }


    /*
    *  uni_bridallist_user_fields
    */
    /*
    function uni_bridallist_user_fields( $user ) {

        $aProducts = array();
        $aUsersBridallist = uni_wc_bridallist_items( $user->ID );
        if ( !empty($aUsersBridallist) ) {
            foreach( $aUsersBridallist as $iProductId => $aProductData ) {
                $aProducts[] = $iProductId;
            }
        }
        $sUserBridallistTitle = get_user_meta( $user->ID, '_uni_wc_bridallist_title', true );
        ?>

	<h3><?php _e( 'Uni Wish and Bridal Lists Options', 'uni-wishlist' ); ?></h3>
	<table class="form-table">
		<tr>
            <th><label><?php _e( 'Bridal List Title', 'uni-wishlist' ); ?></label></th>
            <td>
                <input type="text" class="regular-text" name="uni_wc_bridallist_title" value="<?php echo $sUserBridallistTitle; ?>" />
            </td>
        </tr>
        <tr>
			<th><label><?php _e( 'Bridal List Content', 'uni-wishlist' ); ?> (<?php _e('check and save changes to mark as bought', 'uni-wishlist') ?>)</label></th>
			<td>
            <?php
            if ( !empty($aUserBridallist) ) {

                echo '<dl class="uni-bridallist-admin">';
                foreach ( $aUsersBridallist as $iProductId => $aBridallistItemData ) {
                    if ( $aBridallistItemData['type'] == 'variable' && !empty($aBridallistItemData['variations']) ) {
                        foreach ( $aBridallistItemData['variations'] as $iVariableProductId => $bIsVariableProductBought ) {
                            $oProduct = new WC_Product_Variation( $iVariableProductId, $iProductId );
                            if ( $oProduct->exists() ) {
                                $iProductVariationId = $oProduct->variation_id;
                            ?>
                <dt><a href="<?php echo get_permalink( $oProduct->post->ID ) ?>"><?php echo get_the_title( $oProduct->post->ID ); ?></a> <?php if ( !empty($aProductData['is_bought']) ) echo sprintf(__('<small>%s</small>'), __( '(bought)', 'uni-wishlist' ) ); ?></dt>
                <dd><input type="checkbox" name="uni_wc_bridallist_item[<?php echo $oProduct->post->ID; ?>]" value="1" <?php checked(true, $aProductData['is_bought']) ?> /></dd>
                            <?php
                            }
                        }
                    } else {
                        $oProduct = new WC_Product( $iProductId );
                            if ( $oProduct->exists() ) {
                            ?>
                <dt><a href="<?php echo get_permalink( $oProduct->post->ID ) ?>"><?php echo get_the_title( $oProduct->post->ID ); ?></a> <?php if ( !empty($aProductData['is_bought']) ) echo sprintf(__('<small>%s</small>'), __( '(bought)', 'uni-wishlist' ) ); ?></dt>
                <dd><input type="checkbox" name="uni_wc_bridallist_item[<?php echo $oProduct->post->ID; ?>]" value="1" <?php checked(true, $aProductData['is_bought']) ?> /></dd>
                            <?php
                            }
                    }
                }
                echo '</dl>';

            } else {
                _e('No items in the Bridal List', 'uni-wishlist');
            }
            ?>
            </td>
		</tr>
    </table>

        <?php
    }*/

    /*
    *  uni_bridallist_user_fields_save
    */
    /*
    function uni_bridallist_user_fields_save( $user_id ) {

	    if ( !current_user_can( 'edit_user', $user_id ) )
		    return false;

        if ( isset($_POST['uni_wc_bridallist_title']) && !empty($_POST['uni_wc_bridallist_title']) ) {
            update_user_meta($user_id, '_uni_wc_bridallist_title', $_POST['uni_wc_bridallist_title']);
        }

        $sPostedData = ( !empty($_POST['uni_wc_bridallist_item']) ) ? $_POST['uni_wc_bridallist_item'] : array();
        $aUserBridallist = ( $this->bridallist_get_items( $user_id ) ) ? $this->bridallist_get_items( $user_id ) : array();
        if ( !empty($aUserBridallist) ) {
            foreach ( $aUserBridallist as $iProductId => $aProductData ) {
                if ( isset($sPostedData[$iProductId]) && !empty($sPostedData[$iProductId]) ) {
                    $this->change_item_bought_status( $user_id, $iProductId, true );
                } else if ( !isset($sPostedData[$iProductId]) || empty($sPostedData[$iProductId]) ) {
                    $this->change_item_bought_status( $user_id, $iProductId, false );
                }
            }
        }

    } */

    function uni_plugin_activate(){
        update_option('uni_wishlist_link_position', 'after_add_to_cart');
        update_option('uni_wishlist_style', 'default');
    }

    function uni_plugin_deactivate(){
        update_option('uni_bridallist_enable', false);
        flush_rewrite_rules();
    }

}

endif;

/**
 *  The main object
 */
function UniWishlist() {
	return Uni_WC_Wishlist::instance();
}

// Global for backwards compatibility.
$GLOBALS['uniwcwishlist'] = UniWishlist();
?>