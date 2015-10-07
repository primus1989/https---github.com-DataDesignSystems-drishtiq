<?php
/**
 * Initialize the custom Meta Boxes.
 */
add_action( 'admin_init', 'custom_meta_boxes' );

/**
 * Meta Boxes.
 *
 * @return    void
 * @since     2.0
 */
function custom_meta_boxes() {

  $about_meta_box = array(
    'id'          => 'about_meta_box',
    'title'       => __( 'Parameters for "About" page', 'asana' ),
    'desc'        => '',
    'pages'       => array( 'page' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Additional info', 'asana' ),
        'id'          => 'tab_add_info',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'Enable "meet our team" block?', 'asana' ),
        'id'          => 'uni_meet_team_enable',
        'type'        => 'on-off',
        'desc'        => __( 'Show or hide "meet our team" block', 'asana' ),
        'std'         => 'on'
      ),
      array(
        'label'       => __( 'Title for "meet our team" block', 'asana' ),
        'id'          => 'uni_meet_team_title',
        'type'        => 'text',
        'desc'        => __( 'Add title for "meet our team" block', 'asana' ),
        'std'         => 'meet our team'
      ),
      array(
        'label'       => __( 'Enable Values section on "About" page?', 'asana' ),
        'id'          => 'uni_about_values_enable',
        'type'        => 'on-off',
        'desc'        => __( 'Show or hide Values section', 'asana' ),
        'std'         => 'on'
      ),
      array(
        'label'       => __( 'Title for Values section on "About" page', 'asana' ),
        'id'          => 'uni_about_values_title',
        'type'        => 'text',
        'desc'        => __( 'Add title for Values section. Default is "our values"', 'asana' ),
        'std'         => 'our values'
      ),
      array(
        'label'       => __( 'Enable Instagram block?', 'asana' ),
        'id'          => 'uni_instagram_enable',
        'type'        => 'on-off',
        'desc'        => __( 'Show or hide Instagram block', 'asana' ),
        'std'         => 'on'
      )
    )
  );

  $contact_meta_box = array(
    'id'          => 'contact_meta_box',
    'title'       => __( 'Parameters for "Contact" page', 'asana' ),
    'desc'        => '',
    'pages'       => array( 'page' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Additional info', 'asana' ),
        'id'          => 'tab_add_info',
        'type'        => 'tab'
      ),
      array(
        'label'       => '',
        'id'          => 'contact_textblock',
        'type'        => 'textblock',
        'desc'        => sprintf (__( 'A lot of options for this page is also located on <a href="%s">theme options page</a>.', 'asana' ), trailingslashit(home_url()).'wp-admin/themes.php?page=ot-theme-options#section_contact' ),
        'operator'    => 'and',
        'condition'   => ''
      ),
      array(
        'label'       => __( 'Contact page gallery', 'asana' ),
        'id'          => 'uni_gallery',
        'type'        => 'gallery',
        'desc'        => __( 'Images for gallery on "Contact" page.', 'asana' ),
        'condition'   => ''
      ),
      array(
        'label'       => __( 'Enable map?', 'asana' ),
        'id'          => 'uni_map_enable',
        'type'        => 'on-off',
        'desc'        => __( 'Show or hide map', 'asana' ),
        'std'         => 'on'
      ),
      array(
        'label'       => __( 'Enable contact form?', 'asana' ),
        'id'          => 'uni_form_enable',
        'type'        => 'on-off',
        'desc'        => __( 'Show or hide contact form', 'asana' ),
        'std'         => 'on'
      )
    )
  );

  $classes_meta_box = array(
    'id'          => 'classes_meta_box',
    'title'       => __( 'Parameters for "Classes" page', 'asana' ),
    'desc'        => '',
    'pages'       => array( 'page' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => '',
        'id'          => 'classes_textblock',
        'type'        => 'textblock',
        'desc'        => __( 'This page is designed to showcase your classes schedule (or for similar events). You may add page header image simply by adding featured image to this page. You should use shortcode like [uni-calendar id="X"] where X is the ID of your calendar. Attention: you must enable plugin Uni Events Calendars Manager to be able to create calendars and events for them!', 'asana' ),
        'operator'    => 'and',
        'condition'   => ''
      ),
    )
  );

  $events_meta_box = array(
    'id'          => 'events_meta_box',
    'title'       => __( 'Parameters for "Events" page', 'asana' ),
    'desc'        => '',
    'pages'       => array( 'page' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => '',
        'id'          => 'events_textblock',
        'type'        => 'textblock',
        'desc'        => sprintf (__( 'This page is designed to display your events. Events are just like blog posts, but themed) Also you can add page headers image for this page on <a href="%s">theme options page</a>.', 'asana' ), trailingslashit(home_url()).'wp-admin/themes.php?page=ot-theme-options#section_shop' ),
        'operator'    => 'and',
        'condition'   => ''
      ),
      array(
        'label'       => __( 'Show/hide list of categories.', 'asana' ),
        'id'          => 'events_display_list_cats',
        'type'        => 'on-off',
        'desc'        => __( 'You can decide whether to show or not on this page a small and nice dropdown-like list of all categories of events.', 'asana' ),
        'std'         => 'on'
      ),
      array(
        'id'          => 'events_categories',
        'label'       => __( 'Display events from categories:', 'asana' ),
        'desc'        => __( 'Use this option if you want to show events from certain categories only. If you don\'t choose any category, than all events will be shown on the page.', 'asana' ),
        'std'         => '',
        'type'        => 'taxonomy-checkbox',
        'section'     => 'general',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => 'uni_event_cat',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
    )
  );

  $slider_meta_box = array(
    'id'          => 'slider_meta_box',
    'title'       => __( 'Parameters for Home Page Slide', 'asana' ),
    'desc'        => '',
    'pages'       => array( 'uni_home_slides' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Additional info', 'asana' ),
        'id'          => 'tab_add_info',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'URI for "learn more" button', 'asana' ),
        'id'          => 'uni_slide_uri',
        'type'        => 'text',
        'desc'        => __( 'If you don\'t define an URI for this button, it won\'t be shown for this slider', 'asana' )
      ),
      array(
        'label'       => __( 'Label for "learn more" button', 'asana' ),
        'id'          => 'uni_slide_label',
        'type'        => 'text',
        'std'         => 'learn more',
        'desc'        => ''
      ),
      array(
        'id'          => 'uni_button_a_colour',
        'label'       => __( 'Colour for label of "learn more" button', 'asana' ),
        'desc'        => '',
        'std'         => '#ffffff',
        'type'        => 'colorpicker',
        'section'     => 'tab_colours',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'uni_button_a_bg',
        'label'       => __( 'Background colour for "learn more" button', 'asana' ),
        'desc'        => '',
        'std'         => '#168cb9',
        'type'        => 'colorpicker',
        'section'     => 'tab_colours',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'uni_button_a_bg_hover',
        'label'       => __( 'Background colour of hovered state for "learn more" button', 'asana' ),
        'desc'        => '',
        'std'         => '#1b9fd2',
        'type'        => 'colorpicker',
        'section'     => 'tab_colours',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      )
    )
  );

  $event_meta_box = array(
    'id'          => 'event_meta_box',
    'title'       => __( 'Parameters for Event', 'asana' ),
    'desc'        => '',
    'pages'       => array( 'uni_event' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Additional info', 'asana' ),
        'id'          => 'tab_add_info',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'Page header image', 'asana' ),
        'id'          => 'uni_event_page_header_image',
        'type'        => 'gallery',
        'desc'        => __( 'Add image to page header. Nevertheless you can add more then one image, only the first one will be used!', 'asana' ),
        'condition'   => ''
      ),
      array(
        'label'       => __( 'Date of the event', 'asana' ),
        'id'          => 'uni_event_date',
        'type'        => 'text',
        'desc'        => __( 'Add the date of the event', 'asana' )
      ),
      array(
        'label'       => __( 'Time of the event', 'asana' ),
        'id'          => 'uni_event_time',
        'type'        => 'text',
        'desc'        => __( 'Add the time of the event', 'asana' )
      ),
      array(
        'label'       => __( 'Address of the event', 'asana' ),
        'id'          => 'uni_event_address',
        'type'        => 'text',
        'desc'        => __( 'Add the address of the event', 'asana' )
      ),
      array(
        'label'       => __( 'Coordinates for the map', 'asana' ),
        'id'          => 'uni_event_coord',
        'type'        => 'text',
        'desc'        => __( 'Add the coordinates of the event. Example: "41.404182,2.199451"', 'asana' )
      ),
      array(
        'label'       => __( 'Zoom', 'asana' ),
        'id'          => 'uni_event_zoom',
        'type'        => 'text',
        'desc'        => __( 'Define zoom level. Example: "12"', 'asana' )
      ),
      array(
        'label'       => __( 'Price of a ticket', 'asana' ),
        'id'          => 'uni_event_price',
        'type'        => 'text',
        'desc'        => __( 'Add price for ticket for the event. Examples: "$10" or "Free of charge"', 'asana' )
      ),
      array(
        'label'       => __( 'Enable/disable "join event" functionality for this event only!', 'asana' ),
        'id'          => 'uni_local_events_join_on',
        'type'        => 'on-off',
        'desc'        => __( 'This option allows you to override "join event" global option.', 'asana' ),
        'std'         => 'off'
      ),
      array(
        'label'       => __( 'Custom text for the "join event" button and title for modal window connected with this button', 'asana' ),
        'id'          => 'uni_local_events_button_text',
        'type'        => 'text',
        'desc'        => __( 'Default is "Join event"', 'asana' ),
        'std'         => __( 'Join event', 'asana' )
      ),
      array(
        'id'          => 'uni_local_events_page',
        'label'       => __( 'Events page', 'asana' ),
        'desc'        => __( 'This option allows you to override "Events page" global option. An URI to the page chosen here will be added to the link on this page only.', 'asana' ),
        'std'         => '',
        'type'        => 'custom-post-type-select',
        'section'     => 'general',
        'rows'        => '',
        'post_type'   => 'page',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
    )
  );

  $wishlist_meta_box = array(
    'id'          => 'wishlist_meta_box',
    'title'       => __( 'Parameters for Wishlist', 'asana' ),
    'desc'        => '',
    'pages'       => array( 'page' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Additional info', 'asana' ),
        'id'          => 'tab_add_info',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'Page header image', 'asana' ),
        'id'          => 'uni_wishlist_page_header_image',
        'type'        => 'gallery',
        'desc'        => __( 'Add image to page header. Nevertheless you can add more then one image, only the first one will be used!', 'asana' ),
        'condition'   => ''
      )
    )
  );

  $bridallist_meta_box = array(
    'id'          => 'bridallist_meta_box',
    'title'       => __( 'Parameters for Bridal list', 'asana' ),
    'desc'        => '',
    'pages'       => array( 'page' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Additional info', 'asana' ),
        'id'          => 'tab_add_info',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'Page header image', 'asana' ),
        'id'          => 'uni_bridallist_page_header_image',
        'type'        => 'gallery',
        'desc'        => __( 'Add image to page header. Nevertheless you can add more then one image, only the first one will be used!', 'asana' ),
        'condition'   => ''
      )
    )
  );

  $price_meta_box = array(
    'id'          => 'price_meta_box',
    'title'       => __( 'Parameters for Price', 'asana' ),
    'desc'        => '',
    'pages'       => array( 'uni_price' ),
    'context'     => 'normal',
    'priority'    => 'high',
    'fields'      => array(
      array(
        'label'       => __( 'Additional info', 'asana' ),
        'id'          => 'tab_add_info',
        'type'        => 'tab'
      ),
      array(
        'label'       => __( 'Price value', 'asana' ),
        'id'          => 'uni_price_val',
        'type'        => 'text',
        'desc'        => ''
      ),
      array(
        'label'       => __( 'Currency sign', 'asana' ),
        'id'          => 'uni_currency',
        'type'        => 'text',
        'desc'        => ''
      ),
      array(
        'label'       => __( 'for... ?', 'asana' ),
        'id'          => 'uni_period',
        'type'        => 'text',
        'desc'        => __('for example: "for 7 days", "for 1 year" etc.', 'asana')
      ),
      array(
        'label'       => __( 'Custom text for "Order Now" button', 'asana' ),
        'id'          => 'uni_order_button_text',
        'type'        => 'text',
        'desc'        => ''
      ),
      array(
        'label'       => __( 'Enable/Disable external URI for "Order Now" button', 'asana' ),
        'id'          => 'uni_order_button_ext_url_enable',
        'type'        => 'on-off',
        'desc'        => __( 'By default "Order Now" button opens a modal window with a simple ordering form. It sends information only, no payments. However, you can enable external URI for this button if you want, for instance, redirect your clients to PayPal or similar. Please, don\'t forget to add the URI, otherwise you won\'t see this button at all!', 'asana' ),
        'std'         => 'off'
      ),
      array(
        'label'       => __( 'Custom URI for "Order Now" button', 'asana' ),
        'id'          => 'uni_order_button_uri',
        'type'        => 'text',
        'desc'        => '',
        'condition'   => 'uni_order_button_ext_url_enable:is(on)'
      )
    )
  );

  /**
   * Register our meta boxes using the
   * ot_register_meta_box() function.
   */
  if ( function_exists( 'ot_register_meta_box' ) )
    ot_register_meta_box( $about_meta_box );
    ot_register_meta_box( $contact_meta_box );
    ot_register_meta_box( $classes_meta_box );
    ot_register_meta_box( $events_meta_box );
    ot_register_meta_box( $slider_meta_box );
    ot_register_meta_box( $event_meta_box );
    ot_register_meta_box( $wishlist_meta_box );
    ot_register_meta_box( $bridallist_meta_box );
    ot_register_meta_box( $price_meta_box );
}