jQuery( document ).ready( function( $ ) {
    'use strict';

    // meta-boxes-product.js
	// Initial order
	var woocommerce_attribute_items = $('.uni_cpo_product_attributes').find('.woocommerce_attribute').get();

	woocommerce_attribute_items.sort(function(a, b) {
	   var compA = parseInt($(a).attr('rel'));
	   var compB = parseInt($(b).attr('rel'));
	   return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
	})
	$(woocommerce_attribute_items).each( function(idx, itm) { $('.uni_cpo_product_attributes').append(itm); } );

	function attribute_row_indexes() {
		$('.uni_cpo_product_attributes .woocommerce_attribute').each(function(index, el){
			$('.attribute_position', el).val( parseInt( $(el).index('.uni_cpo_product_attributes .woocommerce_attribute') ) );
		});
	};

	$('.uni_cpo_product_attributes .woocommerce_attribute').each(function(index, el){
		if ( $(el).css('display') != 'none' && $(el).is('.taxonomy') ) {
			$('select.uni_cpo_attribute_taxonomy').find('option[value="' + $(el).data( 'taxonomy' ) + '"]').attr('disabled','disabled');
		}
	});

	// Add rows
	$( 'button.uni_cpo_add_attribute' ).on('click', function(){
		var size         = $( '.uni_cpo_product_attributes .woocommerce_attribute' ).size();
		var attribute    = $( 'select.uni_cpo_attribute_taxonomy' ).val();
		var $wrapper     = $( this ).closest( '#uni_cpo_options_data' ).find( '.uni_cpo_product_attributes' );
		//var product_type = $( 'select#product-type' ).val();
		var data         = {
			action : 'uni_add_row_attribute',
			taxonomy : attribute,
			i : size,
			security : woocommerce_admin_meta_boxes.add_attribute_nonce
		};

		$wrapper.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });

		$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
			$wrapper.append( response );
            /*
			if ( product_type !== 'variable' ) {
				$wrapper.find( '.enable_variation' ).hide();
			}
            */
			$('body').trigger( 'wc-enhanced-select-init' );
			attribute_row_indexes();
			$wrapper.unblock();

			$('body').trigger( 'woocommerce_added_attribute' );
		});

		if ( attribute ) {
			$( 'select.uni_cpo_attribute_taxonomy' ).find( 'option[value="' + attribute + '"]' ).attr( 'disabled','disabled' );
			$( 'select.uni_cpo_attribute_taxonomy' ).val( '' );
		}

		return false;
	});

	$('.uni_cpo_product_attributes').on('blur', 'input.attribute_name', function(){
		$(this).closest('.woocommerce_attribute').find('strong.attribute_name').text( $(this).val() );
	});

	$('.uni_cpo_product_attributes').on('click', 'button.select_all_attributes', function(){
		$(this).closest('td').find('select option').attr("selected","selected");
		$(this).closest('td').find('select').change();
		return false;
	});

	$('.uni_cpo_product_attributes').on('click', 'button.select_no_attributes', function(){
		$(this).closest('td').find('select option').removeAttr("selected");
		$(this).closest('td').find('select').change();
		return false;
	});

	$('.uni_cpo_product_attributes').on('click', 'button.remove_row', function() {
		var answer = confirm(woocommerce_admin_meta_boxes.remove_attribute);
		if (answer){
			var $parent = $(this).parent().parent();

			if ($parent.is('.taxonomy')) {
				$parent.find('select, input[type=text]').val('');
				$parent.hide();
				$('select.uni_cpo_attribute_taxonomy').find('option[value="' + $parent.data( 'taxonomy' ) + '"]').removeAttr('disabled');
			} else {
				$parent.find('select, input[type=text]').val('');
				$parent.hide();
				attribute_row_indexes();
			}
		}
		return false;
	});

	// Attribute ordering
	$('.uni_cpo_product_attributes').sortable({
		items:'.woocommerce_attribute',
		cursor:'move',
		axis:'y',
		handle: 'h3',
		scrollSensitivity:40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start:function(event,ui){
			ui.item.css('background-color','#f6f6f6');
		},
		stop:function(event,ui){
			ui.item.removeAttr('style');
			attribute_row_indexes();
		}
	});

	// Save attributes and update variations
	$('.uni_cpo_save_attributes').on('click', function(){

		$('.uni_cpo_product_attributes').block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });

		var data = {
			post_id: 		woocommerce_admin_meta_boxes.post_id,
			data:			$('.uni_cpo_product_attributes').find('input, select, textarea').serialize(),
			action: 		'uni_cpo_save_attributes',
			security: 		woocommerce_admin_meta_boxes.save_attributes_nonce
		};

		$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

			var this_page = window.location.toString();

			this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + woocommerce_admin_meta_boxes.post_id + '&action=edit&' );

			// Load variations panel
			$('#variable_product_options').block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
			$('#variable_product_options').load( this_page + ' #variable_product_options_inner', function() {
				$('#variable_product_options').unblock();
			} );

			$('.uni_cpo_product_attributes').unblock();

		});

	});

    //
    $.fn.insertAtCaret = function (myValue) {
	    return this.each(function(){
			//IE support
			if (document.selection) {
					this.focus();
					sel = document.selection.createRange();
					sel.text = myValue;
					this.focus();
			}
			//MOZILLA / NETSCAPE support
			else if (this.selectionStart || this.selectionStart == '0') {
					var startPos = this.selectionStart;
					var endPos = this.selectionEnd;
					var scrollTop = this.scrollTop;
					this.value = this.value.substring(0, startPos)+ myValue+ this.value.substring(endPos,this.value.length);
					this.focus();
					this.selectionStart = startPos + myValue.length;
					this.selectionEnd = startPos + myValue.length;
					this.scrollTop = scrollTop;
			} else {
					this.value += myValue;
					this.focus();
			}
	    });
    };

	$('#cpo-options-elements-list li').click(function() {
		$("#_uni_cpo_formula").insertAtCaret($(this).text());
		return false
	});

/*
based on Tristan Denyer code

Author: Tristan Denyer (based on Charlie Griefer's original clone code, and some great help from Dan - see his comments in blog post)
Plugin repo: https://github.com/tristandenyer/Clone-section-of-form-using-jQuery
Demo at http://tristandenyer.com/using-jquery-to-duplicate-a-section-of-a-form-maintaining-accessibility/
Ver: 0.9.4.1
Last updated: Sep 24, 2014

The MIT License (MIT)

Copyright (c) 2011 Tristan Denyer

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
    $('#uniBtnAdd').on("click", function () {

        var num     = $('.uniClonedInput').length, // Checks to see how many "duplicatable" input fields we currently have
            newNum  = new Number(num + 1),      // The numeric ID of the new input field being added, increasing by 1 each time
            newElem = $('#uni_discount_rule' + num).clone().attr('id', 'uni_discount_rule' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value

    /*  This is where we manipulate the name/id values of the input inside the new, cloned element
        Below are examples of what forms elements you can clone, but not the only ones.
        There are 2 basic structures below: one for an H2, and one for form elements.
        To make more, you can copy the one for form elements and simply update the classes for its label and input.
        Keep in mind that the .val() method is what clears the element when it gets cloned. Radio and checkboxes need .val([]) instead of .val('').
    */

        newElem.find('.label-uni-min-qty').attr('for', 'uni-cpo-min-qty-input'+ newNum);
        newElem.find('.input-uni-min-qty').attr('id', 'uni-cpo-min-qty-input'+ newNum).attr('name', 'uni_cpo_min_qty' + newNum).val('');

        newElem.find('.label-uni-max-qty').attr('for', 'uni-cpo-max-qty-input'+ newNum);
        newElem.find('.input-uni-max-qty').attr('id', 'uni-cpo-max-qty-input'+ newNum).attr('name', 'uni_cpo_max_qty' + newNum).val('');

        newElem.find('.label-uni-discount').attr('for', 'uni-cpo-discount-input'+ newNum);
        newElem.find('.input-uni-discount').attr('id', 'uni-cpo-discount-input'+ newNum).attr('name', 'uni_cpo_discount' + newNum).val('');

    // Insert the new element after the last "duplicatable" input field
        $('#uni_discount_rule' + num).after(newElem);

    // Enable the "remove" button. This only shows once you have a duplicated section.
        $('#uniBtnDel').attr('disabled', false);

    // Right now you can only add 4 sections, for a total of 5. Change '5' below to the max number of sections you want to allow.
        if (newNum == 35)
        $('#uniBtnAdd').attr('disabled', true).prop('value', "You've reached the limit"); // value here updates the text in the 'add' button when the limit is reached
    });

    $('#uniBtnDel').on("click", function () {
    // Confirmation dialog box. Works on all desktop browsers and iPhone.
        if ( confirm("Are you sure you wish to remove the last section? This cannot be undone.") )
            {
                var num = $('.uniClonedInput').length;
                // how many "duplicatable" input fields we currently have
                $('#uni_discount_rule' + num).slideUp('slow', function () {$(this).remove();
                // if only one element remains, disable the "remove" button
                    if (num -1 === 1)
                $('#uniBtnDel').attr('disabled', true);
                // enable the "add" button
                $('#uniBtnAdd').attr('disabled', false).prop('value', "add new");});
            }
        return false; // Removes the last section you added
    });
    // Enable the "add" button
    $('#uniBtnAdd').attr('disabled', false);
    // Disable the "remove" button
    $('#uniBtnDel').attr('disabled', true);



    // clone fields for conditions
    $('#uni-clone-condition').on("click", function () {

        var num     = $('.uni-cloned-conditions').length,
            newNum  = new Number(num + 1),
            newElem = $('#conditions_field' + num).clone().attr('id', 'conditions_field' + newNum).fadeIn('slow');

        newElem.find('.uni-condition-left-var').attr('for', 'uni_condition_left_var'+ newNum);
        newElem.find('.select-uni-condition-left-var').attr('id', 'uni_condition_left_var'+ newNum).attr('name', 'uni_condition_left_var' + newNum).val('');
        newElem.find('.select-uni-condition-left-operator').attr('id', 'uni_condition_left_operator'+ newNum).attr('name', 'uni_condition_left_operator' + newNum).val('');
        newElem.find('.input-uni-condition-left-value').attr('id', 'uni_condition_left_value'+ newNum).attr('name', 'uni_condition_left_value' + newNum).val('');

        newElem.find('.select-uni-condition-conj-operator').attr('id', 'uni_condition_conj_operator'+ newNum).attr('name', 'uni_condition_conj_operator' + newNum).val('');

        newElem.find('.select-uni-condition-right-var').attr('id', 'uni_condition_right_var'+ newNum).attr('name', 'uni_condition_right_var' + newNum).val('');
        newElem.find('.select-uni-condition-right-operator').attr('id', 'uni_condition_right_operator'+ newNum).attr('name', 'uni_condition_right_operator' + newNum).val('');
        newElem.find('.input-uni-condition-right-value').attr('id', 'uni_condition_right_value'+ newNum).attr('name', 'uni_condition_right_value' + newNum).val('');

        newElem.find('.textarea-uni-condition-formula').attr('id', 'uni_condition_formula'+ newNum).attr('name', 'uni_condition_formula' + newNum).val('');

        $('#conditions_field' + num).after(newElem);

        $('#uni-delete-condition').attr('disabled', false);

        if (newNum == 35)
        $('#uni-clone-condition').attr('disabled', true).prop('value', "You've reached the limit");
    });

    $('#uni-delete-condition').on("click", function () {

        if ( confirm("Are you sure you wish to remove the last section? This cannot be undone.") )
            {
                var num = $('.uni-cloned-conditions').length;

                $('#conditions_field' + num).slideUp('slow', function () {$(this).remove();
                // if only one element remains, disable the "remove" button
                    if (num -1 === 1)
                $('#uni-delete-condition').attr('disabled', true);
                // enable the "add" button
                $('#uni-clone-condition').attr('disabled', false).prop('value', "add new");});
            }
        return false; // Removes the last section you added
    });
    // Enable the "add" button
    $('#uni-clone-condition').attr('disabled', false);
    // Disable the "remove" button
    $('#uni-delete-condition').attr('disabled', true);

    //
    uni_set_proper_validator();
    $( 'body' ).on( "change", ".select-uni-condition-left-operator, .select-uni-condition-right-operator", uni_set_proper_validator );

    function uni_set_proper_validator(){
    if ( $(".select-uni-condition-left-operator").length > 0 ) {
        $(".select-uni-condition-left-operator, .select-uni-condition-right-operator").each(function() {
          var inp = $(this).closest('span').find(".input-uni-condition-value");
            if ( $(this).val() == 'is' || $(this).val() == 'isnot' ) {
                inp.removeClass("wc_input_decimal");
            } else {
                inp.addClass("wc_input_decimal");
            }
        });
    }
    }

});

jQuery.fn.numVal = function() {
        return parseFloat( this.val() ) || 0;
}