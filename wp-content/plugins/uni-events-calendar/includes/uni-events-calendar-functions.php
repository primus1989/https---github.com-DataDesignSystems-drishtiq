<?php
//
if ( !function_exists('uni_is_positive') ) {
    function uni_is_positive( $iNumber ) {
        return is_int( $iNumber ) && ( $iNumber >= 0 );
    }
}

//
function uni_calendar_current_week_date_range( $bReadableFormat = false ){
    $monday = strtotime("last monday");
    $monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;

    $sunday = strtotime(date("Y-m-d",$monday)." +6 days");

    $aDates = array();
    if ( $bReadableFormat ) {
        $aDates['start'] = date("Y-m-d H:00:01",$monday);
        $aDates['end'] = date("Y-m-d 23:59:59",$sunday);
    } else {
        $aDates['start'] = strtotime(date("Y-m-d H:00:01",$monday));
        $aDates['end'] = strtotime(date("Y-m-d 23:59:59",$sunday));
    }

    return $aDates;
}

//
add_action('uni_calendar_before_calendar_action', 'uni_calendar_display_categories', 10);
function uni_calendar_display_categories(){
    ?>
			<div class="pagePanel clear">
            <?php $aEventCats = get_terms('uni_calendar_event_cat');
            if ( !empty($aEventCats) && !is_wp_error($aEventCats) ) {
            ?>
				<ul class="productFilter classesFilter clear">
                    <li><a class="active" data-filter="all" href="#"><?php _e('All', 'uni-calendar') ?> </a></li>
                <?php foreach ( $aEventCats as $oTerm ) { ?>
					<li><a data-filter="<?php echo $oTerm->slug ?>" href="#"><?php echo $oTerm->name ?></a></li>
				<?php } ?>
				</ul>
            <?php } ?>
			</div>
    <?php
}

//
add_action('uni_calendar_classes_modal_window_action', 'uni_calendar_classes_modal_window_func', 10);
function uni_calendar_classes_modal_window_func(){
    ?>
            <div class="classesDescPopup" id="classesPopup">
                <div class="uni-calendar-ajax-container">
                </div>
            </div>
    <?php
}
?>