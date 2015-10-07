jQuery( document ).ready( function( $ ) {
    'use strict';

    var $page_template = $('#page_template'),
        $about_metabox = $('#about_meta_box'),
        $contact_metabox = $('#contact_meta_box'),
        $classes_metabox = $('#classes_meta_box'),
        $events_metabox = $('#events_meta_box'),
        $wishlist_metabox = $('#wishlist_meta_box'),
        $bridallist_metabox = $('#bridallist_meta_box');

    $about_metabox.hide();
    $contact_metabox.hide();
    $classes_metabox.hide();
    $events_metabox.hide();
    $wishlist_metabox.hide();
    $bridallist_metabox.hide();

    $page_template.on("change", function() {
        if ( $(this).val() == 'templ-about.php' ) {
            $about_metabox.show();
            $contact_metabox.hide();
            $classes_metabox.hide();
            $events_metabox.hide();
            $wishlist_metabox.hide();
            $bridallist_metabox.hide();
        } else if ( $(this).val() == 'templ-contact.php' ) {
            $contact_metabox.show();
            $about_metabox.hide();
            $classes_metabox.hide();
            $events_metabox.hide();
            $wishlist_metabox.hide();
            $bridallist_metabox.hide();
        } else if ( $(this).val() == 'templ-classes.php' ) {
            $contact_metabox.hide();
            $about_metabox.hide();
            $classes_metabox.show();
            $events_metabox.hide();
            $wishlist_metabox.hide();
            $bridallist_metabox.hide();
        } else if ( $(this).val() == 'templ-events.php' ) {
            $contact_metabox.hide();
            $about_metabox.hide();
            $classes_metabox.hide();
            $events_metabox.show();
            $wishlist_metabox.hide();
            $bridallist_metabox.hide();
        } else if ( $(this).val() == 'templ-wishlist.php' ) {
            $contact_metabox.hide();
            $about_metabox.hide();
            $classes_metabox.hide();
            $events_metabox.hide();
            $wishlist_metabox.show();
            $bridallist_metabox.hide();
        } else if ( $(this).val() == 'templ-bridallist.php' ) {
            $contact_metabox.hide();
            $about_metabox.hide();
            $classes_metabox.hide();
            $events_metabox.hide();
            $wishlist_metabox.hide();
            $bridallist_metabox.show();
        } else {
            $about_metabox.hide();
            $contact_metabox.hide();
            $classes_metabox.hide();
            $events_metabox.hide();
            $wishlist_metabox.hide();
            $bridallist_metabox.hide();
        }
    }).change();

});