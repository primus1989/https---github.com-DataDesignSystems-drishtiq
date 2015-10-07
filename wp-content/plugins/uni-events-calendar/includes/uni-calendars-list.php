<?php
/*
* Class UniCalendarsList
*
*/

class UniCalendarsList extends UniWpListTable {

    private $order;
    private $orderby;
    private $posts_per_page = 25;

    function __construct(){
        global $status, $page;

        parent::__construct( array(
                'singular'  => __( 'Calendar', 'uni-calendar' ),
                'plural'    => __( 'Calendars', 'uni-calendar' ),
                'ajax'      => false,
                'screen'    => 'calendars-list'
                )
        );

        $this->set_order();
        $this->set_orderby();

        add_action( 'admin_menu', array( &$this, 'calendars_admin_menu' ) );
        add_filter( 'parent_file', array( &$this, 'set_current_menu' ) );

    }

	/**
	*  calendars_admin_menu
    */
    public function calendars_admin_menu() {
        add_menu_page( __('Uni Events Calendars Manager', 'uni-calendar'), __('Uni Calendars', 'uni-calendar'), 'manage_options', 'uni-events-calendars', array( $this, 'calendars_option_page' ), 'dashicons-calendar-alt', 81.2 );

       $aSubmenuPages = array(

            // Parent page
            array(
                'parent_slug'   => 'uni-events-calendars',
                'page_title'    => __('Manage calendars', 'uni-calendar'),
                'menu_title'    => __('Manage calendars', 'uni-calendar'),
                'capability'    => 'manage_options',
                'menu_slug'     => 'uni-events-calendars',
                'function'      => array( $this, 'calendars_option_page' )
            ),

            // Calendar events categories
            array(
                'parent_slug'   => 'uni-events-calendars',
                'page_title'    => '',
                'menu_title'    => __('Event categories', 'uni-calendar'),
                'capability'    => 'manage_options',
                'menu_slug'     => 'edit-tags.php?taxonomy=uni_calendar_event_cat&post_type=uni_calendar_event',
                'function'      => null
            ),

            // Calendar general settings
            array(
                'parent_slug'   => 'uni-events-calendars',
                'page_title'    => '',
                'menu_title'    => __('Plugin settings', 'uni-calendar'),
                'capability'    => 'manage_options',
                'menu_slug'     => 'uni-events-calendars-settings',
                'function'      => array( $this, 'calendars_settings_page' )
            )

        );

        // Add each submenu item to custom admin menu.
        foreach( $aSubmenuPages as $aSubmenu ){

            add_submenu_page(
                $aSubmenu['parent_slug'],
                $aSubmenu['page_title'],
                $aSubmenu['menu_title'],
                $aSubmenu['capability'],
                $aSubmenu['menu_slug'],
                $aSubmenu['function']
            );

        }

        add_action( 'admin_init', array( &$this, 'register_settings' ) );
    }

    //
    public function register_settings() {
        register_setting( 'uni-calendar-settings-group', 'uni_calendar_enable_auto_transfer' );
        register_setting( 'uni-calendar-settings-group', 'uni_calendar_day_of_auto_transfer' );
    }

    // highlight our menu
    public function set_current_menu($parent_file){

        global $submenu_file, $current_screen, $pagenow;

        if($current_screen->post_type == 'uni_calendar_event') {

            if($pagenow == 'post.php'){
                $submenu_file = 'edit.php?post_type='.$current_screen->post_type;
            }

            if($pagenow == 'edit-tags.php'){
                $submenu_file = 'edit-tags.php?taxonomy=uni_calendar_event_cat&post_type='.$current_screen->post_type;
            }

            $parent_file = 'uni-events-calendars';

        }

        return $parent_file;

    }

    //
    public function calendars_option_page() {
        $sAllCalendarsUrl   = esc_url( add_query_arg( array( 'page' => 'uni-events-calendars' ), admin_url('admin.php') ) );
        $sAddNewCalendarUrl = esc_url( add_query_arg( array( 'page' => 'uni-events-calendars', 'action' => 'add-new' ), admin_url('admin.php') ) );
        ?>

        <div id="uni-calendar-wrapper" class="wrap">
            <div id="icon-tools" class="icon32"></div>
            <?php if ( !isset($_GET['action']) || ( $_GET['action'] != 'add-new' && $_GET['action'] != 'edit' && $_GET['action'] != 'edit-events' ) ) { ?>
                <h2><?php _e('Uni Events Calendars Manager', 'uni-calendar') ?> <a href="<?php echo $sAddNewCalendarUrl; ?>" class="add-new-h2"><?php _e( 'Add New', 'uni-calendar' ) ?></a></h2>
            <?php } else if ( !empty($_GET['action']) && $_GET['action'] == 'edit-events' ) {
                if ( !empty($_GET['cal_id']) ) {
                    $oPost = get_post( $_GET['cal_id'] );
                }
            ?>
                <h2><?php echo $oPost->post_title; ?> <a href="<?php echo $sAllCalendarsUrl; ?>" class="add-new-h2"><?php _e( 'Back to all calendars list', 'uni-calendar' ) ?></a></h2>
            <?php } else if ( !empty($_GET['action']) && $_GET['action'] == 'add-new' ) { ?>
                <h2><?php _e( 'Add a new calendar', 'uni-calendar' ) ?> <a href="<?php echo $sAllCalendarsUrl; ?>" class="add-new-h2"><?php _e( 'Back to all calendars list', 'uni-calendar' ) ?></a></h2>
            <?php } else if ( !empty($_GET['action']) && $_GET['action'] == 'edit' ) {
                if ( !empty($_GET['cal_id']) ) {
                    $oPost = get_post( $_GET['cal_id'] );
                }
            ?>
                <h2><?php _e( 'Edit calendar', 'uni-calendar' ) ?> <a href="<?php echo $sAllCalendarsUrl; ?>" class="add-new-h2"><?php _e( 'Back to all calendars list', 'uni-calendar' ) ?></a></h2>
            <?php } ?>

            <div id="ajax-messages">
            </div>

            <?php
            if ( !isset($_GET['action']) || ( $_GET['action'] != 'add-new' && $_GET['action'] != 'edit' && $_GET['action'] != 'edit-events' ) ) {

                $this->prepare_items();
                $this->display();

            } else if ( !empty($_GET['action']) && $_GET['action'] == 'edit-events' ) {

                include( UniCalendar()->plugin_path().'/includes/views/single-calendar-admin.php' );

            } else if ( !empty($_GET['action']) && $_GET['action'] == 'add-new' ) {

                include( UniCalendar()->plugin_path().'/includes/views/single-calendar-admin-add.php' );

            } else if ( !empty($_GET['action']) && $_GET['action'] == 'edit' ) {

                include( UniCalendar()->plugin_path().'/includes/views/single-calendar-admin-edit.php' );

            }
            ?>

        </div>
        <?php
    }

    //
    public function calendars_settings_page() {
        ?>

        <div id="uni-calendar-wrapper" class="wrap">
            <div id="icon-tools" class="icon32"></div>

            <h2><?php _e( 'Uni Events Calendar Settings', 'uni-calendar' ) ?></h2>

            <form method="post" action="options.php">
                <?php settings_fields( 'uni-calendar-settings-group' ); ?>
                <?php do_settings_sections( 'uni-calendar-settings-group' ); ?>

                <h3><?php _e('General settings', 'uni-calendar') ?></h3>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('Enable auto transfer of events?', 'uni-avatar') ?>
                        </th>
                        <td>
                            <input type="checkbox" name="uni_calendar_enable_auto_transfer" value="1"<?php echo checked( get_option('uni_calendar_enable_auto_transfer'), 1 ); ?> />
                            <p class="description"><?php _e('Check this option to enable auto transfer of events from current period to the next period. Auto transfering happens on per week basis.', 'uni-calendar') ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e('Choose day of auto transfer', 'uni-avatar') ?>
                        </th>
                        <td>
                            <select name="uni_calendar_day_of_auto_transfer">
                                <option value="1"<?php selected('1', get_option('uni_calendar_day_of_auto_transfer')) ?>><?php _e('Monday', 'uni-calendar') ?></option>
                                <option value="2"<?php selected('2', get_option('uni_calendar_day_of_auto_transfer')) ?>><?php _e('Tuesday', 'uni-calendar') ?></option>
                                <option value="3"<?php selected('3', get_option('uni_calendar_day_of_auto_transfer')) ?>><?php _e('Wednesday', 'uni-calendar') ?></option>
                                <option value="4"<?php selected('4', get_option('uni_calendar_day_of_auto_transfer')) ?>><?php _e('Thursday', 'uni-calendar') ?></option>
                                <option value="5"<?php selected('5', get_option('uni_calendar_day_of_auto_transfer')) ?>><?php _e('Friday', 'uni-calendar') ?></option>
                                <option value="6"<?php selected('6', get_option('uni_calendar_day_of_auto_transfer')) ?>><?php _e('Saturday', 'uni-calendar') ?></option>
                                <option value="7"<?php selected('7', get_option('uni_calendar_day_of_auto_transfer')) ?>><?php _e('Sunday', 'uni-calendar') ?></option>
                            </select>
                            <p class="description"><?php _e('All events of current week wil be transfered to the next week on this day. <strong>Important: disable auto transfer option first if you change the day and auto transfer was enabled. It ensures that the related wp-cron job will be executed as it should be.</strong>', 'uni-calendar') ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>

            </form>

        </div>
        <?php
    }

    //
    function get_columns(){
        $columns = array(
            'ID'                        => __( 'ID', 'uni-calendar' ),
            'post_title'                => __( 'Calendar title', 'uni-calendar' ),
            'calendar_shortcode'        => __( 'Shortcode', 'uni-calendar' )
        );
         return $columns;
    }

    //
    public function get_sortable_columns(){
        $sortable = array(
            'ID'                => array( 'ID', true ),
            'post_title'        => array( 'post_title', true )
        );
        return $sortable;
    }

    //
    public function set_order(){
        $order = 'DESC';
        if ( isset( $_GET['order'] ) AND $_GET['order'] )
            $order = $_GET['order'];
        $this->order = esc_sql( $order );
    }

    //
    public function set_orderby(){
        $orderby = 'post_date';
        if ( isset( $_GET['orderby'] ) AND $_GET['orderby'] )
            $orderby = $_GET['orderby'];
        $this->orderby = esc_sql( $orderby );
    }

    //
    public function ajax_user_can(){
        return current_user_can( 'manage_options' );
    }

    //
    private function get_sql_results() {
        global $wpdb;

        $sOrderClause = "ORDER BY $this->orderby $this->order";

        $aResult = $wpdb->get_results("
                SELECT *
                FROM $wpdb->posts
                WHERE post_type = 'uni_calendar' AND post_status = 'publish'
                $sOrderClause",
                ARRAY_A
        );

        return $aResult;

    }

    //
    public function no_items() {
        _e( 'No calendars.', 'uni-calendar' );
    }

    //
    public function get_views() {
        return array();
    }

    //
    public function prepare_items() {
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        // SQL results
        $aCalendars = $this->get_sql_results();
        empty( $aCalendars ) AND $aCalendars = array();

        # >>>> Pagination
        $per_page     = $this->posts_per_page;
        $current_page = $this->get_pagenum();
        $total_items  = count( $aCalendars );
        $this->set_pagination_args( array (
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page )
        ) );
        $last_post = $current_page * $per_page;
        $first_post = $last_post - $per_page + 1;
        $last_post > $total_items AND $last_post = $total_items;

        // Setup the range of keys/indizes that contain
        // the posts on the currently displayed page(d).
        // Flip keys with values as the range outputs the range in the values.
        $range = array_flip( range( $first_post - 1, $last_post - 1, 1 ) );

        // Filter out the posts we're not displaying on the current page.
        $posts_array = array_intersect_key( $aCalendars, $range );
        # <<<< Pagination

        $this->items = $posts_array;
    }

    //
    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'ID':
                return $item[ $column_name ];
            case 'post_title':
                return $item[ $column_name ];
            case 'calendar_shortcode':
                return '[uni-calendar id="'.$item['ID'].'"]';
            default:
                return print_r( $item, true );
        }
    }

    //
    function column_post_title($item) {
        $actions = array(
                'edit'          => sprintf('<a href="?page=%s&action=%s&cal_id=%s&uni_auth_nonce=%s">%s</a>', $_REQUEST['page'], 'edit', $item['ID'], wp_create_nonce('uni_authenticate_nonce'), __( 'Edit', 'uni-calendar' )),
                'edit-events'   => sprintf('<a href="?page=%s&action=%s&cal_id=%s&uni_auth_nonce=%s">%s</a>', $_REQUEST['page'], 'edit-events', $item['ID'], wp_create_nonce('uni_authenticate_nonce'), __( 'Edit calendar\'s events', 'uni-calendar' )),
                'delete'        => sprintf('<a href="?page=%s&action=%s&cal_id=%s&uni_auth_nonce=%s">%s</a>', $_REQUEST['page'], 'delete', $item['ID'], wp_create_nonce('uni_authenticate_nonce'), __( 'Delete', 'uni-calendar' ))
        );
        return sprintf('%1$s %2$s', $item['post_title'], $this->row_actions($actions) );
    }

    //
    public function extra_tablenav( $which ) {
        global $wp_meta_boxes;
        $views = $this->get_views();
        if ( empty( $views ) )
            return;

        $this->views();
    }

}

?>