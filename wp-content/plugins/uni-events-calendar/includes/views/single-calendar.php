<?php
$iCalId = $aAttr['id'];
$aPostCustom = get_post_custom($iCalId);
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
                    height: "auto",
					axisFormat: 'HH:mm',
					minTime: '<?php echo ( !empty($aPostCustom['_min_start_time'][0]) ) ? esc_attr($aPostCustom['_min_start_time'][0]) : '00:00:00'; ?>',
					maxTime: '<?php echo ( !empty($aPostCustom['_max_end_time'][0]) ) ? esc_attr($aPostCustom['_max_end_time'][0]) : '23:59:59'; ?>',
					defaultView: '<?php echo ( !empty($aPostCustom['_calendar_view'][0]) ) ? esc_attr($aPostCustom['_calendar_view'][0]) : 'agendaWeek'; ?>',
					defaultDate: '<?php echo $sCurrentTime; ?>',
					firstDay: 1,
					slotDuration: '00:30:00',
					columnFormat: 'dddd',
					allDaySlot: false,
					editable: false,
					eventLimit: true,
                    events: function(start, end, timezone, callback) {
                        var container = jQuery("#uni-calendar");

                        var dataToSend = {
                                start : start.unix(),
                                end : end.unix(),
                                action : 'uni_calendar_get_events',
                                cal_id : '<?php echo $iCalId; ?>',
                                cheaters_always_disable_js : 'true_bro'
                        };

                        jQuery.ajax({
                            type: 'post',
                            url: unicalendar.ajax_url,
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
                    eventClick: function(calEvent, jsEvent, view) {

                        jQuery.fancybox.open("#classesPopup", {
		                    wrapCSS: 'fancyboxClassesPopup',
		                    helpers : {
	                            overlay : {
	                                css : {
	                                    'background' : 'rgba(255, 255, 255, 0.94)'
	                                }
	                            }
	                        },
                            beforeShow: function() {

                                var container = jQuery(".uni-calendar-ajax-container");
                                container.empty();

                                var sendData = {
                                        action : 'uni_calendar_get_event_data',
                                        event_id : calEvent.event_id,
                                        cheaters_always_disable_js : 'true_bro',
                                        };

			                    jQuery.ajax({
				                    type: 'post',
	        	                    url: unicalendar.ajax_url,
	        	                    data: sendData,
	        	                    dataType: 'json',
                                    beforeSend: function(response) {
	        	                        container.block({
	        	                            message: null,
                                            overlayCSS: { background: '#fff url(' + unicalendar.loader + ') no-repeat center', backgroundSize: '24px 24px', opacity: 0.6 }
                                        });
                                    },
	        	                    success: function(response) {
	        	                        //console.log(response);
	        		                    if ( response.status == "success" ) {
	        		                        container.unblock();
	        		                        container.html(response.output);
                                            jQuery.fancybox.update();
	        		                    } else if ( response.status == "error" ) {
	        		                        container.unblock();
	        		                        container.html(response.message);
                                            jQuery.fancybox.update();
	        		                    }
	        	                    },
	        	                    error:function(response){
	        	                        container.unblock();
	        		                    container.html(response.message);
                                        jQuery.fancybox.update();
	        	                    }
	                            });

                            }
	                    });
                    }
				});

			});
		</script>

            <?php do_action('uni_calendar_before_calendar_action'); ?>

			<div class="classesCallendar">
				<div id="uni-calendar"></div>
			</div>

            <?php do_action('uni_calendar_classes_modal_window_action'); ?>

            <?php do_action('uni_calendar_after_calendar_action'); ?>