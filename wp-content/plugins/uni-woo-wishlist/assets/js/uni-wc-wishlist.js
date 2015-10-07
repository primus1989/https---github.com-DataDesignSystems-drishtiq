jQuery( document ).ready( function( $ ) {
    'use strict';

    if ( uniwcwishlist.bridal_enabled ) {
        uni_wc_wishbridallist_init();
        $( 'body' ).on( "change", ".variation_id", uni_wc_wishbridallist_init );
    } else {
        uni_wc_wishlist_init();
        $( 'body' ).on( "change", ".variation_id", uni_wc_wishlist_init );
    }

    // uni_wc_wishbridallist_init
    function uni_wc_wishbridallist_init(){
        if ( $(".variation_id").length > 0 ) {
            var container = $(".uni-wishlist-link-container"),
                bridal_container = $(".uni-bridallist-link-container"),
                product_id = $("input[name=product_id]").val(),
                variation_id = $("input[name=variation_id]").val();

                if ( variation_id ) {

                var sendData = {
                            action: 'uni_wc_wishlist_variation_chosen',
                            pid: product_id,
                            vid: variation_id
                            }

			    $.ajax({
				    type: 'post',
	        	    url: uniwcwishlist.ajax_url,
	        	    data: sendData,
	        	    dataType: 'json',
                    beforeSend: function(response) {
	        	        container.block({
	        	            message: null,
                            overlayCSS: { background: '#fff url(' + uniwcwishlist.loader + ') no-repeat center', backgroundSize: '80px 10px', opacity: 0.6 }
                        });
	        	        bridal_container.block({
	        	            message: null,
                            overlayCSS: { background: '#fff url(' + uniwcwishlist.loader + ') no-repeat center', backgroundSize: '80px 10px', opacity: 0.6 }
                        });
                    },
	        	    success: function(response) {
	        	        //console.log(response);
	        		    if ( response.status == "success" ) {
	        		        container.unblock();
                            container.empty().html(response.output);
	        		        bridal_container.unblock();
                            bridal_container.empty().html(response.output_bridal);
	        		    } else if ( response.status == "error" ) {
	        		        container.unblock();
                            container.empty().html(response.message);
	        		        bridal_container.unblock();
                            bridal_container.empty();
	        		    }
	        	    },
	        	    error:function(response){
	        	        container.unblock();
                        bridal_container.unblock();
	        	    }
	            });

                }

        }
    }

    // uni_wc_wishlist_init
    function uni_wc_wishlist_init(){
        if ( $(".variation_id").length > 0 ) {
            var container = $(".uni-wishlist-link-container"),
                product_id = $("input[name=product_id]").val(),
                variation_id = $("input[name=variation_id]").val();

                if ( variation_id ) {

                var sendData = {
                            action: 'uni_wc_wishlist_variation_chosen',
                            pid: product_id,
                            vid: variation_id
                            }

			    $.ajax({
				    type: 'post',
	        	    url: uniwcwishlist.ajax_url,
	        	    data: sendData,
	        	    dataType: 'json',
                    beforeSend: function(response) {
	        	        container.block({
	        	            message: null,
                            overlayCSS: { background: '#fff url(' + uniwcwishlist.loader + ') no-repeat center', backgroundSize: '80px 10px', opacity: 0.6 }
                        });
                    },
	        	    success: function(response) {
	        	        //console.log(response);
	        		    if ( response.status == "success" ) {
	        		        container.unblock();
                            container.empty().html(response.output);
	        		    } else if ( response.status == "error" ) {
	        		        container.unblock();
                            container.empty().html(response.message);
	        		    }
	        	    },
	        	    error:function(response){
	        	        container.unblock();
	        	    }
	            });

                }

        }
    }

    // add/delete product to/from wish and bridal lists
	$("body").on("click", ".uni-wishlist-link-container a, .uni-bridallist-link-container a, .uni-wishlist-table-remove-link", function (e) {
	    e.preventDefault();
        var $this_link = $(this);
        if ( $(".uni-wishlist-link-container").length > 0 || $(".uni-bridallist-link-container").length > 0 ) {
            var container = $(this).parent();
        } else {
            var container = $(this).closest(".uni-wishlist-table-row");
        }

                var sendData = $this_link.data();
                    sendData.cheaters_always_disable_js = 'true_bro';

			    $.ajax({
				    type: 'post',
	        	    url: uniwcwishlist.ajax_url,
	        	    data: sendData,
	        	    dataType: 'json',
                    beforeSend: function(response) {
	        	        container.block({
	        	            message: null,
                            overlayCSS: { background: '#fff url(' + uniwcwishlist.loader + ') no-repeat center', backgroundSize: '80px 10px', opacity: 0.6 }
                        });
                    },
	        	    success: function(response) {
	        	        //console.log(response);
	        		    if ( response.status == "success" ) {
	        		        container.unblock();
                            if ( $(".uni-wishlist-link-container").length > 0 || $(".uni-bridallist-link-container").length > 0 ) {
                                container.empty().html(response.output);
                            } else {
                                container.remove();
                            }

                            $( 'body' ).trigger( 'uni_wishlist_items_changed' );

                            if ( response.redirect.length > 0 ) {
						        setTimeout(function() {
							        window.location.replace( response.redirect );
						        }, 500);
                            }
	        		    } else if ( response.status == "error" ) {
	        		        container.unblock();
                            if ( $(".uni-wishlist-link-container").length > 0 || $(".uni-bridallist-link-container").length > 0 ) {
                                container.empty().html(response.message);
                            } else {
                                container.remove().html('<td>'+response.message+'</td>');
                            }

                            $( 'body' ).trigger( 'uni_wishlist_items_not_changed' );
	        		    }
	        	    },
	        	    error:function(response){
	        	        container.unblock();
                        container.empty().html(response.message);
                        //uni_popup_message("Error!", "warning");
	        	    }
	            });

     });

    // for different ajax actions
	$("body").on("click", ".uni_wishlist_ajax_link", function (e) {
	    e.preventDefault();
        var $this_link = $(this),
            container = $(this).parent();

                var sendData = $this_link.data();
                    sendData.cheaters_always_disable_js = 'true_bro';

			    $.ajax({
				    type: 'post',
	        	    url: uniwcwishlist.ajax_url,
	        	    data: sendData,
	        	    dataType: 'json',
                    beforeSend: function(response) {
	        	        container.block({
	        	            message: null,
                            overlayCSS: { background: '#fff url(' + uniwcwishlist.loader + ') no-repeat center', backgroundSize: '80px 10px', opacity: 0.6 }
                        });
                    },
	        	    success: function(response) {
	        	        //console.log(response);
	        		    if ( response.status == "success" ) {
	        		        container.unblock();

                            if ( response.redirect.length > 0 ) {
						        setTimeout(function() {
							        window.location.replace( response.redirect );
						        }, 500);
                            }
	        		    } else if ( response.status == "error" ) {
	        		        container.unblock();
	        		    }
	        	    },
	        	    error:function(response){
	        	        container.unblock();
	        	    }
	            });

     });

    //*********************

    if ( $('.uni-bridallist-editable').length > 0 ) {
        $(".uni-bridallist-editable").each(function() {
            var container = $(this),
                input_id = container.data("id");

            $(this).editable(uniwcwishlist.ajax_url, {
                submitdata : function(value, settings) {
                    return {
                        action: "uni_wc_wishlist_bridal_title_inline_edit",
                        uid: settings.id
                        };
                },
                type        : 'text',
                indicator   : uniwcwishlist.indicator_text,
                tooltip     : uniwcwishlist.tooltip_text,
                cancel      : uniwcwishlist.cancel_text,
                submit      : uniwcwishlist.save_text,
                id          : input_id
            });

        });
    }

});