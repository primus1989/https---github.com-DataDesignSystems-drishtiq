<?php
if ( !empty($oPost) ) {
$aPostCustom = get_post_custom($oPost->ID);
$sCalendarEvents = ( !empty($aPostCustom['_uni_calendar_events_ids']) ) ? $aPostCustom['_uni_calendar_events_ids'] : '';
$sCurrentTime = ( !empty($aPostCustom['_default_date'][0]) ) ? esc_attr($aPostCustom['_default_date'][0]) : date('Y-m-d', current_time('timestamp'));
?>
		<script>
			jQuery(document).ready(function() {

				jQuery("#uni-calendar").fullCalendar({
				    lang: unicalendar.locale,      
					header: {
						left: 'prev',
						center: 'title',
						right: 'next'
					},
                    <?php if ( !empty($aPostCustom['_calendar_view'][0]) && $aPostCustom['_calendar_view'][0] == 'uniCustomView' ) { ?>
                    views: {
                        uniCustomView: {
                            type: '<?php echo ( !empty($aPostCustom['_celendar_type_view'][0]) ) ? esc_attr($aPostCustom['_celendar_type_view'][0]) : 'agenda'; ?>',
                            duration: { days: <?php echo ( !empty($aPostCustom['_calendar_duration'][0]) ) ? esc_attr($aPostCustom['_calendar_duration'][0]) : '4'; ?> }
                        }
                    },
                    <?php } ?>
                    height: 'auto',
					axisFormat: 'HH:mm',
					minTime: '<?php echo ( !empty($aPostCustom['_min_start_time'][0]) ) ? esc_attr($aPostCustom['_min_start_time'][0]) : '00:00:00'; ?>',
					maxTime: '<?php echo ( !empty($aPostCustom['_max_end_time'][0]) ) ? esc_attr($aPostCustom['_max_end_time'][0]) : '23:59:59'; ?>',
					defaultView: '<?php echo ( !empty($aPostCustom['_calendar_view'][0]) ) ? esc_attr($aPostCustom['_calendar_view'][0]) : 'agendaWeek'; ?>',
					defaultDate: '<?php echo $sCurrentTime; ?>',
					firstDay: 1,
					slotDuration: '00:30:00',
					columnFormat: 'dddd',
					allDaySlot: false,
					editable: true,
					eventLimit: true,
                    events: function(start, end, timezone, callback) {
                        var container = jQuery("#uni-calendar");

                        var dataToSend = {
                                start : start.unix(),
                                end : end.unix(),
                                action : 'uni_calendar_get_events_admin',
                                cal_id : '<?php echo $oPost->ID; ?>',
                                cheaters_always_disable_js : 'true_bro'
                        };

                        jQuery.ajax({
                            type: 'post',
                            url: ajaxurl,
                            dataType: 'json',
                            data: dataToSend,
	        	            beforeSend: function(){
	        	                container.block({
	        	                    message: null,
                                    overlayCSS: { background: '#fff', opacity: 0.6 }
                                });
	        	            },
                            success: function(response) {
                                container.unblock();
                                if ( response.status == "success" ) {
                                    //console.log(response.events);
                                    callback(response.events);
                                } else if ( response.status == "error" ) {
                                    var events = [];
                                    callback(events);
                                }
                            },
	        	            error:function(response){
	        	                container.unblock();
	        	            }
                        });
                    },
                    eventDrop: function(event, delta, revertFunc) {
                        //console.log(event);
                        //console.log(delta);
                        uni_calendar_event_change( 'drop', event, delta, revertFunc );
                    },
                    eventResize: function(event, delta, revertFunc) {
                        //console.log(event);
                        //console.log(delta);
                        uni_calendar_event_change( 'resize', event, delta, revertFunc );
                    },
                    dayClick: function(date, jsEvent, view) {
                        //console.log('Clicked on: ' + date + ' | ' + date.format());
                        jQuery("#uni_add_calendar_event > input[name=uni_event_time_start]").val(date);
                        $new_event.dialog('open');
                    },
                    eventClick: function(calEvent, jsEvent, view) {
                        //console.log(calEvent);
                        jQuery(".uni_calendar_event_form input[name=uni_event_id]").val(calEvent.event_id);
                        jQuery(".uni_calendar_event_form .uni-event-delete-link").data("event_id", calEvent.event_id);
                        jQuery(".uni_calendar_event_form input[name=uni_event_time_start]").val(calEvent.start._i);
                        jQuery(".uni_calendar_event_form input[name=uni_event_time_end]").val(calEvent.end._i);
                        jQuery(".uni_calendar_event_form div input[name=uni_input_title]").val(calEvent.title);
                        jQuery(".uni_calendar_event_form div select[name=uni_input_cat]").val(calEvent.event_cat_id);
                        jQuery(".uni_calendar_event_form div textarea[name=uni_input_description]").val(calEvent.event_desc);
                        jQuery(".uni_calendar_event_form div select[name=uni_input_user]").val(calEvent.event_user);
                        jQuery(".uni_calendar_event_form div input[name=uni_input_copy_to]").val('');
                        $edit_event.dialog('open');
                    }
				});

                // create a new event
                var $new_event = jQuery("#uni-new-event-modal");
                $new_event.dialog({
                    'dialogClass'   : 'wp-dialog',
                    'modal'         : true,
                    'autoOpen'      : false,
                    'closeOnEscape' : true,
                    'width'         : 350,
                    'buttons'       : {
                        "Close": function() {
                            jQuery(this).dialog('close');
                        }
                    }
                });

                // edit an event
                var $edit_event = jQuery("#uni-edit-event-modal");
                $edit_event.dialog({
                    'dialogClass'   : 'wp-dialog',
                    'modal'         : true,
                    'autoOpen'      : false,
                    'closeOnEscape' : true,
                    'width'         : 350,
                    'buttons'       : {
                        "Close": function() {
                            jQuery(this).dialog('close');
                        }
                    }
                });

            // uni_calendar_event_change
            function uni_calendar_event_change( type_operation, event, delta, revertFunc ){

                var container = jQuery("#uni-calendar-wrapper"),
                    ajax_msg_container = jQuery("#ajax-messages");
                var dataToSend = {};
                    dataToSend.action = 'uni_change_calendar_event';
                    dataToSend.type_operation = type_operation;
                    dataToSend.event_id = event.event_id;
                    dataToSend.millisec = delta._milliseconds;
                    dataToSend.days = delta._days;
                    dataToSend.months = delta._months;
                    dataToSend.cheaters_always_disable_js = 'true_bro';

			        jQuery.ajax({
				            type: 'post',
	        	            url: ajaxurl,
	        	            data: dataToSend,
	        	            dataType: 'json',
	        	            beforeSend: function(){
	        	                ajax_msg_container.empty();
	        	                container.block({
	        	                    message: null,
                                    overlayCSS: { background: '#fff', opacity: 0.6 }
                                });
	        	            },
	        	            success: function(response) {
	        		            if ( response.status == "success" ) {
                                    container.unblock();
                                if ( response.message.length > 0 ) {
                                    ajax_msg_container.html(response.message);
                                }
	        		        } else if ( response.status == "error" ) {
                                container.unblock();
                                if ( response.message.length > 0 ) {
                                    ajax_msg_container.html(response.message);
                                }
                                revertFunc();
	        		        }
	        	        },
	        	        error:function(response){
	        	            container.unblock();
	        	            ajax_msg_container.html(response.message);
                            revertFunc();
	        	        }
	                });
                    return false;
            }

			});
		</script>

<div class="postbox" style="padding: 30px;">
    <div id="uni-calendar"></div>
</div>

<div id="uni-new-event-modal" class="uni-event-modal" title="<?php _e('Create new event', 'uni-calendar') ?>">
    <form id="uni_add_calendar_event" class="uni_calendar_event_form" action="<?php echo UniCalendar()->ajax_url(); ?>" method="post">
        <input type="hidden" name="action" value="uni_add_calendar_event" />
        <input type="hidden" name="uni_auth_nonce" value="<?php echo wp_create_nonce('uni_authenticate_nonce') ?>" />
        <input type="hidden" name="uni_cal_id" value="<?php echo $oPost->ID; ?>" />
        <input type="hidden" name="uni_event_time_start" value="" />

        <div class="uni-event-form-row">
            <label for="uni_input_title_add"><?php _e('Title', 'uni-calendar') ?>*</label>
            <input type="text" name="uni_input_title" id="uni_input_title_add" value="" class="text ui-widget-content ui-corner-all" data-parsley-required="true" data-parsley-trigger="change focusout submit">
        </div>

        <div class="uni-event-form-row">
            <label for="uni_input_cat_add"><?php _e('Category', 'uni-calendar') ?></label>
            <?php $aEventCats = get_terms('uni_calendar_event_cat', array('hide_empty' => false));
            if ( !empty($aEventCats) && !is_wp_error($aEventCats) ) {
                echo '<select id="uni_input_cat_add" name="uni_input_cat">';
                foreach ( $aEventCats as $oTerm ) {
                    echo '<option value="'.$oTerm->term_id.'">'.$oTerm->name.'</option>';
                }
                echo '</select>';
            } else {
                echo '<p>'.__('No event categories created.', 'uni-calendar').'</p>';
            }
            ?>
        </div>

        <div class="uni-event-form-row">
            <label for="uni_input_description_add"><?php _e('Description', 'uni-calendar') ?></label>
            <textarea name="uni_input_description" id="uni_input_description_add" class="text ui-widget-content ui-corner-all"></textarea>
        </div>

        <div class="uni-event-form-row">
            <label for="uni_input_user_add"><?php _e('Instructor', 'uni-calendar') ?></label>
            <?php $oUserQuery = new WP_User_Query( array('role' => 'instructor') );
            if ( ! empty( $oUserQuery->results ) ) {
                echo '<select id="uni_input_user_add" name="uni_input_user">';
                    echo '<option value="">'.__( 'Please choose...', 'uni-calendar' ).'</option>';
                foreach ( $oUserQuery->results as $oUser ) {
                    echo '<option value="'.$oUser->ID.'">'.esc_html( $oUser->display_name ).'</option>';
                }
                echo '</select>';
            } else {
                echo '<p>'.__('No instructors found.', 'uni-calendar').'</p>';
            }
            ?>
        </div>

        <div class="uni-event-form-row">
            <input class="button button-primary button-large uni_calendar_submit" name="uni_add_calendar_event" type="button" value="<?php _e( 'Create', 'uni-calendar' ) ?>">
        </div>

    </form>
</div>

<div id="uni-edit-event-modal" class="uni-event-modal" title="<?php _e('Edit event', 'uni-calendar') ?>">
    <form id="uni_edit_calendar_event" class="uni_calendar_event_form" action="<?php echo UniCalendar()->ajax_url(); ?>" method="post">
        <input type="hidden" name="action" value="uni_edit_calendar_event" />
        <input type="hidden" name="uni_auth_nonce" value="<?php echo wp_create_nonce('uni_authenticate_nonce') ?>" />
        <input type="hidden" name="uni_cal_id" value="<?php echo $oPost->ID; ?>" />
        <input type="hidden" name="uni_event_id" value="" />
        <input type="hidden" name="uni_event_time_start" value="" />
        <input type="hidden" name="uni_event_time_end" value="" />

        <div class="uni-event-form-row">
            <label for="uni_input_title"><?php _e('Title', 'uni-calendar') ?>*</label>
            <input type="text" name="uni_input_title" id="uni_input_title_edit" value="" class="text ui-widget-content ui-corner-all" data-parsley-required="true" data-parsley-trigger="change focusout submit">
        </div>

        <div class="uni-event-form-row">
            <label for="uni_input_cat"><?php _e('Category', 'uni-calendar') ?></label>
            <?php $aEventCats = get_terms('uni_calendar_event_cat', array('hide_empty' => false));
            if ( !empty($aEventCats) && !is_wp_error($aEventCats) ) {
                echo '<select id="uni_input_cat_edit" name="uni_input_cat">';
                foreach ( $aEventCats as $oTerm ) {
                    echo '<option value="'.$oTerm->term_id.'">'.$oTerm->name.'</option>';
                }
                echo '</select>';
            } else {
                echo '<p>'.__('No event categories created.', 'uni-calendar').'</p>';
            }
            ?>
        </div>

        <div class="uni-event-form-row">
            <label for="uni_input_description"><?php _e('Description', 'uni-calendar') ?></label>
            <textarea name="uni_input_description" id="uni_input_description_edit" class="text ui-widget-content ui-corner-all"></textarea>
        </div>

        <div class="uni-event-form-row">
            <label for="uni_input_user"><?php _e('Instructor', 'uni-calendar') ?></label>
            <?php $oUserQuery = new WP_User_Query( array('role' => 'instructor') );
            if ( ! empty( $oUserQuery->results ) ) {
                echo '<select id="uni_input_user_edit" name="uni_input_user">';
                    echo '<option value="">'.__( 'Please choose...', 'uni-calendar' ).'</option>';
                foreach ( $oUserQuery->results as $oUser ) {
                    echo '<option value="'.$oUser->ID.'">'.esc_html( $oUser->display_name ).'</option>';
                }
                echo '</select>';
            } else {
                echo '<p>'.__('No instructors found.', 'uni-calendar').'</p>';
            }
            ?>
        </div>

        <div class="uni-event-form-row">
            <input class="button button-primary button-large uni_calendar_submit" name="uni_edit_calendar_event" type="button" value="<?php _e( 'Edit event', 'uni-calendar' ) ?>">
        </div>
        <div class="uni-event-form-row">
            <a class="uni-delete-link uni-event-delete-link uni_calendar_ajax_link" data-cal_id="<?php echo $oPost->ID; ?>" data-action="uni_delete_calendar_event"><?php _e( 'Delete', 'uni-calendar' ) ?></a>
        </div>

    </form>
    <hr>
    <form id="uni_copy_calendar_event" class="uni_calendar_event_form" action="<?php echo UniCalendar()->ajax_url(); ?>" method="post">
        <input type="hidden" name="action" value="uni_copy_calendar_event" />
        <input type="hidden" name="uni_auth_nonce" value="<?php echo wp_create_nonce('uni_authenticate_nonce') ?>" />
        <input type="hidden" name="uni_cal_id" value="<?php echo $oPost->ID; ?>" />
        <input type="hidden" name="uni_event_id" value="" />

        <div class="uni-event-form-row">
            <label for="uni_input_copy_to"><?php _e('Make a copy of this event on...', 'uni-calendar') ?></label>
            <input type="text" name="uni_input_copy_to" id="uni_input_copy_to" value="" class="text ui-widget-content ui-corner-all uni_calendar_date_time" data-parsley-required="true" data-parsley-trigger="change focusout submit">
        </div>

        <div class="uni-event-form-row">
            <input class="button button-primary button-large uni_calendar_submit" name="uni_copy_calendar_event" type="button" value="<?php _e( 'Make a copy', 'uni-calendar' ) ?>">
        </div>

    </form>
</div>

<?php } else {
    echo _e('The ID of the calendar is not specified!', 'uni-calendar');
} ?>