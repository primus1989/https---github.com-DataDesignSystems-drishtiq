<?php
/*
Plugin Name: Uni Events Calendars Manager
Plugin URI: http://moomoo.agency
Description: A comprehesive, modern and flexible Events Calendars Manager for WordPress.
Version: 1.0.7
Author: MooMoo Web Studio
Author URI: http://moomoo.agency
License: GPL2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uni_Calendar' ) ) :

/**
 * Uni_Calendar Class
 */
final class Uni_Calendar {

	public $version = '1.0.7';

    public $calendars_list = null;
    public $calendars_ajax = null;

	protected static $_instance = null;

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
        add_shortcode( 'uni-calendar', array( $this, 'calendar_shortcode' ) );
	}

	/**
	 *  Includes
	 */
    private function includes() {
        include_once( 'includes/uni-events-calendar-functions.php' );
        include_once( 'includes/unibill-class-wp-list-table.php' );
        include_once( 'includes/uni-calendars-list.php' );
        include_once( 'includes/uni-events-calendar-ajax.php' );
    }

	/**
	 *  Init hooks
	 */
    private function init_hooks() {
        add_action( 'init', array( $this, 'init' ), 0 );
    }

	/**
	 * Init
	 */
	public function init() {

        $this->post_type();
        add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ), 10 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 10 );

        $this->calendars_list = new UniCalendarsList();
        $this->calendars_ajax = new UniCalendarAjax();

        $sAllCalendarsUrl   = esc_url( add_query_arg( array( 'page' => 'uni-events-calendars' ), admin_url('admin.php') ) );
        if ( !empty($_GET['page']) && $_GET['page'] == 'uni-events-calendars' && !empty($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['cal_id']) ) {
            wp_delete_post( $_GET['cal_id'], false );
            wp_safe_redirect( $sAllCalendarsUrl );
            die();
        }

        add_action( 'uni_calendar_event_cat_edit_form_fields', array( $this, 'uni_tax_custom_data' ) );
        add_action( 'uni_calendar_event_cat_add_form_fields', array( $this, 'uni_add_new_meta_fields' ), 10, 2 );
        add_action( 'edited_uni_calendar_event_cat', array( $this, 'uni_save_custom_meta' ), 10, 2 );
        add_action( 'create_uni_calendar_event_cat', array( $this, 'uni_save_custom_meta' ), 10, 2 );
        add_action( 'delete_uni_calendar_event_cat', array( $this, 'uni_delete_custom_meta' ), 10, 2 );

        add_filter( 'manage_edit-uni_calendar_event_cat_columns', array( $this, 'uni_add_custom_attr_column' ));
        add_filter( 'manage_uni_calendar_event_cat_custom_column', array( $this, 'uni_add_custom_attr_column_content' ), 10, 3);

		// Multilanguage support
		$this->load_plugin_textdomain();

        $this->cron_jobs();
	}

	/**
	 * post_type
	 */
    function post_type() {
        // register new post types
	    $labels = array(
		    'name' => __('Calendar', 'uni-calendar'),
		    'singular_name' => __('Calendars', 'uni-calendar'),
		    'add_new' => __('New Calendar', 'uni-calendar'),
		    'add_new_item' => __('Add New Calendar', 'uni-calendar'),
		    'edit_item' => __('Edit Calendar', 'uni-calendar'),
		    'new_item' => __('All calendars', 'uni-calendar'),
		    'view_item' => __('View Calendar', 'uni-calendar'),
		    'search_items' => __('Search calendars', 'uni-calendar'),
		    'not_found' =>  __('Calendars not found', 'uni-calendar'),
		    'not_found_in_trash' => __('Calendars not found in cart', 'uni-calendar'),
		    'parent_item_colon' => '',
		    'menu_name' => __('Uni Calendars', 'uni-calendar')
	    );

	    $args = array(
		    'labels' => $labels,
		    'public' => false,
		    'publicly_queryable' => false,
		    'show_ui' => false,
		    'show_in_menu' => false,
		    'query_var' => true,
		    'menu_position' => 4.5,
            'menu_icon' => '',
		    'capability_type' => 'post',
		    'hierarchical' => false,
		    'has_archive' => false,
		    'rewrite' => array( 'slug' => 'calendar', 'with_front' => false ),
		    'supports' => array('title'),
		    'taxonomies' => array(),
	    );
	    register_post_type( 'uni_calendar' , $args );

	    $labels = array(
		    'name' => __('Event', 'uni-calendar'),
		    'singular_name' => __('Events', 'uni-calendar'),
		    'add_new' => __('New Event', 'uni-calendar'),
		    'add_new_item' => __('Add New Event', 'uni-calendar'),
		    'edit_item' => __('Edit Event', 'uni-calendar'),
		    'new_item' => __('All events', 'uni-calendar'),
		    'view_item' => __('View Event', 'uni-calendar'),
		    'search_items' => __('Search events', 'uni-calendar'),
		    'not_found' =>  __('Events not found', 'uni-calendar'),
		    'not_found_in_trash' => __('Events not found in cart', 'uni-calendar'),
		    'parent_item_colon' => '',
		    'menu_name' => __('Uni Calendar Events', 'uni-calendar')
	    );

	    $args = array(
		    'labels' => $labels,
		    'public' => false,
		    'publicly_queryable' => false,
		    'show_ui' => false,
		    'show_in_menu' => false,
		    'query_var' => true,
		    'menu_position' => 4.5,
            'menu_icon' => '',
		    'capability_type' => 'post',
		    'hierarchical' => false,
		    'has_archive' => false,
		    'rewrite' => array( 'slug' => 'calendar-event', 'with_front' => false ),
		    'supports' => array('title'),
		    'taxonomies' => array('uni_calendar_event_cat'),
	    );
	    register_post_type( 'uni_calendar_event' , $args );

        // register new taxonomy
        register_taxonomy('uni_calendar_event_cat', 'uni_calendar_event', array(
            'labels' => array('menu_name' => __('Event categories', 'uni-calendar')),
            'rewrite' => array('slug' => 'uni-calendar-cat'),
            'hierarchical'  => false
        ));
    }

	/**
	 * cron_jobs
	 */
    function cron_jobs() {

        if ( get_option('uni_calendar_enable_auto_transfer') ) {
            if ( ! wp_next_scheduled( 'uni_calendar_transfer_events_hook' ) ) {
                wp_schedule_event( time(), 'daily', 'uni_calendar_transfer_events_hook' );
            }
        } else {
            wp_clear_scheduled_hook( 'uni_calendar_transfer_events_hook' );
            $iCurrentWeekNumber = absint( date('W') );
            delete_transient('_uni_calendars_auto_transfered_w_'.$iCurrentWeekNumber);
        }

        add_action( 'uni_calendar_transfer_events_hook', array( $this, 'transfer_events_func' ) );
        // test only
        //add_action( 'init', array( $this, 'transfer_events_func' ) );
    }

	/**
	 * transfer_events_func
	 */
    function transfer_events_func() {

        $iCurrentWeekNumber = absint( date('W') );
        $iCurrentDayNumber = absint( date('N') );

        //delete_transient('uni_calendars_auto_transfered_w_'.$iCurrentWeekNumber);
        if ( !get_transient('uni_calendars_auto_transfered_w_'.$iCurrentWeekNumber) && $iCurrentDayNumber >= get_option('uni_calendar_day_of_auto_transfer') ) {

        $aDates = uni_calendar_current_week_date_range();
        $aCals = get_posts(
            array(
                'post_type'         => 'uni_calendar',
                'post_status'       => 'publish',
                'posts_per_page'    => -1,
                'meta_key'          => '_auto_transfered_w_'.$iCurrentWeekNumber,
                'meta_compare'      => 'NOT EXISTS'
                )
        );

        // at least one calendar exists
        if ( !empty($aCals) && !is_wp_error($aCals) ) {
            foreach ( $aCals as $oCal ) {

            $aEventArgs = array(
                'post_type'	=> 'uni_calendar_event',
                'post_status' => 'publish',
                'ignore_sticky_posts'	=> 1,
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => '_uni_event_parent_calendar',
                        'value' => $oCal->ID,
                        'compare' => '=',
                        'type' => 'NUMERIC'
                    ),
                    array(
                        'key' => '_uni_event_timestamp_start',
                        'value' => $aDates['start'],
                        'compare' => '>=',
                        'type' => 'NUMERIC'
                    ),
                    array(
                        'key' => '_uni_event_timestamp_end',
                        'value' => $aDates['end'],
                        'compare' => '<=',
                        'type' => 'NUMERIC'
                    )
                )
            );

            $oEventQuery = new WP_Query( $aEventArgs );

            // at least one event exists on this week
            if ( !empty($oEventQuery->found_posts) ) {
                foreach ( $oEventQuery->posts as $oEvent ) {
                    //print_r($oEvent->ID);
                    // data of current event
                    $aArgs                  = array();
                    $oEvent                 = get_post( $oEvent->ID );
                    $aPostCustom            = get_post_custom( $oEvent->ID );
                    $aArgs['post_type']     = 'uni_calendar_event';
                    $aArgs['post_title']    = $oEvent->post_title;
                    $aArgs['post_content']  = $oEvent->post_content;
                    $aArgs['post_status']   = 'publish';
                    $aEventCats             = wp_get_post_terms( $oEvent->ID, 'uni_calendar_event_cat' );
                    if ( !empty($aEventCats) && !is_wp_error($aEventCats) ) {
                        $iEventCatId        = $aEventCats[0]->term_id;
                    }
                    $iDelta = ( $aPostCustom['_uni_event_timestamp_end'][0] - $aPostCustom['_uni_event_timestamp_start'][0] );

                    // creating of the new event
                    $iNewPostId = wp_insert_post( $aArgs );

                    if ( $iNewPostId != 0 ) {

                        update_post_meta( $iNewPostId, '_uni_event_parent_calendar', $oCal->ID );
                        $iCopyDateTimestamp = $aPostCustom['_uni_event_timestamp_start'][0] + 604800; // + 7 days
                        update_post_meta( $iNewPostId, '_uni_event_timestamp_start', $iCopyDateTimestamp );
                        update_post_meta( $iNewPostId, '_uni_event_timestamp_end', $iCopyDateTimestamp + $iDelta );
                        update_post_meta( $iNewPostId, '_uni_event_user_id', $aPostCustom['_uni_event_user_id'][0] );

                        if ( !empty($iEventCatId) ) {
                            $iEventCatId = intval($iEventCatId);
                            wp_set_object_terms( $iNewPostId, $iEventCatId, 'uni_calendar_event_cat', false);
	                        clean_object_term_cache( $iNewPostId, 'uni_calendar_event_cat' );
                        }

                    }

                }
            }

            // update metas of calendars
            update_post_meta($oCal->ID, '_auto_transfered_w_'.$iCurrentWeekNumber, 'yes');

            } // end of foreach calendars
            set_transient('uni_calendars_auto_transfered_w_'.$iCurrentWeekNumber, 'yes', 604800);
        }

        } // end of get_transient

    }

	/**
	 * load_plugin_textdomain()
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'uni-calendar' );

		load_textdomain( 'uni-calendar', WP_LANG_DIR . '/uni-events-calendar/uni-calendar-' . $locale . '.mo' );
		load_plugin_textdomain( 'uni-calendar', false, plugin_basename( dirname( __FILE__ ) ) . "/languages" );
	}

	/**
	*  front_scripts()
    */
    function front_scripts() {

        $sLocale = get_locale();
        $aLocale = explode('_',$sLocale);
        $sLangCode = $aLocale[0];

        wp_enqueue_script( 'jquery' );
        // jquery.fancybox.pack
        wp_register_script('jquery-fancybox', $this->plugin_url().'/assets/js/jquery.fancybox.pack.js', array('jquery'), '2.1.5' );
        wp_enqueue_script('jquery-fancybox');
        // moment.min
        wp_register_script( 'moment', $this->plugin_url().'/assets/js/moment.min.js', array('jquery'), '2.9.0' );
        wp_enqueue_script( 'moment' );
        // FullCalendar
        wp_register_script( 'jquery-fullcalendar', $this->plugin_url().'/assets/js/fullcalendar.min.js', array('jquery'), '2.4.0' );
        wp_enqueue_script( 'jquery-fullcalendar' );
        // FullCalendar localization
        wp_register_script( 'fullcalendar-localization', $this->plugin_url().'/assets/js/lang-all.js', array('jquery'), '2.4.0' );
        wp_enqueue_script( 'fullcalendar-localization' );
        // plugin's scripts
        wp_register_script( 'uni-events-calendar', $this->plugin_url().'/assets/js/uni-events-calendar.js', array('jquery'), $this->version);
        wp_enqueue_script( 'uni-events-calendar' );


        $params = array(
            'site_url'          => get_bloginfo('url'),
		    'ajax_url' 		    => $this->ajax_url(),
            'loader'            => $this->plugin_url().'/assets/images/preloader.gif',
            'locale'            => $sLangCode
	    );
	    wp_localize_script( 'uni-events-calendar', 'unicalendar', $params );

        wp_enqueue_style( 'fancybox', $this->plugin_url().'/assets/css/fancybox.css', false, '2.1.5', 'all' );
        wp_enqueue_style( 'fullcalendar', $this->plugin_url().'/assets/css/fullcalendar.min.css', false, '2.4.0', 'all');
        wp_enqueue_style( 'fullcalendar-print', $this->plugin_url().'/assets/css/fullcalendar.print.css', false, '2.4.0', 'print');
        wp_enqueue_style( 'uni-events-calendar-styles', $this->plugin_url().'/assets/css/uni-events-calendar-styles.css', false, $this->version, 'all');

    }

	/**
	*  admin_scripts()
    */
    function admin_scripts( $hook ) {
        if ( ( $hook == 'edit-tags.php' && isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'uni_calendar_event' ) ||
                ( $hook == 'toplevel_page_uni-events-calendars' ) ) {

        $sLocale = get_locale();
        $aLocale = explode('_',$sLocale);
        $sLangCode = $aLocale[0];
        $bTranslateForParsley = true;

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_enqueue_style( 'wp-color-picker' );
        // moment.min
        wp_register_script( 'moment', $this->plugin_url().'/assets/js/moment.min.js', array('jquery'), '2.9.0' );
        wp_enqueue_script( 'moment' );
        // FullCalendar
        wp_register_script( 'jquery-fullcalendar', $this->plugin_url().'/assets/js/fullcalendar.min.js', array('jquery'), '2.3.1' );
        wp_enqueue_script( 'jquery-fullcalendar' );
        // FullCalendar localization
        wp_register_script( 'fullcalendar-localization', $this->plugin_url().'/assets/js/lang-all.js', array('jquery'), '2.3.1' );
        wp_enqueue_script( 'fullcalendar-localization' );
        // jquery.blockUI
        wp_register_script( 'jquery-blockui', $this->plugin_url().'/assets/js/jquery.blockUI.js', array('jquery'), '2.70.0' );
        wp_enqueue_script( 'jquery-blockui' );
        // parsley localization
        if ( !file_exists( $this->plugin_path() . '/js/parsley/i18n/'.$sLangCode.'.js' ) ) {
            wp_register_script('parsley-localization', $this->plugin_url().'/assets/js/parsley/i18n/en.js', array('jquery'), '2.0.7' );
            $bTranslateForParsley = false;
        } else {
            wp_register_script('parsley-localization', $this->plugin_url().'/assets/js/parsley/i18n/'.$sLangCode.'.js', array('jquery'), '2.0.7' );
        }
        wp_enqueue_script('parsley-localization');
        // parsley
        wp_register_script('jquery-parsley', $this->plugin_url().'/assets/js/parsley.min.js', array('jquery'), '2.0.7' );
        wp_enqueue_script('jquery-parsley');
        // jquery.datetimepicker
        wp_register_script('jquery-datetimepicker', $this->plugin_url().'/assets/js/jquery.datetimepicker.js', array('jquery'), '2.4.3' );
        wp_enqueue_script('jquery-datetimepicker');
        // plugin's scripts
        wp_register_script( 'uni-events-calendar-admin', $this->plugin_url().'/assets/js/uni-events-calendar-admin.js', array('jquery', 'jquery-ui-dialog', 'wp-color-picker'), $this->version);
        wp_enqueue_script( 'uni-events-calendar-admin' );

        wp_enqueue_style( 'fullcalendar', $this->plugin_url().'/assets/css/fullcalendar.min.css', false, '2.3.1', 'all');
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        wp_enqueue_style( 'uni-events-calendar-styles-admin', $this->plugin_url().'/assets/css/uni-events-calendar-styles-admin.css', false, $this->version, 'all');

        $params = array(
            'locale'            => $sLangCode,
            'parsley_translation' => $bTranslateForParsley
	    );
	    wp_localize_script( 'uni-events-calendar-admin', 'unicalendar', $params );
        }

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
	 * uni_add_new_meta_fields
	 */
    function uni_add_new_meta_fields( $taxonomy ) {
	?>
        <legend><?php _e('Uni Events Calendars additional fields', 'uni-calendar') ?></legend>
	    <div class="form-field">
		    <label for="uni-calendar-bg-colour"><?php _e('Background colour', 'uni-calendar') ?></label>
            <input type="text" name="uni_calendar_tax_custom[background]" id="uni-calendar-bg-colour" value="" class="uni-calendar-colour-field">
		    <p class="description"><?php _e('Background colour for events in this category (optional)', 'uni-calendar') ?></p>
	    </div>
	    <div class="form-field">
		    <label for="uni-calendar-border-colour"><?php _e('Border colour', 'uni-calendar') ?></label>
		    <input type="text" name="uni_calendar_tax_custom[border]" id="uni-calendar-border-colour" value="" class="uni-calendar-colour-field">
		    <p class="description"><?php _e('Border colour for events in this category (optional).', 'uni-calendar') ?></p>
	    </div>
    <?php
    }

	/**
	 * uni_save_custom_meta
	 */
    function uni_save_custom_meta( $sTermId ) {
	    if ( isset( $_POST['uni_calendar_tax_custom'] ) ) {

		    $aTermData = get_option('uni_calendar_tax_'.$sTermId.'_data');
		    $aKeys = array_keys( $_POST['uni_calendar_tax_custom'] );
		    foreach ( $aKeys as $sKey ) {
			    if ( isset ( $_POST['uni_calendar_tax_custom'][$sKey] ) ) {
				    $aTermData[$sKey] = $_POST['uni_calendar_tax_custom'][$sKey];
			    }
		    }
		    // Save the option array.
            update_option( 'uni_calendar_tax_'.$sTermId.'_data' , $aTermData );
	    }
    }

	/**
	 * uni_delete_custom_meta
	 */
    function uni_delete_custom_meta( $oTerm, $sTermId) {
        delete_option( 'uni_calendar_tax_'.$sTermId.'_data' );
    }

	/**
	 * uni_tax_custom_data
	 */
    function uni_tax_custom_data( $oTerm ) {
        $aTermData = get_option('uni_calendar_tax_'.$oTerm->term_id.'_data');
        ?>
        <tr valign="top" class="form-field">
            <th scope="row"><legend><?php _e('Uni Events Calendars additional fields', 'uni-calendar') ?></legend></th>
            <td></td>
        </tr>
        <tr valign="top" class="form-field">
        <th scope="row"><?php _e('Background colour', 'uni-calendar') ?></th>
            <td>
                <input type="text" name="uni_calendar_tax_custom[background]" id="uni-calendar-bg-colour" value="<?php echo $aTermData['background']; ?>" class="uni-calendar-colour-field">
		        <p class="description"><?php _e('Background colour for events in this category (optional)', 'uni-calendar') ?></p>
            </td>
        </tr>
        <tr valign="top" class="form-field">
        <th scope="row"><?php _e('Border colour', 'uni-calendar') ?></th>
            <td>
		        <input type="text" name="uni_calendar_tax_custom[border]" id="uni-calendar-border-colour" value="<?php echo $aTermData['border']; ?>" class="uni-calendar-colour-field">
		        <p class="description"><?php _e('Border colour for events in this category (optional).', 'uni-calendar') ?></p>
            </td>
        </tr>
    <?php
    }

	/**
	 * uni_add_custom_attr_column
	 */
    function uni_add_custom_attr_column( $columns ){
        $columns['uni_calendar_bg'] = 'Background colour';
        $columns['uni_calendar_border'] = 'Border colour';
        return $columns;
    }

	/**
	 * uni_add_custom_attr_column_content
	 */
    function uni_add_custom_attr_column_content( $content, $column_name, $term_id ){
        $aTermData = get_option('uni_calendar_tax_'.$term_id.'_data');
        switch ($column_name) {
            case 'uni_calendar_bg':
                if ( $aTermData['background'] ) {
                    $content = '<span style="display:block;width:20px;height:20px;background-color:'.$aTermData['background'].';"></span><br>'.$aTermData['background'];
                }
                break;
            case 'uni_calendar_border':
                if ( $aTermData['border'] ) {
                    $content = '<span style="display:block;width:20px;height:20px;background-color:'.$aTermData['border'].';"></span><br>'.$aTermData['border'];
                }
                break;
            default:
                break;
        }
        return $content;
    }

	/**
	 * calendar_shortcode()
	 */
    public function calendar_shortcode( $atts, $content = null ) {
        $aAttr = shortcode_atts( array(
            'id' => null
        ), $atts );

        if ( $aAttr['id'] != null ) {
	        ob_start();
	        include( UniCalendar()->plugin_path().'/includes/views/single-calendar.php' );
	        return ob_get_clean();
        } else {
            return;
        }
    }

	/**
	 * plugin_deactivate()
	 */
    public function plugin_deactivate(){
        wp_clear_scheduled_hook( 'uni_calendar_transfer_events_hook' );
    }

}

endif;

/**
 *  The main object
 */
function UniCalendar() {
	return Uni_Calendar::instance();
}

// Global for backwards compatibility.
$GLOBALS['unieventscalendar'] = UniCalendar();
?>