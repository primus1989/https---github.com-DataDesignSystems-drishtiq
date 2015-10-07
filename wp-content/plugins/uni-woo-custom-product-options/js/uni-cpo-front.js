window.Parsley.setLocale(unicpo.locale);

jQuery( document ).ready( function( $ ) {
    'use strict';

    uni_ajax_calculation();
    $( 'body' ).on( "change", ".uni-cpo-input, .uni-cpo-select, .uni-cpo-checkbox, .uni-cpo-radio, .uni-cpo-textarea, .uni-cpo-color, .qty", uni_ajax_calculation );

    // ajax calculation
    function uni_ajax_calculation(){

            var item_id = $(".uni_cpo_product_id").val();
            var fields = {};
            var uni_qty = '';
            var all_valid;
            var uniValid = true;
            //var price_tag = $(".main-item-wrapper .text .price > span");
            if ( unicpo.price_selector ) {
                var price_tag = $(unicpo.price_selector);
            } else {
                var price_tag = $(".summary.entry-summary .price span");
            }

            if ( unicpo.hide_price == true ) {
                price_tag.html(unicpo.zero_price+' '+unicpo.text_after_zero_price);
            }

            $(".uni-cpo-input, .uni-cpo-select, .uni-cpo-checkbox, .uni-cpo-radio, .uni-cpo-textarea, .uni-cpo-color, .qty").each(function() {
                fields["action"] = 'uni_cpo_calculate_price_ajax';
                fields["uni_cpo_product_id"] = item_id;
                fields["uni_quantity"] = $(".qty").val();
                uni_qty = fields["uni_quantity"];

                if ( this.type == 'checkbox' && !$(this).data('multiple') ) {
                    if ( $(this).prop('checked') == true ) {
                        fields[this.name] = $(this).val();
                    } else if ( $(this).prop('checked') != true ) {
                        fields[this.name] = '';
                    }
                } else if ( this.type == 'checkbox' && $(this).data('multiple') == 'yes' ) {
                    var checkboxes = [];
                    var name_of_checkes = this.name.slice(0,-2);
                    $("input[name="+name_of_checkes+"\\[\\]]:checked").each(function() {
                        checkboxes.push( $(this).val() );
                    });
                    fields[name_of_checkes] = checkboxes;
                } else if ( this.type == 'radio' ) {
                    if ($('input[name='+ this.name +']:checked').length) {
                        if ( $(this).prop('checked') == true ) {
                            fields[this.name] = $(this).val();
                        }
                    } else {
                        fields[this.name] = '';
                    }
                } else if ( this.type == 'text' ) {
                    if ( uniIsNumber($(this).val()) == false ) {
                        var n = $(this).val().replace(/,/,".");
                        $(this).val(n);
                        fields[this.name] = $(this).val();
                    } else {
                        fields[this.name] = $(this).val();
                    }
                } else {
                    fields[this.name] = $(this).val();
                }


                all_valid = $(this).parsley({excluded: '[disabled]'}).validate();
                if ( all_valid != true && all_valid.length > 0 ) {
                    uniValid = false;
                }
            });
            //console.log(fields);

            if ( fields["uni_cpo_product_id"] && uniValid ) {
			    $.ajax({
				    type:'post',
	        	    url: unicpo.ajax_url,
	        	    data: fields,
	        	    dataType: 'json',
                    beforeSend: function(){
                        $(".uni-cpo-total").remove();
                        price_tag.empty().html("<span class='uni-cpo-preloader'></span>");
                    },
	        	    success: function(response) {
	        		    if (response.status == 'success') {
                            price_tag.empty().html(response.message);
                            $(".uni-cpo-total").remove();
                            $(".single_add_to_cart_button").after('<div class="uni-cpo-total">'+unicpo.total_text_start+' '+uni_qty+' '+unicpo.total_text_end+' <b>'+response.total+'</b></div>');
		        	    } else if (response.status == 'error') {
		        		    console.log(response);
		        	    }
	        	    },
	        	    error:function(response){
	        	        if (response.status !== 'success') {
	        	    	    console.log('Error');
					    }
	        	    }
	            });
                return false;
            }
	}

    $("body").on("click", ".single_add_to_cart_button", function(e){
        e.preventDefault();
        var this_form = $(this).closest("form");
        var all_valid;
        var uniValid = true;

        $(".uni-cpo-input, .uni-cpo-select, .uni-cpo-checkbox, .uni-cpo-radio, .uni-cpo-textarea, .uni-cpo-color").each(function() {
                all_valid = $(this).parsley({excluded: '[disabled]'}).validate();
                if ( all_valid != true && all_valid.length > 0 ) {
                    uniValid = false;
                }
        });

        if ( uniValid ) {
            this_form.submit();
        }

    });

});

function uniIsNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

jQuery.fn.numVal = function() {
        return parseFloat(this.val()) || 0;
}