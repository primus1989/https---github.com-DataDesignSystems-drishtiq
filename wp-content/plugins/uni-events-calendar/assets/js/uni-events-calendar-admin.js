if ( unicalendar.parsley_translation ) {
    window.ParsleyValidator.setLocale(unicalendar.locale);
} else {
    window.ParsleyValidator.setLocale('en');
}

jQuery( document ).ready( function( $ ) {
    'use strict';

    //
    $(".uni_calendar_min_max_time").datetimepicker({
        datepicker:false,
        lazyInit:true,
        dayOfWeekStart:1,
        format:'H:i',
        allowTimes:[
            '01:00','01:30','02:00','02:30','03:00','03:30','04:00','04:30','05:00','05:30','06:00','06:30',
            '07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30',
            '13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30',
            '19:00','19:30','20:00','20:30','21:00','21:30','22:00','22:30','23:00','23:30','23:59'
        ],
        closeOnDateSelect:true
    });

    //
    $(".uni_calendar_date_time").datetimepicker({
        datepicker:true,
        lazyInit:true,
        dayOfWeekStart:1,
        format:'Y-m-d H:i',
        allowTimes:[
            '01:00','01:30','02:00','02:30','03:00','03:30','04:00','04:30','05:00','05:30','06:00','06:30',
            '07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30',
            '13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30',
            '19:00','19:30','20:00','20:30','21:00','21:30','22:00','22:30','23:00','23:30','23:59'
        ],
        defaultTime:'08:00'
    });

    //
    $(".uni_calendar_date").datetimepicker({
        datepicker:true,
        lazyInit:true,
        format:'Y-m-d',
        closeOnDateSelect:true,
        timepicker:false
    });

    // Add color picker
    $('.uni-calendar-colour-field').wpColorPicker();

    //
    $(document).on("change", "#uni_input_calendar_view", function(){
        var this_value = $(this).val();
        if ( this_value == 'uniCustomView' ) {
            $(".uni_calendar_view_additional").show();
        } else {
            $(".uni_calendar_view_additional").hide();
        }
    });

    //
    $("body").on("click", ".uni_calendar_submit", function(e){

        var submit_button = $(this),
            this_form = submit_button.closest("form"),
            action = this_form.attr('action'),
            ajax_msg_container = $("#ajax-messages"),
            dataToSend = this_form.serialize();

        if ( action == 'uni_add_calendar' ) {
            var container = $("#uni-calendar-wrapper");
        } else {
            var container = this_form;
        }

        var form_valid = this_form.parsley({excluded: '[disabled]'}).validate();

            if ( form_valid ) {
                dataToSend = this_form.serialize();

			    $.ajax({
				    type: 'post',
	        	    url: action,
	        	    data: dataToSend + '&cheaters_always_disable_js=' + 'true_bro',
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

	        		        $(".ui-dialog-content").dialog("close");
                            container.unblock();
                            if ( response.message.length > 0 ) {
                                ajax_msg_container.html(response.message);
                            }

                            if ( response.redirect.length > 0 ) {
						        setTimeout(function() {
							        window.location.replace( response.redirect );
						        }, 500);
                            }

                            if ( response.refetch.length > 0 ) {
                                $("#uni-calendar").fullCalendar('refetchEvents');
                            }

	        		    } else if ( response.status == "error" ) {
                            container.unblock();
                            if ( $(".ui-dialog-content").length > 0 ) {
                                $(".ui-dialog-content").dialog("close");
                            }
                            if ( response.message.length > 0 ) {
                                ajax_msg_container.html(response.message);
                            }
	        		    }
	        	    },
	        	    error:function(response){
	        	        container.unblock();
                        if ( $(".ui-dialog-content").length > 0 ) {
                            $(".ui-dialog-content").dialog("close");
                        }
	        	        ajax_msg_container.html(response.message);
	        	    }
	            });
            }
            return false;
    });

    //
    $("body").on("click", ".uni_calendar_ajax_link", function(e){

	    e.preventDefault();
        var $this_link = $(this),
            container = $(this).parent(),
            ajax_msg_container = $("#ajax-messages");

                var dataToSend = $this_link.data();
                    dataToSend.cheaters_always_disable_js = 'true_bro';

			    $.ajax({
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

                            $(".ui-dialog-content").dialog("close");
                            container.unblock();
                            if ( response.message.length > 0 ) {
                                ajax_msg_container.html(response.message);
                            }

                            if ( response.redirect.length > 0 ) {
						        setTimeout(function() {
							        window.location.replace( response.redirect );
						        }, 500);
                            }

                            if ( response.refetch.length > 0 ) {
                                $("#uni-calendar").fullCalendar('refetchEvents');
                            }

	        		    } else if ( response.status == "error" ) {
                            container.unblock();
                            if ( $(".ui-dialog-content").length > 0 ) {
                                $(".ui-dialog-content").dialog("close");
                            }
                            if ( response.message.length > 0 ) {
                                ajax_msg_container.html(response.message);
                            }
	        		    }
	        	    },
	        	    error:function(response){
	        	        container.unblock();
                        if ( $(".ui-dialog-content").length > 0 ) {
                            $(".ui-dialog-content").dialog("close");
                        }
	        	        ajax_msg_container.html(response.message);
	        	    }
	            });
            return false;
    });

});