window.ParsleyValidator.setLocale(unithemeparams.locale);

//message
var uni_popup_message = function(text, type) {
  var message_div = jQuery('#uni_popup');
      message_text = message_div.text(text);

  if (type == 'success') {
    message_div.addClass('success-message');
  }
  if (type == 'warning') {
    message_div.addClass('warning-message');
  }
  if (type == 'error') {
    message_div.addClass('error-message');
  }

  if (text != '') {
    message_div.fadeIn(400).dequeue().animate({ left: 25 }, 250, function(){
      setTimeout(function(){
        message_div.animate({ left: -125 }, 250).dequeue().fadeOut(400, function(){

            setTimeout(function(){
              message_div.removeClass('success-message warning-message error-message');
            }, 1);
        });
      }, 3000);
    });
  }
};

jQuery( document ).ready( function( $ ) {
    'use strict';

	//// ---> Check issue element
	jQuery.fn.exists = function() {
	  return jQuery(this).length;
	}

	// global vars
	var winWidth = $(window).width();
	var winHeight = $(window).height();
	var slideDescMtop = (($(".slideDesc").height() / 2 ) + 8) * -1;

	if($('.homeBxSlider').exists()){
		$('.homeBxSliderWrap').css({
		   	'height': winHeight
		});
		$('.homeBxSlider').css({
		   	'height': winHeight
		});
		$('.homeBxSlider .slide').css({
		   	'height': winHeight
		});
		$('.homeBxSlider .slide .slideDesc').css({
		   	'margin-top': slideDescMtop
		});

		$(window).resize( function(e)
		{
		    var winWidth = $(window).width();
			var winHeight = $(window).height();
			$('.homeBxSliderWrap').css({
			   	'height': winHeight
			});
			$('.homeBxSlider').css({
			   	'height': winHeight
			});
			$('.homeBxSlider .slide').css({
			   	'height': winHeight
			});
		});
	}

	
	if($('.homeBxSlider').exists()){
		var homeSlide = $('.homeBxSlider').bxSlider({
			mode:"fade",
			auto:true,
			speed:700,
			pause:4000,
			onSlideAfter: function(){
				var gcs = homeSlide.getCurrentSlide();
		        $('.homeBxSlider').find('.slide:not(li[data-slide="'+gcs+'"])').removeClass("active");
				$('.homeBxSlider').find('.slide[data-slide="'+gcs+'"]').addClass("active");
				
		    }
		});
	}

	if($('.contactGallery').exists()){
		var contactGallery = $('.contactGallery').find("ul").bxSlider({
			auto:true,
			controls:false
		});
	}

	$("#joinEventBtn").fancybox({
		wrapCSS    : 'eventRegistrationFancyboxPopup',
		helpers : {
	        overlay : {
	            css : {
	                'background' : 'rgba(255, 255, 255, 0.94)'
	            }
	        }
	    }
	});

    $(".membershipCardOrder").on("click", function (e) {
            e.preventDefault();

            var price_id = $(this).data("priceid"),
                price_title = $(this).data("pricetitle");

            $.fancybox.open('#membershipCardOrderPopup', {
		        wrapCSS    : 'eventRegistrationFancyboxPopup',
		        helpers : {
			        overlay : {
				        css : {
					        'background' : 'rgba(255, 255, 255, 0.94)'
					        }
				    }
		        },
                beforeShow: function() {
                    $("#uni_price_title").empty().text(price_title);
                    $("input[name=uni_price_id]").val(price_id);
                }
            });
    });

	if(!$('.single-uni_event .nextEventBox').exists()){
		$("body.single-uni_event").addClass("single-uni_event_last");
	}

    $(".teamItem").on("click", function(){
    	var userDescId = $(this).data("userid");
    	$("#"+userDescId).addClass("show");
    });


	$(".closeTeamDesc").on("click", function(){
    	$(this).closest(".teamItemDesc").removeClass("show");
    });
    
	$(".miniCart").on("click", function(){
		$(this).closest(".contentWrap").addClass("showMiniCart");
	});
	$(".closeCartPopup").on("click", function(){
		$(this).closest(".contentWrap").removeClass("showMiniCart");
	});

	$('.country_to_state, .options select, .uni_cpo_fields_container select').selectric();

	$(".galleryThumbItem").on("click", function(e){
		e.preventDefault();
		if (!$(this).hasClass("active")) {
			$("a.galleryThumbItem.active").removeClass("active");
			$(this).addClass("active");
			var imgID = $(this).attr("href");
			$(".productGalleryWrap .current").removeClass("current");
			$(imgID).addClass("current");	
		}
	});

	
	$(".categoryList span").on("click", function(){
		if ($(this).hasClass("clicked")) {
			$(this).removeClass("clicked").closest(".categoryList").find("ul").slideUp(300);
		} else {
			$(this).addClass("clicked").closest(".categoryList").find("ul").slideDown(300);
		}
	});

	$(document).on('click', function(e) {
	  if (!$(e.target).parents().hasClass('categoryList') /*&& !$(e.target).hasClass('miniCartWrap')*/ )  {
	    $(".categoryList").find("ul").slideUp(300);
		$(".categoryList span").removeClass("clicked");
	  }
	});

	$(".classesFilter a").on("click", function(e){
		e.preventDefault();
		var filterData = $(this).data("filter");
		if (filterData == "all") {
			$(".classesFilter a.active").removeClass("active");
			$(this).addClass("active");
			$(".fc-content-skeleton a.fc-event.hide").removeClass("hide");
		} else {
			$(".classesFilter a.active").removeClass("active");
			$(this).addClass("active");
			$(".fc-content-skeleton a.fc-event.hide").removeClass("hide");
			$(".fc-content-skeleton a.fc-event").not("."+filterData).addClass("hide");
		}
	});


	$('body').on('click', 'button.fc-button', function() {
		$(".classesFilter a.active").removeClass("active");
		$(".classesFilter a[data-filter='all']").addClass("active");
	});

	$('.showMobileMenu').on("click", function(e){
		e.preventDefault();
		$(this).toggleClass('open').closest("body").toggleClass('animated');
	});

	$(".cartPage .woocommerce td.product-name dl.variation").each(function(){
		var dlContainer = $(this).closest(".cartProduct").find("h4");
		$(this).appendTo(dlContainer);
	});	

	$(".miniCartItem dd, .page-template-templ-wishlist .variation dd").each(function(){
		$("<br>").insertAfter($(this));
	});	

	$(".page-template-templ-wishlist .uni-wishlist-variation-details dl.variation").each(function(){
		var dlContainer = $(this).closest(".uni-wishlist-item-details").find("h4.uni-wishlist-item-title");
		$(this).appendTo(dlContainer);
	});	

	$(".page-template-templ-wishlist .uni-wishlist-item-availability span").each(function(){
		var dlContainer = $(this).closest(".uni-wishlist-item-details").find("h4.uni-wishlist-item-title");
		$(this).appendTo(dlContainer);
	});	

	$(".single-product .woocommerce-breadcrumb").prependTo(".single-product .singleProductWrap");

	if ( $(".postItem p").length > 0 ) {
		$(".postItem p").dotdotdot({
			wrap: 'letter'	
		});
    }
    if ( $(".postItem h4").length > 0 ) {
		$(".postItem h4").dotdotdot({
			wrap: 'letter'	
		});
    }
    if ( $(".gridItemDesc p ").length > 0 ) {
		$(".gridItemDesc p ").dotdotdot({
			wrap: 'letter'	
		});
    }
    if ( $(".mainItemDesc p ").length > 0 ) {
		$(".mainItemDesc p ").dotdotdot({
			wrap: 'word'	
		});
    }
    if ( $(".gridItemDesc h3").length > 0 ) {
		$(".gridItemDesc h3").dotdotdot({
			wrap: 'letter'	
		});
    }
    if ( $(".mainItemDesc h3").length > 0 ) {
		$(".mainItemDesc h3").dotdotdot({
			wrap: 'letter'	
		});
    }
    if ( $(".membershipCardItem p").length > 0 ) {
		$(".membershipCardItem p").dotdotdot({
			wrap: 'letter'	
		});
    }

    if($('.teamItemDescWrap').exists()){
		$('.teamItemDescWrap').jScrollPane({
			autoReinitialise: true
		});
	}

	/* Sticky */
	var sticky_navigation_offset_top = 0;
	var sticky_navigation = function(){
		var scroll_top = $(window).scrollTop();
		if (scroll_top > sticky_navigation_offset_top) { 
			$('#header .headerWrap').addClass("is-sticky");
		} else {
			$('#header .headerWrap').removeClass("is-sticky"); 
		}
	};
	sticky_navigation();

	$(window).on('scroll', function() {
		 sticky_navigation();
	});
	/* END Sticky */
    
    if (winWidth > 767) {
		$('div[data-type="parallax"]').each(function(){
	        var $bgobj = $(this); // assigning the object
	        var bgobjTop = $(this).offset().top;
	    
	        $(window).scroll(function() {
	        	//console.log($(window).scrollTop() + winHeight)
	        	//console.log(bgobjTop)

				if ( ($(window).scrollTop() + winHeight) > bgobjTop )        	
				{

					var yPos = -(($(window).scrollTop() - $bgobj.offset().top) / $bgobj.data('speed')); 
	            
		            // Put together our final background position
		            var coords = '50% '+ yPos + 'px';

		            // Move the background
		            $bgobj.css({ backgroundPosition: coords });	
				}
	        }); 
	    });  
    }

    $(window).resize( function(e)
		{
		    var winWidth = $(window).width();
			var winHeight = $(window).height();

			if (winWidth > 767) {
				$('div[data-type="parallax"]').each(function(){
			        var $bgobj = $(this); // assigning the object
			        var bgobjTop = $(this).offset().top;
			    
			        $(window).scroll(function() {
			        	//console.log($(window).scrollTop() + winHeight)
			        	//console.log(bgobjTop)

						if ( ($(window).scrollTop() + winHeight) > bgobjTop )        	
						{

							var yPos = -(($(window).scrollTop() - $bgobj.offset().top) / $bgobj.data('speed')); 
			            
				            // Put together our final background position
				            var coords = '50% '+ yPos + 'px';

				            // Move the background
				            $bgobj.css({ backgroundPosition: coords });	
						}
			        }); 
			    });
		    }
	});

    $("body").on("click", ".uni_input_submit", function (e) {
        var submit_button = $(this),
            this_form = submit_button.closest("form");
        this_form.submit();
    });

    $("body").on("submit", ".uni_form", function (e) {

        var submit_button = $(this),
            this_form = submit_button.closest("form"),
            action = this_form.attr('action');
            //console.log(submit_button);
        var form_valid = this_form.parsley({excluded: '[disabled]'}).validate();

            if ( form_valid ) {
                var dataToSend = this_form.serialize();

			    $.ajax({
				    type: 'post',
	        	    url: action,
	        	    data: dataToSend + '&cheaters_always_disable_js=' + 'true_bro',
	        	    dataType: 'json',
	        	    beforeSend: function(){
	        	        this_form.block({
	        	            message: null,
                            overlayCSS: { background: '#fff', opacity: 0.6 }
                        });
	        	    },
	        	    success: function(response) {
	        		    if ( response.status == "success" ) {
                            this_form.unblock();
                            uni_popup_message(response.message, "success");
                            $.fancybox.close();
	        		    } else if ( response.status == "error" ) {
                            this_form.unblock();
                            uni_popup_message(response.message, "error");
	        		    }
	        	    },
	        	    error:function(response){
	        	        this_form.unblock();
	        	        uni_popup_message("Error!", "warning");
	        	    }
	            });
            }
            return false;
    });

    if ( unithemeparams.lazy_load_on_products ) {
        $('.products').infinitescroll({
            navSelector  : ".woocommerce-pagination",
            nextSelector : ".woocommerce-pagination ul li a.next",
            itemSelector : "li.product",
            loading: {
                finished: undefined,
                finishedMsg: "<em>"+unithemeparams.lazy_load_end+"</em>",
                img: unithemeparams.lazy_loader,
                msg: null,
                msgText: "",
                selector: null,
                speed: 'fast',
                start: undefined
            },
            pixelsFromNavToBottom: 240
        });
    }
    if ( unithemeparams.lazy_load_on_events ) {
        $('.eventsWrap').infinitescroll({
            navSelector  : ".pagination",
            nextSelector : ".pagination ul li a.uni-page-next",
            itemSelector : ".eventItem",
            loading: {
                finished: undefined,
                finishedMsg: "<em>"+unithemeparams.lazy_load_end+"</em>",
                img: unithemeparams.lazy_loader,
                msg: null,
                msgText: "",
                selector: null,
                speed: 'fast',
                start: undefined
            },
            pixelsFromNavToBottom: 700
        });
    }
    if ( unithemeparams.lazy_load_on_posts ) {
        $('.blogPostWrap').infinitescroll({
            navSelector  : ".pagination",
            nextSelector : ".pagination ul li a.uni-page-next",
            itemSelector : ".postItem",
            loading: {
                finished: undefined,
                finishedMsg: "<em>"+unithemeparams.lazy_load_end+"</em>",
                img: unithemeparams.lazy_loader,
                msg: null,
                msgText: "",
                selector: null,
                speed: 'fast',
                start: undefined
            },
            pixelsFromNavToBottom: 240
        });
    }

    // hide the loader after page is loaded
    $(window).load(function() {
        $(".loaderWrap").addClass("hide");

    });

});