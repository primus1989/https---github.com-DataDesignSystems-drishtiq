<?php
/*
*   Class UniCalendarAjax
*
*/

if ( !class_exists( 'UniCalendarAjax' ) ) {

class UniCalendarAjax {

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
                    'uni_add_calendar' => false,
                    'uni_edit_calendar' => false,
                    'uni_add_calendar_event' => false,
                    'uni_edit_calendar_event' => false,
                    'uni_change_calendar_event' => false,
                    'uni_delete_calendar_event' => false,
                    'uni_copy_calendar_event' => false,
                    'uni_calendar_get_events' => true,
                    'uni_calendar_get_events_admin' => false,
                    'uni_calendar_get_event_data' => true
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

	/**
	*   uni_add_calendar
    */
    public function uni_add_calendar() {

	    $aResult 		= $this->_r();

        $sTitle			= ( !empty($_POST['uni_input_title']) ) ? esc_sql($_POST['uni_input_title']) : '';
        $sDefaultDate	= ( !empty($_POST['uni_input_default_date']) ) ? esc_sql($_POST['uni_input_default_date']) : '';

        $sCalView	    = ( !empty($_POST['uni_input_calendar_view']) ) ? esc_sql($_POST['uni_input_calendar_view']) : '';
        $sCalTypeView	= ( !empty($_POST['uni_input_calendar_type_view']) ) ? esc_sql($_POST['uni_input_calendar_type_view']) : '';
        $iCalDuration	= ( !empty($_POST['uni_input_calendar_duration']) ) ? intval(esc_sql($_POST['uni_input_calendar_duration'])) : '';

        $sMinStartTime	= ( !empty($_POST['uni_input_start_time']) ) ? esc_sql($_POST['uni_input_start_time']) : '';
        $sMaxEndTime	= ( !empty($_POST['uni_input_end_time']) ) ? esc_sql($_POST['uni_input_end_time']) : '';

        $sNonce         = esc_sql($_POST['uni_auth_nonce']);
        $sAntiCheat     = esc_sql($_POST['cheaters_always_disable_js']);

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || !wp_verify_nonce( $_POST['uni_auth_nonce'], 'uni_authenticate_nonce' ) ) {
            wp_send_json( $aResult );
        }

        if ( !empty($sTitle) ) {

            $iNewPostId = wp_insert_post( array('post_type' => 'uni_calendar', 'post_title' => $sTitle, 'post_status' => 'publish') );

            update_post_meta($iNewPostId, '_default_date', $sDefaultDate);

            update_post_meta($iNewPostId, '_calendar_view', $sCalView);
            update_post_meta($iNewPostId, '_celendar_type_view', $sCalTypeView);
            update_post_meta($iNewPostId, '_calendar_duration', $iCalDuration);

            update_post_meta($iNewPostId, '_min_start_time', $sMinStartTime);
            update_post_meta($iNewPostId, '_max_end_time', $sMaxEndTime);

            if ( $iNewPostId != 0 ) {
                $aResult['status']      = 'success';
                $aResult['message'] 	= sprintf(__('%s Sucess! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
                $aResult['redirect']    = add_query_arg( array( 'page' => 'uni-events-calendars', 'action' => 'edit-events', 'cal_id' => $iNewPostId, 'uni_auth_nonce' => wp_create_nonce('uni_authenticate_nonce') ), admin_url('admin.php') );
            } else {
                $aResult['message'] 	= sprintf(__('%s Error: calendar not created! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
            }

        } else {
	        $aResult['message'] 	= sprintf(__('%s You must add a title! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_edit_calendar
    */
    public function uni_edit_calendar() {

	    $aResult 		= $this->_r();

        $iCalId         = absint($_POST['cal_id']);
        $sTitle			= ( !empty($_POST['uni_input_title']) ) ? esc_sql($_POST['uni_input_title']) : '';
        $sDefaultDate	= ( !empty($_POST['uni_input_default_date']) ) ? esc_sql($_POST['uni_input_default_date']) : '';

        $sCalView	    = ( !empty($_POST['uni_input_calendar_view']) ) ? esc_sql($_POST['uni_input_calendar_view']) : '';
        $sCalTypeView	= ( !empty($_POST['uni_input_calendar_type_view']) ) ? esc_sql($_POST['uni_input_calendar_type_view']) : '';
        $iCalDuration	= ( !empty($_POST['uni_input_calendar_duration']) ) ? intval(esc_sql($_POST['uni_input_calendar_duration'])) : '';

        $sMinStartTime	= ( !empty($_POST['uni_input_start_time']) ) ? esc_sql($_POST['uni_input_start_time']) : '';
        $sMaxEndTime	= ( !empty($_POST['uni_input_end_time']) ) ? esc_sql($_POST['uni_input_end_time']) : '';

        $sNonce         = esc_sql($_POST['uni_auth_nonce']);
        $sAntiCheat     = esc_sql($_POST['cheaters_always_disable_js']);

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || !wp_verify_nonce( $_POST['uni_auth_nonce'], 'uni_authenticate_nonce' ) ) {
            wp_send_json( $aResult );
        }

        if ( !empty($sTitle) ) {

            $iNewPostId = wp_update_post( array('ID' => $iCalId, 'post_title' => $sTitle) );

            update_post_meta($iNewPostId, '_default_date', $sDefaultDate);

            update_post_meta($iNewPostId, '_calendar_view', $sCalView);
            update_post_meta($iNewPostId, '_celendar_type_view', $sCalTypeView);
            update_post_meta($iNewPostId, '_calendar_duration', $iCalDuration);

            update_post_meta($iNewPostId, '_min_start_time', $sMinStartTime);
            update_post_meta($iNewPostId, '_max_end_time', $sMaxEndTime);

            if ( $iNewPostId != 0 ) {
                $aResult['status']      = 'success';
                $aResult['message'] 	= sprintf(__('%s Sucess! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
                //$aResult['redirect']    = add_query_arg( array( 'page' => 'uni-events-calendars', 'action' => 'edit', 'cal_id' => $iNewPostId, 'uni_auth_nonce' => wp_create_nonce('uni_authenticate_nonce') ), admin_url('admin.php') );
            } else {
                $aResult['message'] 	= sprintf(__('%s Error: calendar not updated! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
            }

        } else {
	        $aResult['message'] 	= sprintf(__('%s You must add a title! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_add_calendar_event
    */
    public function uni_add_calendar_event() {

	    $aResult 		= $this->_r();

        $sTitle			= ( !empty($_POST['uni_input_title']) ) ? esc_sql($_POST['uni_input_title']) : '';
        $sDescription	= ( !empty($_POST['uni_input_description']) ) ? esc_sql($_POST['uni_input_description']) : '';
        $iEventCatId    = ( !empty($_POST['uni_input_cat']) ) ? esc_sql($_POST['uni_input_cat']) : '';
        $sEventTime	    = ( !empty($_POST['uni_event_time_start']) ) ? esc_sql($_POST['uni_event_time_start']) : '';
        $iUserId		= ( !empty($_POST['uni_input_user']) ) ? esc_sql($_POST['uni_input_user']) : '';
        $iCalId			= esc_sql($_POST['uni_cal_id']);

        $sNonce         = esc_sql($_POST['uni_auth_nonce']);
        $sAntiCheat     = esc_sql($_POST['cheaters_always_disable_js']);

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || !wp_verify_nonce( $_POST['uni_auth_nonce'], 'uni_authenticate_nonce' ) ) {
            wp_send_json( $aResult );
        }

        if ( !empty($sTitle) && !empty($sEventTime) && !empty($iCalId) ) {

            $iNewPostId = wp_insert_post(
                array(
                    'post_type' => 'uni_calendar_event',
                    'post_title' => $sTitle,
                    'post_content' => $sDescription,
                    'post_status' => 'publish'
                    )
            );

            if ( $iNewPostId != 0 ) {

                update_post_meta( $iNewPostId, '_uni_event_parent_calendar', $iCalId );
                $iEventTimestamp = strtotime( $sEventTime );
                update_post_meta( $iNewPostId, '_uni_event_timestamp_start', $iEventTimestamp );
                update_post_meta( $iNewPostId, '_uni_event_timestamp_end', $iEventTimestamp + 3600 );
                update_post_meta( $iNewPostId, '_uni_event_user_id', $iUserId );

                if ( !empty($iEventCatId) ) {
                    $iEventCatId = intval($iEventCatId);
                    wp_set_object_terms( $iNewPostId, $iEventCatId, 'uni_calendar_event_cat', false);
	                clean_object_term_cache( $iNewPostId, 'uni_calendar_event_cat' );
                }

                $aResult['status']      = 'success';
                $aResult['message'] 	= '';
                $aResult['refetch'] 	= 'yes';
                //$aResult['redirect']    = add_query_arg( array( 'page' => 'uni-events-calendars', 'action' => 'edit-events', 'cal_id' => $iCalId, 'uni_auth_nonce' => wp_create_nonce('uni_authenticate_nonce') ), admin_url('admin.php') );
            } else {
                $aResult['message'] 	= sprintf(__('%s Error: event not created! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
            }

        } else {
	        $aResult['message'] 	= sprintf(__('%s You must add a title! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_edit_calendar_event
    */
    public function uni_edit_calendar_event() {

	    $aResult 		    = $this->_r();

        $sTitle			    = ( !empty($_POST['uni_input_title']) ) ? esc_sql($_POST['uni_input_title']) : '';
        $sDescription	    = ( !empty($_POST['uni_input_description']) ) ? esc_sql($_POST['uni_input_description']) : '';
        $iEventCatId        = ( !empty($_POST['uni_input_cat']) ) ? esc_sql($_POST['uni_input_cat']) : '';
        $iCopyDate          = ( !empty($_POST['uni_input_copy_to']) ) ? esc_sql($_POST['uni_input_copy_to']) : '';
        $iEventId			= esc_sql($_POST['uni_event_id']);
        $iCalId			    = esc_sql($_POST['uni_cal_id']);
        $iUserId		= ( !empty($_POST['uni_input_user']) ) ? esc_sql($_POST['uni_input_user']) : '';

        $sNonce             = esc_sql($_POST['uni_auth_nonce']);
        $sAntiCheat         = esc_sql($_POST['cheaters_always_disable_js']);

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || !wp_verify_nonce( $_POST['uni_auth_nonce'], 'uni_authenticate_nonce' ) ) {
            wp_send_json( $aResult );
        }

        if ( !empty($sTitle) && !empty($iEventId) ) {

            $iNewPostId = wp_update_post( array('ID' => $iEventId, 'post_title' => $sTitle, 'post_content' => $sDescription) );

            update_post_meta( $iNewPostId, '_uni_event_user_id', $iUserId );

            if ( $iNewPostId != 0 ) {
                if ( !empty($iEventCatId) ) {
                    $iEventCatId = intval($iEventCatId);
                    wp_set_object_terms( $iNewPostId, $iEventCatId, 'uni_calendar_event_cat', false);
	                clean_object_term_cache( $iNewPostId, 'uni_calendar_event_cat' );
                }

                $aResult['status']      = 'success';
                $aResult['message'] 	= sprintf(__('%s Success! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
                $aResult['refetch'] 	= 'yes';
                //$aResult['redirect']    = add_query_arg( array( 'page' => 'uni-events-calendars', 'action' => 'edit-events', 'cal_id' => $iCalId, 'uni_auth_nonce' => wp_create_nonce('uni_authenticate_nonce') ), admin_url('admin.php') );
            } else {
                $aResult['message'] 	= sprintf(__('%s Error: event not updated! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
            }

        } else {
	        $aResult['message'] 	= sprintf(__('%s Something went wrong(( %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_copy_calendar_event
    */
    public function uni_copy_calendar_event() {

	    $aResult 		    = $this->_r();

        $iCopyDateString    = ( !empty($_POST['uni_input_copy_to']) ) ? esc_sql($_POST['uni_input_copy_to']) : '';
        $iEventId			= esc_sql($_POST['uni_event_id']);
        $iCalId			    = esc_sql($_POST['uni_cal_id']);

        $sNonce             = esc_sql($_POST['uni_auth_nonce']);
        $sAntiCheat         = esc_sql($_POST['cheaters_always_disable_js']);

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || !wp_verify_nonce( $_POST['uni_auth_nonce'], 'uni_authenticate_nonce' ) ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iCopyDateString) && !empty($iEventId) && !empty($iCalId) ) {

            // data of current event
            $oEvent         = get_post($iEventId);
            $aPostCustom    = get_post_custom( $iEventId );
            $iEventTitle    = get_the_title( $iEventId );
            $sEventContent  = $oEvent->post_content;
            $aEventCats     = wp_get_post_terms( $iEventId, 'uni_calendar_event_cat' );
            if ( !empty($aEventCats) && !is_wp_error($aEventCats) ) {
                $iEventCatId = $aEventCats[0]->term_id;
            }
            $iDelta = ( $aPostCustom['_uni_event_timestamp_end'][0] - $aPostCustom['_uni_event_timestamp_start'][0] );

            // creating of the new event
            $iNewPostId = wp_insert_post(
                    array(
                        'post_type' => 'uni_calendar_event',
                        'post_title' => $iEventTitle,
                        'post_content' => $sEventContent,
                        'post_status' => 'publish')
            );

            if ( $iNewPostId != 0 ) {

                update_post_meta( $iNewPostId, '_uni_event_parent_calendar', $iCalId );
                $iCopyDateTimestamp = strtotime($iCopyDateString);
                update_post_meta( $iNewPostId, '_uni_event_timestamp_start', $iCopyDateTimestamp );
                update_post_meta( $iNewPostId, '_uni_event_timestamp_end', $iCopyDateTimestamp + $iDelta );
                update_post_meta( $iNewPostId, '_uni_event_user_id', $aPostCustom['_uni_event_user_id'][0] );

                if ( !empty($iEventCatId) ) {
                    $iEventCatId = intval($iEventCatId);
                    wp_set_object_terms( $iNewPostId, $iEventCatId, 'uni_calendar_event_cat', false);
	                clean_object_term_cache( $iNewPostId, 'uni_calendar_event_cat' );
                }

                $aResult['status']      = 'success';
                $aResult['message'] 	= sprintf(__('%s Success! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
                $aResult['refetch'] 	= 'yes';
                //$aResult['redirect']    = add_query_arg( array( 'page' => 'uni-events-calendars', 'action' => 'edit-events', 'cal_id' => $iCalId, 'uni_auth_nonce' => wp_create_nonce('uni_authenticate_nonce') ), admin_url('admin.php') );
            } else {
                $aResult['message'] 	= sprintf(__('%s Error: event not created! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
            }

        } else {
	        $aResult['message'] 	= sprintf(__('%s Something went wrong(( %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_delete_calendar_event
    */
    public function uni_delete_calendar_event() {

	    $aResult 		    = $this->_r();

        $iEventId			= esc_sql($_POST['event_id']);
        $iCalId			    = esc_sql($_POST['cal_id']);

        $sAntiCheat         = esc_sql($_POST['cheaters_always_disable_js']);

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iCalId) && !empty($iEventId) ) {

            $bResult = wp_delete_post( $iEventId, true );

            if ( $bResult ) {

                $aResult['status']      = 'success';
                $aResult['message'] 	= sprintf(__('%s Success! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
                $aResult['refetch'] 	= 'yes';
                //$aResult['redirect']    = add_query_arg( array( 'page' => 'uni-events-calendars', 'action' => 'edit-events', 'cal_id' => $iCalId, 'uni_auth_nonce' => wp_create_nonce('uni_authenticate_nonce') ), admin_url('admin.php') );
            } else {
                $aResult['message'] 	= sprintf(__('%s Error: event not deleted! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
            }

        } else {
	        $aResult['message'] 	= sprintf(__('%s Something went wrong(( %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_change_calendar_event
    */
    public function uni_change_calendar_event() {

	    $aResult 		        = $this->_r();

        $sTypeOfOperation       = ( !empty($_POST['type_operation']) ) ? esc_sql($_POST['type_operation']) : '';
        $iEventDeltaMillisec    = ( !empty($_POST['millisec']) ) ? intval(esc_sql($_POST['millisec'])) : '';
        $iEventDeltaDays        = ( !empty($_POST['days']) ) ? intval(esc_sql($_POST['days'])) : '';
        $iEventDeltaMonths      = ( !empty($_POST['months']) ) ? intval(esc_sql($_POST['months'])) : ''; // TODO!
        $iEventId			    = esc_sql($_POST['event_id']);

        //Object { _milliseconds: 0, _days: -2, _months: 0, _data: Object, _locale: Object }
        //Object { _milliseconds: 25200000, _days: 0, _months: 0, _data: Object, _locale: Object }
        // hour 3600
        // day 86400
        // month

        $sAntiCheat     = esc_sql($_POST['cheaters_always_disable_js']);

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($sTypeOfOperation) && !empty($iEventId) && ( !empty($iEventDeltaMillisec) || !empty($iEventDeltaDays) || !empty($iEventDeltaMonths) ) ) {

            $iEventTimeStartTimestamp = intval(get_post_meta( $iEventId, '_uni_event_timestamp_start', true ));
            $iEventTimeEndTimestamp = intval(get_post_meta( $iEventId, '_uni_event_timestamp_end', true ));

            if ( $sTypeOfOperation == 'resize' ) {
                if ( !empty($iEventDeltaMillisec) && uni_is_positive( $iEventDeltaMillisec ) ) {
                    $iEventDeltaSec = $iEventDeltaMillisec / 1000;
                    $iEventTimeEndTimestamp = $iEventTimeEndTimestamp + $iEventDeltaSec;
                } else if ( !empty($iEventDeltaMillisec) && !uni_is_positive( $iEventDeltaMillisec ) ) {
                    $iEventDeltaSec = $iEventDeltaMillisec / 1000;
                    $iEventTimeEndTimestamp = $iEventTimeEndTimestamp + $iEventDeltaSec;
                }
            } else if ( $sTypeOfOperation == 'drop' ) {
                $iEventDeltaSec = $iEventDeltaMillisec / 1000;
                $iEventTimeStartTimestamp = $iEventTimeStartTimestamp + $iEventDeltaSec;
                $iEventTimeEndTimestamp = $iEventTimeEndTimestamp + $iEventDeltaSec;
            }

            if ( !empty($iEventDeltaDays) ) {
                $iSecondsInDays = intval($iEventDeltaDays * 86400);
                $iEventTimeStartTimestamp = $iEventTimeStartTimestamp + $iSecondsInDays;
                $iEventTimeEndTimestamp = $iEventTimeEndTimestamp + $iSecondsInDays;
            }

            update_post_meta( $iEventId, '_uni_event_timestamp_start', $iEventTimeStartTimestamp );
            $bResult = update_post_meta( $iEventId, '_uni_event_timestamp_end', $iEventTimeEndTimestamp );

            if ( $bResult ) {
                $aResult['status']      = 'success';
                $aResult['message'] 	= sprintf(__('%s Success! %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
            } else {
                $aResult['message'] 	= sprintf(__('%s Something went wrong(( %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
            }

        } else {
	        $aResult['message'] 	= sprintf(__('%s Something went wrong(( %s', 'uni-calendar'), '<div class="notice is-dismissible"><p>', '</p></div>');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_calendar_get_events
    */
    public function uni_calendar_get_events() {

	    $aResult 		    = $this->_r();

        $iStart			    = ( !empty($_POST['start']) ) ? esc_sql($_POST['start']) : '';
        $iEnd			    = ( !empty($_POST['end']) ) ? esc_sql($_POST['end']) : '';
        $iCalId			    = ( !empty($_POST['cal_id']) ) ? esc_sql($_POST['cal_id']) : '';

        $sAntiCheat         = esc_sql($_POST['cheaters_always_disable_js']);

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iStart) && !empty($iEnd) && !empty($iCalId) ) {

            $aEventArgs = array(
                'post_type'	=> 'uni_calendar_event',
                'post_status' => 'publish',
                'ignore_sticky_posts'	=> 1,
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => '_uni_event_parent_calendar',
                        'value' => $iCalId,
                        'compare' => '=',
                        'type' => 'NUMERIC'
                    ),
                    array(
                        'key' => '_uni_event_timestamp_start',
                        'value' => $iStart,
                        'compare' => '>=',
                        'type' => 'NUMERIC'
                    ),
                    array(
                        'key' => '_uni_event_timestamp_end',
                        'value' => $iEnd,
                        'compare' => '<=',
                        'type' => 'NUMERIC'
                    )
                )
            );

            $oEventQuery = new WP_Query( $aEventArgs );

            if ( !empty($oEventQuery->found_posts) ) {
                $aEvents = array();
                foreach ( $oEventQuery->posts as $oEvent ) {
                    // get event data
                    $aEventCustom = get_post_custom($oEvent->ID);
                    $aEventCats = wp_get_post_terms( $oEvent->ID, 'uni_calendar_event_cat' );
                    $sEventCatSlug = $iEventCatId = '';
                    $sCatBgColor = '#8ce4cf';
                    $sCatBorderColor = '#5fc7ae';
                    if ( !empty($aEventCats) && !is_wp_error($aEventCats) ) {
                        $aTermData = get_option('uni_calendar_tax_'.$aEventCats[0]->term_id.'_data');
                        if ( !empty( $aTermData['background'] ) ) {
                            $sCatBgColor = $aTermData['background'];
                        }
                        if ( !empty( $aTermData['border'] ) ) {
                            $sCatBorderColor = $aTermData['border'];
                        }
                        $sEventCatSlug = $aEventCats[0]->slug;
                        $iEventCatId = $aEventCats[0]->term_id;
                    }

                    // create event object
                    $oCalendarEvent                     = new stdClass();
                    $oCalendarEvent->title              = apply_filters( 'uni_calendar_class_title_filter', $oEvent->post_title, $oEvent );
                    $oCalendarEvent->start              = date('Y-m-d H:i:s', $aEventCustom['_uni_event_timestamp_start'][0]);
                    $oCalendarEvent->end                = date('Y-m-d H:i:s', $aEventCustom['_uni_event_timestamp_end'][0] );
                    $oCalendarEvent->url                = '';
                    $oCalendarEvent->className          = $sEventCatSlug;
                    $oCalendarEvent->event_cat_id       = $iEventCatId;
                    $oCalendarEvent->event_id           = $oEvent->ID;
                    $oCalendarEvent->backgroundColor    = $sCatBgColor;
                    $oCalendarEvent->borderColor        = $sCatBorderColor;
                    $oCalendarEvent->event_desc         = ( !empty($oEvent->post_content) ) ? esc_attr($oEvent->post_content) : '';
                    $oCalendarEvent->event_user         = ( !empty($aEventCustom['_uni_event_user_id'][0]) ) ? $aEventCustom['_uni_event_user_id'][0] : '';

                    // add to array of events
                    $aEvents[]                          = $oCalendarEvent;
                }

                $aResult['status']      = 'success';
                $aResult['message'] 	= __('Success!', 'uni-calendar');
                $aResult['events']      = $aEvents;

            } else {
                $aResult['message'] 	= __('No events in this period', 'uni-calendar');
            }

        } else {
	        $aResult['message'] 	    = __('Not enough data', 'uni-calendar');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_calendar_get_events_admin
    */
    public function uni_calendar_get_events_admin() {

	    $aResult 		    = $this->_r();

        $iStart			    = ( !empty($_POST['start']) ) ? esc_sql($_POST['start']) : '';
        $iEnd			    = ( !empty($_POST['end']) ) ? esc_sql($_POST['end']) : '';
        $iCalId			    = ( !empty($_POST['cal_id']) ) ? esc_sql($_POST['cal_id']) : '';

        $sAntiCheat         = esc_sql($_POST['cheaters_always_disable_js']);

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iStart) && !empty($iEnd) && !empty($iCalId) ) {

            $aEventArgs = array(
                'post_type'	=> 'uni_calendar_event',
                'post_status' => 'publish',
                'ignore_sticky_posts'	=> 1,
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => '_uni_event_parent_calendar',
                        'value' => $iCalId,
                        'compare' => '=',
                        'type' => 'NUMERIC'
                    ),
                    array(
                        'key' => '_uni_event_timestamp_start',
                        'value' => $iStart,
                        'compare' => '>=',
                        'type' => 'NUMERIC'
                    ),
                    array(
                        'key' => '_uni_event_timestamp_end',
                        'value' => $iEnd,
                        'compare' => '<=',
                        'type' => 'NUMERIC'
                    )
                )
            );

            $oEventQuery = new WP_Query( $aEventArgs );

            if ( !empty($oEventQuery->found_posts) ) {
                $aEvents = array();
                foreach ( $oEventQuery->posts as $oEvent ) {
                    // get event data
                    $aEventCustom = get_post_custom($oEvent->ID);
                    $aEventCats = wp_get_post_terms( $oEvent->ID, 'uni_calendar_event_cat' );
                    $sEventCatSlug = $iEventCatId = '';
                    $sCatBgColor = '#8ce4cf';
                    $sCatBorderColor = '#5fc7ae';
                    if ( !empty($aEventCats) && !is_wp_error($aEventCats) ) {
                        $aTermData = get_option('uni_calendar_tax_'.$aEventCats[0]->term_id.'_data');
                        if ( !empty( $aTermData['background'] ) ) {
                            $sCatBgColor = $aTermData['background'];
                        }
                        if ( !empty( $aTermData['border'] ) ) {
                            $sCatBorderColor = $aTermData['border'];
                        }
                        $sEventCatSlug = $aEventCats[0]->slug;
                        $iEventCatId = $aEventCats[0]->term_id;
                    }

                    // create event object
                    $oCalendarEvent                     = new stdClass();
                    $oCalendarEvent->title              = esc_attr($oEvent->post_title);
                    $oCalendarEvent->start              = date('Y-m-d H:i:s', $aEventCustom['_uni_event_timestamp_start'][0]);
                    $oCalendarEvent->end                = date('Y-m-d H:i:s', $aEventCustom['_uni_event_timestamp_end'][0] );
                    $oCalendarEvent->url                = '';
                    $oCalendarEvent->className          = $sEventCatSlug;
                    $oCalendarEvent->event_cat_id       = $iEventCatId;
                    $oCalendarEvent->event_id           = $oEvent->ID;
                    $oCalendarEvent->backgroundColor    = $sCatBgColor;
                    $oCalendarEvent->borderColor        = $sCatBorderColor;
                    $oCalendarEvent->event_desc         = ( !empty($oEvent->post_content) ) ? esc_attr($oEvent->post_content) : '';
                    $oCalendarEvent->event_user         = ( !empty($aEventCustom['_uni_event_user_id'][0]) ) ? $aEventCustom['_uni_event_user_id'][0] : '';

                    // add to array of events
                    $aEvents[]                          = $oCalendarEvent;
                }

                $aResult['status']      = 'success';
                $aResult['message'] 	= __('Success!', 'uni-calendar');
                $aResult['events']      = $aEvents;

            } else {
                $aResult['message'] 	= __('No events in this period', 'uni-calendar');
            }

        } else {
	        $aResult['message'] 	    = __('Not enough data', 'uni-calendar');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_calendar_get_event_data
    */
    public function uni_calendar_get_event_data() {

	    $aResult 		    = $this->_r();

        $iEventId			= ( !empty($_POST['event_id']) ) ? esc_sql($_POST['event_id']) : '';

        $sAntiCheat         = esc_sql($_POST['cheaters_always_disable_js']);

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iEventId) ) {

            $oEvent         = get_post($iEventId);
            $aPostCustom    = get_post_custom($iEventId);
            if (  !empty($aPostCustom['_uni_event_user_id'][0]) ) {
                $oUser      = get_user_by('id', $aPostCustom['_uni_event_user_id'][0]);
            }

            if ( !empty($oEvent) ) {

                $sContent           = apply_filters( 'the_content', $oEvent->post_content );
                $sContent           = str_replace( ']]>', ']]&gt;', $sContent );

                $sOutput = '<h3>'.get_the_title($iEventId).'</h3>
                    <div class="classesDescWrap">';
                if ( !empty($aPostCustom['_uni_event_user_id'][0]) && !empty($oUser) ) {
                    $sOutput .= '<div class="classesInstructorWrap">';
                    $sOutput .= do_shortcode('[uav-display-avatar id="'.$oUser->ID.'" size="100" alt="'.esc_attr( $oUser->display_name ).'"]');
                    $sOutput .= '<h4>'.esc_html( $oUser->display_name ).'</h4>
                        </div>';
                }
                $sOutput .= $sContent;
                $sOutput .= '</div>';

                $aResult['status']      = 'success';
                $aResult['message'] 	= __('Success!', 'uni-calendar');
                $aResult['output']      = $sOutput;

            } else {
                $aResult['message'] 	= __('Found no event', 'uni-calendar');
            }

        } else {
	        $aResult['message'] 	    = __('Not enough data', 'uni-calendar');
        }

        wp_send_json( $aResult );
    }

}

}

?>