jQuery(document).ready(function($){

if ( uniavatarparams.upload_form_in_front_on == true ) {
    /*
    *  modal window based on the new media uploader of WP
    */
    var ds = ds || {};
	var media;

	ds.media = media = {};

	$.extend( media, { view: {}, controller: {} } );

	media.view.UniAvatarUpload = wp.media.View.extend( {
		className: 'upload-avatar-frame',
		template:  wp.media.template( 'uni-avatar-upload' )
	} );

	media.controller.UniAvatarUpload = wp.media.controller.State.extend( {
		defaults: {
			id:         'upload-avatar-state',
			menu:       'default',
			content:    'upload_avatar_state',
            type:       'image'
		}
	} );

    var media_button = $('.uni-avatar-open-modal'),
        user_id = media_button.data("user_id"),
        user_avatar = media_button.data("user_avatar");

    if ( $("#uni_save_avatar").length > 0 ) {
        $("#uni_save_avatar").find("input[name=user_id]").val(user_id);
    }
    if ( uniavatarparams.select_avatars_on == true ) {

        $("#uni_select_avatar").find("input[name=user_id]").val(user_id);

	    media.view.UniAvatarSelect = wp.media.View.extend( {
		    className: 'select-avatar-frame',
		    template:  wp.media.template( 'uni-avatar-select' )
	    } );

	    media.controller.UniAvatarSelect = wp.media.controller.State.extend( {
		    defaults: {
			    id:         'select-avatar-state',
			    menu:       'default',
			    content:    'select_avatar_state',
                type:       'image'
		    }
	    } );

    }

    if ( uniavatarparams.select_avatars_on == true ) {

	$.extend( media, {
		frame: function() {
			if ( this._frame )
				return this._frame;

			var states = [
				new media.controller.UniAvatarUpload( {
					title:    uniavatarparams.modal_upload_title,
					id:       'upload-avatar-state',
                    toolbar:  'main-gallery',
					priority: 50,
				} ),
				new media.controller.UniAvatarSelect( {
					title:    uniavatarparams.modal_select_title,
					id:       'select-avatar-state',
                    toolbar:  'main-gallery',
					priority: 60,
				} ),
			];

			this._frame = wp.media( {
				className: 'media-frame no-sidebar',
                state: 'upload-avatar-state',
				states: states
			} );

			this._frame.on( 'content:create:upload_avatar_state', function() {
				var view = new ds.media.view.UniAvatarUpload( {
					controller: media.frame(),
					model:      media.frame().state()
				} );

				media.frame().content.set( view );
                //console.log('on');

                uni_avatar_ajax_load();
			} );

			this._frame.on( 'content:create:select_avatar_state', function() {
				var view = new ds.media.view.UniAvatarSelect( {
					controller: media.frame(),
					model:      media.frame().state()
				} );

				media.frame().content.set( view );

                $("#uni_select_avatar").on("submit", function(e){
                    e.preventDefault();

		            var this_form = $(this),
                        action = this_form.attr('action'),
			            selected = this_form.find("input[type=radio]:checked").val(),
                        user_id = this_form.find("input[name=user_id]").val(),
                        dataToSend = this_form.serialize();
                        //console.log(selected);

                        if ( selected ) {
			                $.ajax({
				                type:'post',
	        	                url: action,
	        	                data: dataToSend,
	        	                dataType: 'json',
                                beforeSend: function(){
                                    $("#upload-loader").show();
                                },
	        	                success: function(response) {
                                    //console.log(response);
                                    $("#upload-loader").hide();
	        		                if (response.status == 'success') {
                                        $("#uni-avatar-preview-original img", window.parent.document).attr("src", response.url);
                                        $( ".uni-user-avatar-image" ).each(function( index ) {
                                            var $this_img = $( this );
                                            if ( $this_img.data("user_id") == user_id ) {
                                                $this_img.attr("src", response.url);
                                            }
                                        });
                                        media.frame().close();
		        	                } else if (response.status == 'error') {
		        	                    $("#upload-loader").hide();
		        		                console.log(response.message);
		        	                }
	        	                },
	        	                error:function(response){
	        	                    //console.log(response);
	        	                    if (response.status !== 'success' || response.status != 'error') {
	        	    	                alert('Something went wrong. Please, try again later.');

					                }
	        	                }
	                        });
                            return false;
                        }
                });

			} );

			this._frame.on( 'open', this.open );

			this._frame.on( 'ready', this.ready );

			this._frame.on( 'close', this.close );

			this._frame.on( 'menu:render:default', this.menuRender );

			return this._frame;
		},

		open: function() {
			$( '.media-modal' ).addClass( 'smaller' );
            //console.log('open');
		},

		ready: function() {
		},

		close: function() {
			$( '.media-modal' ).removeClass( 'smaller' );
            //console.log('close');
		},

		menuRender: function( view ) {
		    //console.log( view );
		},

		select: function() {
			var settings = wp.media.view.settings,
				selection = this.get( 'selection' );

			$( '.added' ).remove();
			selection.map( media.showAttachmentDetails );
		},

		showAttachmentDetails: function( attachment ) {
			var details_tmpl = $( '#attachment-details-tmpl' ),
				details = details_tmpl.clone();

			details.addClass( 'added' );

			$( 'input', details ).each( function() {
				var key = $( this ).attr( 'id' ).replace( 'attachment-', '' );
				$( this ).val( attachment.get( key ) );
			} );

			details.attr( 'id', 'attachment-details-' + attachment.get( 'id' ) );

			var sizes = attachment.get( 'sizes' );
			$( 'img', details ).attr( 'src', sizes.thumbnail.url );

			$( 'textarea', details ).val( JSON.stringify( attachment, null, 2 ) );

			details_tmpl.after( details );
		},

		init: function() {
			$( media_button ).on( 'click', function(e) {
				e.preventDefault();
                //console.log( 'init' );

                if ( media.frame()._state == 'upload-avatar-state' ) {
                    media.frame().open();
				    var view = new ds.media.view.UniAvatarUpload( {
					    controller: media.frame(),
					    model:      media.frame().state()
				    } );
				    media.frame().content.set( view );
                } else {
				    media.frame().open().lastState();
                }

                uni_avatar_ajax_load();
			});
		}
	} );

    } else {

	$.extend( media, {
		frame: function() {
			if ( this._frame )
				return this._frame;

			var states = [
				new media.controller.UniAvatarUpload( {
					title:    uniavatarparams.modal_upload_title,
					id:       'upload-avatar-state',
                    toolbar:  'main-gallery',
					priority: 50,
				} )
			];

			this._frame = wp.media( {
				className: 'media-frame no-sidebar',
                state: 'upload-avatar-state',
				states: states
			} );

			this._frame.on( 'content:create:upload_avatar_state', function() {
				var view = new ds.media.view.UniAvatarUpload( {
					controller: media.frame(),
					model:      media.frame().state()
				} );

				media.frame().content.set( view );
                //console.log('on');

                uni_avatar_ajax_load();
			} );

			this._frame.on( 'open', this.open );

			this._frame.on( 'ready', this.ready );

			this._frame.on( 'close', this.close );

			this._frame.on( 'menu:render:default', this.menuRender );

			return this._frame;
		},

		open: function() {
			$( '.media-modal' ).addClass( 'smaller' );
            //console.log('open');
		},

		ready: function() {
		},

		close: function() {
			$( '.media-modal' ).removeClass( 'smaller' );
            //console.log('close');
		},

		menuRender: function( view ) {
		    //console.log( view );
		},

		select: function() {
			var settings = wp.media.view.settings,
				selection = this.get( 'selection' );

			$( '.added' ).remove();
			selection.map( media.showAttachmentDetails );
		},

		showAttachmentDetails: function( attachment ) {
			var details_tmpl = $( '#attachment-details-tmpl' ),
				details = details_tmpl.clone();

			details.addClass( 'added' );

			$( 'input', details ).each( function() {
				var key = $( this ).attr( 'id' ).replace( 'attachment-', '' );
				$( this ).val( attachment.get( key ) );
			} );

			details.attr( 'id', 'attachment-details-' + attachment.get( 'id' ) );

			var sizes = attachment.get( 'sizes' );
			$( 'img', details ).attr( 'src', sizes.thumbnail.url );

			$( 'textarea', details ).val( JSON.stringify( attachment, null, 2 ) );

			details_tmpl.after( details );
		},

		init: function() {
			$( media_button ).on( 'click', function(e) {
				e.preventDefault();
                //console.log( 'init' );

                if ( media.frame()._state == 'upload-avatar-state' ) {
                    media.frame().open();
				    var view = new ds.media.view.UniAvatarUpload( {
					    controller: media.frame(),
					    model:      media.frame().state()
				    } );
				    media.frame().content.set( view );
                } else {
				    media.frame().open().lastState();
                }

                uni_avatar_ajax_load();
			});
		}
	} );

    }

	$( media.init );

}

function uni_avatar_form_init() {

    /*
    *  Crop
    */
        var jcrop_api,
            boundx,
            boundy,
            $preview = $('#uni-avatar-preview-pane'),
            $pcnt = $('#uni-avatar-preview-pane .uni-avatar-preview-container'),
            $pimg = $('#uni-avatar-preview-pane .uni-avatar-preview-container img'),
            xsize = $pcnt.width(),
            ysize = $pcnt.height(),
            $cropped_url = $('input[name=cropped_url]'),
            $cropped_x1 = $('input[name=cropped_x1]'),
            $cropped_y1 = $('input[name=cropped_y1]'),
            $cropped_x2 = $('input[name=cropped_x2]'),
            $cropped_y2 = $('input[name=cropped_y2]'),
            $cropped_w = $('input[name=cropped_w]'),
            $cropped_h = $('input[name=cropped_h]'),
            $crop_session = $('input[name=crop_session]'),
            crop_session_val;

        function updatePreview(c) {
            if (parseInt(c.w) > 0){

                var rx = xsize / c.w;
                var ry = ysize / c.h;

                $pimg.css({
                    width: Math.round(rx * boundx) + 'px',
                    height: Math.round(ry * boundy) + 'px',
                    marginLeft: '-' + Math.round(rx * c.x) + 'px',
                    marginTop: '-' + Math.round(ry * c.y) + 'px'
                });

                $cropped_x1.val(c.x);
                $cropped_y1.val(c.y);
                $cropped_x2.val(c.x2);
                $cropped_y2.val(c.y2);
                $cropped_w.val(c.w);
                $cropped_h.val(c.h);

            }
        };

        $('#uni_cropbox').Jcrop({
                onChange: updatePreview,
                onSelect: updatePreview,
                boxWidth: 250,
                boxHeight: 250,
                aspectRatio: xsize / ysize
            },function(){
                var bounds = this.getBounds();
                boundx = bounds[0];
                boundy = bounds[1];
                jcrop_api = this;
                //console.log('Jcrop');
                $preview.appendTo(jcrop_api.ui.holder);
        });

    /*
    *  fileupload
    */
    $('#avatar_image_upload').fileupload({
            dataType: 'json',
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: uniavatarparams.uni_avatar_max,
            processalways: function (e, data) {
                $("#upload-alert").hide();
                var file = data.files[0];
                if (file.error) {
                    if ( file.error == "File type not allowed" ) {
                        $("#upload-alert").html('<span class="dashicons dashicons-welcome-comments"></span>'+uniavatarparams.file_not_allowed).show();
                    } else if ( file.error == "File is too large" ) {
                        $("#upload-alert").html('<span class="dashicons dashicons-welcome-comments"></span>'+uniavatarparams.file_too_large).show();
                    }
                }
            },
            start: function (e) {
                //console.log('uploading has started');
                $("#upload-loader").show();
            },
            done: function (e, data) {
                //console.log(data);
                $("#upload-loader").hide();
                if ( data.result.status == 'success' ) {
                    //console.log(data.result);
                    $cropped_url.val(data.result.url);
                    $("#uni-avatar-preview-original img").css({width:data.result.width, height:data.result.height}).attr("src", data.result.url);
                    $("input[name=attach_id]").val(data.result.attach_id);
                    jcrop_api.setImage(data.result.url);
                    boundx = data.result.width;
                    boundy = data.result.height;
                    $pimg.css({
                        width: data.result.width + 'px',
                        height: data.result.height + 'px',
                        marginLeft: '0px',
                        marginTop: '0px'
                    });

                } else if ( data.result.status == 'error' ) {
                    console.log(data.result.msg);
                }
            }
    });
    //}).bind('fileuploadprocess ', function (e, data) {});

    /*
    *  save cropped avatar
    */
    $("#uni_save_avatar").on("submit", function(e){
        e.preventDefault();

		var this_form = $(this);
			action = this_form.attr('action'),
            user_id = this_form.find("input[name=user_id]").val(),
            dataToSend = this_form.serialize();
            //console.log(dataToSend);

			$.ajax({
				type:'post',
	        	url: action,
	        	data: dataToSend,
	        	dataType: 'json',
                beforeSend: function(){
                    $("#upload-loader").show();
                },
	        	success: function(response) {
                    //console.log(response);
                    $("#upload-loader").hide();
	        		if (response.status == 'success') {
	        		    //console.log(response.url);
                        $cropped_url.val(response.url);
                        //console.log($cropped_url.val());
                        $("#user-avatar-preview-original img").attr("src", response.url).css({width: 250, height: 250});
                        jcrop_api.setImage(response.url);
                        boundx = 250;
                        boundy = 250;
                        $pimg.attr("src", response.url).css({
                            width: 250 + 'px',
                            height: 250 + 'px',
                            marginLeft: '0px',
                            marginTop: '0px'
                        });
                        crop_session_val = parseInt($crop_session.val());
                        $crop_session.val(crop_session_val+1);

                        $("#uni-avatar-preview-original img", window.parent.document).attr("src", response.url);
                        $( ".uni-user-avatar-image" ).each(function( index ) {
                            var $this_img = $( this );
                            if ( $this_img.data("user_id") == user_id ) {
                                $this_img.attr("src", response.url);
                            }
                        });
		        	} else if (response.status == 'error') {
		        	    $("#upload-loader").hide();
		        		console.log(response.message);
		        	}

	        	},
	        	error:function(response){
	        	    //console.log(response);
	        	    if (response.status !== 'success' || response.status != 'error') {
	        	    	alert('Something went wrong. Please, try again later.');

					}
	        	}
	        });

        return false;
    });

}

    /*
    *  ajax load of current avatar
    */
    function uni_avatar_ajax_load(){

		var this_form = $("#uni_save_avatar");
			user_id = this_form.find("input[name=user_id]").val();
            cropped_url = this_form.find("input[name=cropped_url]");
            uni_cropbox_cont = $("#uni-avatar-preview-original img");
            jcrop_preview_cont = $(".jcrop-preview");

                                var dataToSend = {
                                    action: 'uni_avatar_ajax_load',
                                    user_id: user_id
                                };
            //console.log(dataToSend);
            if ( user_id ) {
			$.ajax({
				type:'post',
	        	url: uniavatarparams.ajax_url,
	        	data: dataToSend,
	        	dataType: 'json',
                beforeSend: function(){
                    $("#upload-loader").show();
                },
	        	success: function(response) {
                    //console.log(response);
                    $("#upload-loader").hide();
	        		if (response.status == 'success') {
	        		    //console.log(response.url);
                        cropped_url.val( response.url );
                        uni_cropbox_cont.attr("src", response.url).css({width: 250, height: 250});
                        jcrop_preview_cont.attr("src", response.url);
                        uni_avatar_form_init();
		        	} else if (response.status == 'error') {
		        	    $("#upload-loader").hide();
		        		console.log(response.message);
		        	}

	        	},
	        	error:function(response){
	        	    //console.log(response);
	        	    if (response.status !== 'success' || response.status != 'error') {
	        	    	alert('Something went wrong. Please, try again later.');

					}
	        	}
	        });
            return false;
            }
    }

    /*
    *  reset avatar image to default one
    */
    $(".uni_delete_avatar").on("click", function(e){
		var this_link = $(this),
            user_id = this_link.data("user_id");

        var dataToSend = {
                            action: "uni_delete_avatar",
                            user_id: user_id
                        };

			$.ajax({
				type:'post',
	        	url: uniavatarparams.ajax_url,
	        	data: dataToSend,
	        	dataType: 'json',
	        	success: function(response) {
                    //console.log(response);
	        		if (response.status == 'success') {
                        $( ".uni-user-avatar-image" ).each(function( index ) {
                            var $this_img = $( this );
                            if ( $this_img.data("user_id") == user_id ) {
                                $this_img.attr("src", response.url);
                            }
                        });
		        	} else if (response.status == 'error') {
		        		console.log(response.message);
		        	}
	        	},
	        	error:function(response){
	        	    //console.log(response);
	        	    if (response.status !== 'success' || response.status != 'error') {
	        	    	alert('Something went wrong. Please, try again later.');

					}
	        	}
	        });

        return false;
    });

});