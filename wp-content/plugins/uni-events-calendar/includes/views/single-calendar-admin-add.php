                <form name="post" action="<?php echo UniCalendar()->ajax_url(); ?>" method="post" id="post">
                    <input type="hidden" name="action" value="uni_add_calendar" />
                    <input type="hidden" name="uni_auth_nonce" value="<?php echo wp_create_nonce('uni_authenticate_nonce') ?>" />

                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">

                                <div id="uni_calendar_title_wrap">
                                    <div id="uni_calendar_title_inside">
		                                <label class="screen-reader-text" id="uni_input_title_label" for="uni_input_title"><?php _e( 'Enter title here', 'uni-calendar' ) ?></label>
	                                    <input type="text" name="uni_input_title" size="30" value="" id="uni_input_title" spellcheck="true" autocomplete="off" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                    </div>
                                </div>

                                <div id="uni_calendar_options_wrap" class="postbox">
                                    <h3><span><?php _e( 'Calendar settings', 'uni-calendar' ) ?></span></h3>
                                    <div class="uni_calendar_options_inside">

                                        <h4><?php _e( 'Starting date', 'uni-calendar' ) ?></h4>
                                        <p><?php _e( 'Define starting date. It is "today" by default. Leave it blank if you want it to be every day "today"
                                        (so the calendar will display day/week/month of current date) or
                                        specify a date. In case of specific date the calendar will always show this day (for "agendaDay" and "basicDay" views) or
                                        the week of this specific date (for "agendaWeek" and "basicWeek" views) or the month of this specific date (for
                                        "month" view).', 'uni-calendar' ) ?></p>

                                        <div class="uni_calendar_fields_wrap">
                                            <input name="uni_input_default_date" type="text" size="12" class="uni_calendar_date" value="" />
                                        </div>

                                        <hr>

                                        <h4><?php _e( 'Available views', 'uni-calendar' ) ?></h4>
                                        <p><?php _e( 'Choose a way of displaying days and events.', 'uni-calendar' ) ?></p>

                                        <div class="uni_calendar_fields_wrap">
                                            <label for="uni_input_calendar_view"><?php _e( 'Views (default is "Agenda Week")', 'uni-calendar' ) ?></label>
                                            <select id="uni_input_calendar_view" name="uni_input_calendar_view" class="uni_long_field">
                                                <option value="agendaWeek" selected><?php _e( 'Agenda Week', 'uni-calendar' ) ?></option>
                                                <option value="agendaDay"><?php _e( 'Agenda Day', 'uni-calendar' ) ?></option>
                                                <option value="basicWeek"><?php _e( 'Basic Week', 'uni-calendar' ) ?></option>
                                                <option value="basicDay"><?php _e( 'Basic Day', 'uni-calendar' ) ?></option>
                                                <option value="month"><?php _e( 'Month', 'uni-calendar' ) ?></option>
                                                <option value="uniCustomView"><?php _e( 'Custom view & duration', 'uni-calendar' ) ?></option>
                                            </select>
                                        </div>

                                        <p class="uni_calendar_view_additional uni_hidden"><?php _e( 'Please, specify type of view and the number of days to display.', 'uni-calendar' ) ?></p>

                                        <div class="uni_calendar_fields_wrap uni_calendar_view_additional uni_hidden">
                                            <label for="uni_input_calendar_type_view"><?php _e( 'Type of view (default is "agenda")', 'uni-calendar' ) ?></label>
                                            <select id="uni_input_calendar_type_view" name="uni_input_calendar_type_view">
                                                <option value="agenda" selected><?php _e( 'Agenda', 'uni-calendar' ) ?></option>
                                                <option value="basic"><?php _e( 'Basic', 'uni-calendar' ) ?></option>
                                            </select>
                                        </div>

                                        <div class="uni_calendar_fields_wrap uni_calendar_view_additional uni_hidden">
                                            <label for="uni_input_calendar_duration"><?php _e( 'Duration (number of days; digits only; default is "4")', 'uni-calendar' ) ?></label>
                                            <input id="uni_input_calendar_duration" name="uni_input_calendar_duration" type="text" size="12" class="" value="4" data-parsley-trigger="change focusout submit" data-parsley-type="digits" />
                                        </div>

                                        <hr>

                                        <h4><?php _e( 'Range of hours available for each day', 'uni-calendar' ) ?></h4>

                                        <div class="uni_calendar_fields_wrap">
                                            <label for="uni_input_start_time"><?php _e( 'Min. start time (hour)', 'uni-calendar' ) ?></label>
                                            <input name="uni_input_start_time" type="text" size="12" id="uni_input_start_time" class="uni_calendar_min_max_time" value="" />

                                            <label for="uni_input_end_time"><?php _e( 'Max. end time (hour)', 'uni-calendar' ) ?></label>
                                            <input name="uni_input_end_time" type="text" size="12" id="uni_input_end_time" class="uni_calendar_min_max_time" value="" />
                                        </div>

                                    </div>
                                </div>

                                <input class="button button-primary button-large uni_calendar_submit" name="uni_calendar_add" type="button" value="<?php _e( 'Create', 'uni-calendar' ) ?>">
                            </div>
                        </div>
                    </div>
                </form>