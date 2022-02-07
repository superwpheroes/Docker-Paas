window.UM_Gallery_Pro = {};
( function( window, $, app ) {

	function equalHeight() {
		// Equalize column heights
		$(".um-gallery-album-list .um-gallery-grid-item img,.um-gallery-grid img").matchHeight();
	}
	equalHeight();

	var modal_id = '#um-gallery-modal';
	Dropzone.autoDiscover = false;
	var myDropzone = '';
	var current_photo_id = 0;

	app.current_album	= 0;
	app.total_process 	= 0;
	app.total_processed = 0;
	app.init = function() {
		app.current_photo_id = 0;
		app.events();
	}

	app.removeURLParameter = function(url, parameter) {
	   var urlParts = url.split('?');

	  if (urlParts.length >= 2) {
	    // Get first part, and remove from array
	    var urlBase = urlParts.shift();

	    // Join it back up
	    var queryString = urlParts.join('?');

	    var prefix = encodeURIComponent(parameter) + '=';
	    var parts = queryString.split(/[&;]/g);

	    // Reverse iteration as may be destructive
	    for (var i = parts.length; i-- > 0; ) {
	      // Idiom for string.startsWith
	      if (parts[i].lastIndexOf(prefix, 0) !== -1) {
	        parts.splice(i, 1);
	      }
	    }

	    url = urlBase + '?' + parts.join('&');
	  }

	  return url;
	}
	app.events = function() {

		// Main content container
		var $container = jQuery('.um-gallery-container');
		//var $container = document.querySelector('.um-gallery-container');
		var msnry;
		if ( $container ) {
			// Masonry + ImagesLoaded
			$container.imagesLoaded(function(){
				
				if ( true == $container.data('masonry') ) {
					msnry = new Masonry( document.querySelector('.um-gallery-container'), {
						itemSelector: '.um-gallery-item'
					});
				}
				
			});

			function URLToArray(url) {
			    var request = {};
			    var pairs = url.substring(url.indexOf('?') + 1).split('&');
			    for (var i = 0; i < pairs.length; i++) {
			        if(!pairs[i])
			            continue;
			        var pair = pairs[i].split('=');
			        request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
			     }
			     return request;
			}

			function getLoadMoreUrl() {
				var page  = $container.data( 'page' );
				var query = $container.data( 'query_args' );
				query = jQuery.param( query );

				page = page + 1;
				var query2 = URLToArray( query );
				query2.page = page;
				query = jQuery.param( query2 );
				return um_gallery_config.ajax_url + '?action=um_gallery_get_more_photos&page=' + page + '&' + query;
			}

			$container.infiniteScroll({
				// options
				path: '.pagination__next',
				append: false, // disable automatic appending
				path: getLoadMoreUrl,
				responseType: 'text',
				history: false,
				// finished message
				loading: {
					finishedMsg: 'No more pages to load.'
					}
				},
				// Trigger Masonry as a callback
				function( newElements ) {
					// hide new items while they are loading
					var $newElems = $( newElements ).css({ opacity: 0 });
					// ensure that images load before adding to masonry layout
					$newElems.imagesLoaded(function(){
						// show elems now they're ready
						$newElems.animate({ opacity: 1 });
						$container.masonry( 'appended', $newElems, true );
						equalHeight();
					});
				}
			);
			
			$container.on( 'load.infiniteScroll', function( event, response ) {
				// Boost page counter.
				var page = $container.data('page');
				page = page + 1;
				$container.data('page', page );

				var data = JSON.parse( response );

				$.each( data.images, function( key, value ) {
					window.um_gallery_images[ key ] = value;
				});

				$.each( data.users, function( key, value ) {
					window.um_gallery_users[ key ] = value;
				});
				
				// get posts from response
				var $posts = $( data.html ).find('.um-gallery-item');
				
				if ( ! $posts.length ) {
					$container.data( 'um-gallery-last-load', 1 );
					$container.infiniteScroll('destroy');
					return;
				} 

				// append posts after images loaded
				$posts.imagesLoaded( function() {
					$container.infiniteScroll( 'appendItems', $posts );
					equalHeight();
					if ( true == $container.data('masonry') ) {
						msnry.appended( $posts );
					}
				});
			});
		}
		
		if ( typeof comments === "function" ) {
			jQuery('#um-gallery-comments').comments();
		}
		
		//Open Album Form
		jQuery(document).on("click", ".um-gallery-form,.um-gallery-edit-link", function(event){
			event.preventDefault();
			var id = $(this).data('id');
			app._um_gallery_album_form( id );

		});

		jQuery( document ).on( 'click', '.um-gallery-full-screener', app.toggleFullScreen );
		jQuery( document ).on( "click", '.um-gallery-pro-action-buttons ul li a', function( event ) {
			event.preventDefault();
			jQuery( '.um-gallery-pro-action-buttons ul li' ).removeClass( 'active' );
			jQuery( this ).parent('li').addClass( 'active' );
			var tab = $(this).attr( 'href' );
			tab = tab.split('#')[1];
			app.um_gallery_change_tab( tab );
		});

		jQuery( document ).on( "click", '.um-gallery-add-video', function( event ) {
			event.preventDefault();
			//add video
			var obj = $('#um-gallery-pro-video-insert #video_url');
			var url = obj.val();
			if( ! url ) {
				return;
			}
			var video_type = app.um_gallery_get_video_type( url );

			if( ! video_type.type ) {
				return;
			}

			var thumbnail 	= '';
			var video_id 	= '';
			var content 	= '';
			var video_holder = jQuery( '.um-gallery-pro-video-list' );
			if( 'youtube' == video_type.type ){

				video_id = video_type.id;
				thumbnail = '//i.ytimg.com/vi/'+ video_id +'/hqdefault.jpg';
				content = '<div class="um-gallery-video-items">'+
				'<div class="um-gallery-video-image"><img src="' + thumbnail +'" /></div>' +
				'<input type="hidden" class="um-gallery-video-url" name="video[]" value="' + url + '" />'
				'</div>';
				video_holder.append( content );
			}

			if( 'vimeo' == video_type.type ) {

				$.ajax({
					type:'GET',
					url: '//vimeo.com/api/v2/video/' + video_type.id + '.json',
					jsonp: 'callback',
					dataType: 'jsonp',
					success: function(data){
						thumbnail = data[0].thumbnail_large;
						content = '<div class="um-gallery-video-items">'+
						'<div class="um-gallery-video-image"><img src="' + thumbnail +'" /></div>' +
						//'<div class="um-gallery-video-title">' + title +'</div>' +
						'<input type="hidden" class="um-gallery-video-url" name="video[]" value="' + url + '" />'
						'</div>';
						video_holder.append( content );
					}
				});
			}

			if ( 'hudl' === video_type.type ) {
				$.ajax({
					type:'GET',
					url: um_gallery_config.ajax_url,
					data: {
						action: 'um_gallery_fetch_remote_thumbnail',
						videoType: video_type.type,
						videoUrl: url,
					},
					//jsonp: 'callback',
					//dataType: 'jsonp',
					success: function(data){
						thumbnail = data.thumbnail;
						content   = '<div class="um-gallery-video-items">'+
						'<div class="um-gallery-video-image"><img src="' + thumbnail +'" /></div>' +
						//'<div class="um-gallery-video-title">' + title +'</div>' +
						'<input type="hidden" class="um-gallery-video-url" name="video[]" value="' + url + '" />'
						'</div>';
						video_holder.append( content );
					}
				});
			}

			// clear the field after content added
			obj.val( '' );
		});
		jQuery(document).on("click", "#um-gallery-caption-edit,.um-gallery-quick-edit", function(event){
			event.preventDefault();
			var id = $(this).data('id');
			$('.um-user-gallery-modify').slideDown(500);
			$('.um-user-gallery-caption,#um-gallery-caption-edit').slideUp(500);
			//_um_gallery_enable_edit( id );
		});
		jQuery(document).on("click", "#um-gallery-save", function(event){
			event.preventDefault();
			var id = $(this).data('id');
			var type = $(this).data('type');
			if( type === 'album' ){
				app._um_gallery_album_save( id );
			}
		});
		jQuery(document).on("click", ".um-delete-album", function(event){
			event.preventDefault();
			var id = $(this).data('id');
			if ( confirm(um_gallery_config.confirm_delete) ) {
				app._um_gallery_album_delete( id );
			}
		});
		jQuery(document).on("click",".um-gallery-delete-item", function(event){
			event.preventDefault();
			var id = jQuery(this).data("id");
			var obj = jQuery(this);
			if (confirm(um_gallery_config.confirm_delete)) {
				jQuery.ajax({
				  method: "POST",
				  url: um_gallery_config.ajax_url,
				  data: { action: "sp_gallery_um_delete", id: id, 'album_id': um_gallery_config.album_id},
				  success: function(result){
						obj.closest('.um-gallery-item').slideUp().remove();
						equalHeight();
				  }
				})
			}
		});
		jQuery(document).on("click", ".um-gallery-close,.um-gallery-cancel", function(event){
			event.preventDefault();
			//close modal
			jQuery.magnificPopup.close();
		});
		jQuery(document).on("click", "#savePhoto", function(event){
			event.preventDefault();
			var id = $('#um-gallery-modal').data('id');
			app._um_gallery_edit_photo( id );
		});
		jQuery(document).on("click", "#cancelPhoto", function(event){
			event.preventDefault();
			$('.um-user-gallery-modify').slideUp(500);
			$('.um-user-gallery-caption,#um-gallery-caption-edit').slideDown(500);
		});
		jQuery(document).on("click", ".um-gallery-open-photo", function(event){
			event.preventDefault();
			var id = jQuery(this).data('id');
			app._um_gallery_open_photo( id );
		});
		jQuery(document).on("click", ".aqm-delete-gallery-photo", function(e){
			e.preventDefault();
			jQuery('.um-user-gallery-normal').slideUp(500);
			jQuery('.um-user-gallery-edit').slideDown(600);
		});
		$(document).on("click", ".um-user-gallery-confirm", function(e){
			e.preventDefault();
			var option = $(this).data('option');
			if(option === 'no'){
				$('.um-user-gallery-normal').slideDown(500);
				$('.um-user-gallery-edit').slideUp(600);
			}else if(option === 'yes'){
				var id =  $('#um-gallery-modal').data('id');
				app._um_gallery_photo_delete( id );
			}
		});
		//Click on arrows
		jQuery(document).on("click", ".um-user-gallery-arrow a", function(event){
			event.preventDefault();
			var id =  jQuery('#um-gallery-modal').data('id');
			var direction = $(this).data('direction');
			var adjacent_id = '';
			var previous, next, ids = [];

			jQuery.each( um_gallery_images, function(key, value){
				ids.push(key);
			});
			jQuery.each(ids, function (i, data) {
				var currentId = data;
				if (currentId == id){
					//$before = ($index > 0 ? $array[$index - 1] : $array[count($array)-1]);
					//$after = ($index + 1) < count($array) ? $array[$index + 1] : $array[0];
					next = i > 0 ? ids[i-1] : ids[ids.length - 1];
					previous = (i+1) < ids.length  ? ids[i + 1 ] : ids[0];
				}
			});

			if(direction === 'left'){
				//adjacent_id = jQuery('#um-gallery-item-' + id).closest('.um-gallery-item').prev().find('.um-gallery-open-photo').data('id');
				adjacent_id = previous;
				app._um_load_image(adjacent_id);
			}
			if(direction === 'right'){
				//adjacent_id = jQuery('#um-gallery-item-' + id).closest('.um-gallery-item').next().find('.um-gallery-open-photo').data('id');
				adjacent_id = next;
				app._um_load_image(adjacent_id);
			}
		});

		//change arrows with keyboard
		jQuery(document).on("keydown", function(e) {
			//check if modal is open
			if( $('.mfp-wrap #um-gallery-modal').length){

				//check if we are inside of form field
				if (e.target.tagName.toLowerCase() !== 'input' &&
					e.target.tagName.toLowerCase() !== 'textarea') {

					var id =  jQuery('#um-gallery-modal').data('id');

					if(e.keyCode == 37) { // left
						adjacent_id = jQuery('#um-gallery-item-' + id).closest('.um-gallery-item').prev().find('.um-gallery-open-photo').data('id');
						app._um_load_image(adjacent_id);
					} else if(e.keyCode == 39) { // right
						adjacent_id = jQuery('#um-gallery-item-' + id).closest('.um-gallery-item').next().find('.um-gallery-open-photo').data('id');
						app._um_load_image(adjacent_id);
					}
				}
			}
		});
	}

	/**
	 * Calculate progress percentage
	 *
	 * Take the total process to be done. Minus the amount already done and find the percentage.
	 *
	 * @param  {int} processed     Number of items being processed
	 */
	app._um_gallery_progress =  function( processed ) {
		app.total_processed = ( app.total_processed + processed );
		var percent = Math.round(( app.total_processed / app.total_process) * 100);
		//jQuery( '#um-gallery--progress-bar' ).val( percent );
		//
		if ( 100 === percent && app.current_album ) {
			jQuery( '.um-gallery-spinner' ).hide();
			if ( 0 === um_gallery_config.layout_mode || ! um_gallery_config.layout_mode ) {
				app._um_gallery_get_album_item( app.current_album );
			}
			if ( um_gallery_config.closeModalAfterSave ) {
				jQuery.magnificPopup.close();
			}
			app.current_album	= 0;
			app.total_process 	= 0;
			app.total_processed = 0;
		}
	};
	/**
	 * Save album
	 *
	 * @param  {int} id [description]
	 * @return {void}    [description]
	 */
	app._um_gallery_album_save = function( id ){
		// Always reset progress.
		app.total_process 	= 1;
		app.total_processed = 0;
		//app._um_gallery_progress(0);

		// Start the spinner.
		jQuery( '.um-gallery-spinner' ).fadeIn();

		var album_name 			= jQuery('#album_name').val();
		var album_description 	= jQuery('#album_description').val();
		var album_privacy 		= jQuery('#album_privacy').val();
		var file_added 			= false;

		if ( myDropzone.files.length > 0 ) {
			app.total_process	= app.total_process + parseInt( myDropzone.files.length );
			file_added = true;
		}
		if( jQuery( '.um-gallery-video-items input' ).length ){
			jQuery( '.um-gallery-video-items input' ).each(function() {
			  app.total_process	= app.total_process + 1;
			});
		}

		 jQuery('.um-gallery-message').html('').slideUp();
		 jQuery.ajax({
			type: 'post',
			url: um_gallery_config.ajax_url,
			 data: {
				'action': 'um_gallery_album_update',
				'id': id,
				'album_name' : album_name,
				'album_description' : album_description,
				'album_privacy'  : album_privacy,
				'security': um_gallery_config.nonce
			},
			cache: false,
			success: function(response) {
				var file_response;
				// hide placeholder
				jQuery( '.um-gallery-none' ).hide();

				app.current_album = response.id;
				app._um_gallery_progress( 1 );
				if(response.id){
					jQuery('#um-gallery-save').data('id', response.id);
					if( jQuery( '.um-gallery-video-items input' ).length ){
						var videos = [];
						jQuery( '.um-gallery-video-items input' ).each(function() {
						  videos.push( jQuery(this).val() );
						});
						jQuery.ajax({
							type: 'post',
							url: um_gallery_config.ajax_url,
							 data: {
								'action': 	'um_gallery_add_videos',
								'album_id': response.id,
								'videos': 	videos,
								'security': um_gallery_config.nonce
							},
							cache: false,
							success: function(response) {
								jQuery( '.um-gallery-video-items input' ).remove();
								jQuery( '.um-gallery-pro-video-list' ).html('');
								if( response.success === true ){
									if (typeof um_gallery_images !== 'undefined') {
										um_gallery_images = response.data.gallery_images;
									}
									app._um_gallery_progress( videos.length );
									var thumbnail = app.get_video_thumbnail( response.data.video_url );
									if ( ! jQuery( '#um-photo-'+ response.data.id ).length ) {

										var source   = document.getElementById("um_gallery_item_block").innerHTML;
										var data = {
											'id': response.data.id,
											'media_url': response.data.video_url,
											'media_image_url': thumbnail,
										};

										var template = Handlebars.compile(source);
										html    = template( data );
										// Add new item at start.
										jQuery('.um-gallery-grid').prepend( html );
										equalHeight();
									}
								}
							}
						});
					}
					if( file_added == true){
						myDropzone.on('sending', function(file, xhr, formData){
							formData.append('album_id', response.id);
							formData.append('action', 'um_gallery_photo_upload');
						});
						myDropzone.processQueue();

						myDropzone.on('complete', function( file ){
							app._um_gallery_progress( 1 );
							file_response = file.xhr.response;
							file_response = jQuery.parseJSON( file_response );

							var source   = document.getElementById("um_gallery_item_block").innerHTML;
							var data = {
								'id': file_response.id,
								'media_url': file_response.image_src,
								'media_image_url': file_response.thumb,
							};

							var template = Handlebars.compile(source);
							html    = template( data );
			
							myDropzone.removeFile( file );
							// Add new item at start.
							jQuery('.um-gallery-grid').prepend( html );
							equalHeight();
							if (typeof um_gallery_images !== 'undefined') {
								um_gallery_images = file_response.gallery_images;
							}
						});
					}else{
						if( response.new === true){
							app._um_gallery_get_album_item( response.id );
						}
					}
				}
			}
		});
	}

	/**
	 * Get album html
	 *
	 * @param  {int} id
	 *
	 * @return {html}
	 */
	app._um_gallery_get_album_item = function( id ) {
		jQuery.ajax({
			type: 'get',
			url: um_gallery_config.ajax_url,
			data: {
				'action': 'um_gallery_get_album_item',
				'album_id': id,
				'security': um_gallery_config.nonce
			},
			cache: false,
			success: function(response) {
				if ( jQuery( '#um-album-' + id ).length ){
					jQuery( '#um-album-' + id ).replaceWith( response );
				} else {
					jQuery('.um-gallery-album-list').prepend( response );
				}
				equalHeight();
			}
		});
	}

	/**
	 * Edit Photo
	 *
	 * @param  {int} id ID
	 */
	app._um_gallery_edit_photo = function( id ){
		var formData = jQuery('#um-gallery-photo-form').serializeArray();

		jQuery.ajax({
			type: 'post',
			url: um_gallery_config.ajax_url,
			data: formData,
			cache: false,
			success: function(response) {
				//jQuery('#um-gallery-data').html('var um_gallery_images = '+JSON.stringify(response)+';');
				um_gallery_images = response;
				app._um_load_image( id );
			}
		});
	}
	/**
	 * Not being used
	 * @param  {int} id
	 * @return void
	 */
	app._um_gallery_enable_edit = function( id ){

	}
	/**
	 * Get Album form in modal
	 * @param  {int} id
	 * @return {html}
	 */
	app._um_gallery_album_form = function( id ) {
		var modal_id = '#um-gallery-modal';
		jQuery(modal_id).html('<div class="um-gallery-loader"><i class="fa fa-spin fa-spinner"></i></div>');
		jQuery.magnificPopup.open({
		  items: {
			src: jQuery('<div id="um-gallery-modal" class="um-gallery-popup"></div>')
		  },
		  closeMarkup: '<a title="%title%" class="mfp-close">&#215;</a>',
		  type: 'inline',
		  'mainClass': 'um-gallery-modal-wrapper',

		  // You may add options here, they're exactly the same as for $.fn.magnificPopup call
		  // Note that some settings that rely on click event (like disableOn or midClick) will not work here
		}, 0);
		if ( ! id ) {
			id = 0;
		}
		jQuery.ajax({
			type: 'get',
			url: um_gallery_config.ajax_url,
			data: {
				'action': 'um_gallery_get_album_form',
				'album_id': id
			},
			success: function(response) {
				jQuery(modal_id).html(response);
				jQuery(modal_id).animate({'max-width':'740px'}, 'slow');
				myDropzone = new Dropzone("#dropzone", {
					 url: um_gallery_config.ajax_url,
					 autoProcessQueue: false,
					 parallelUploads: 5000,
					 method: "post",
					 acceptedFiles: "image/*",
					 dictDefaultMessage: um_gallery_config.dictDefaultMessage,
					 queuecomplete: function(){
						jQuery( '.um-gallery-message' ).html( um_gallery_config.upload_complete ).slideDown();
						//location.reload();
					 }
				});

			}
		});
	}

	/**
	 * To be deleted
	 * @param  {int} id ID to
	 */
	function _um_gallery_photo_form( id ){
		jQuery.ajax({
			type: 'post',
			url: um_gallery_config.ajax_url,
			 data: {
				'action': 'um_gallery_photo_delete',
				'id': id,
				'security': um_gallery_config.nonce
			},
			cache: false,
			success: function(response) {

			}
		});
	}

	/**
	 * Delete photo via AJAX
	 *
	 * @param  {int} id The Photo ID
	 * @return {json}
	 */
	app._um_gallery_photo_delete = function( id ){
		jQuery.ajax({
			type: 'post',
			url: um_gallery_config.ajax_url,
			data: {
				'action': 'sp_gallery_um_delete',
				'id': id,
				'album_id': um_gallery_config.album_id,
				'security': um_gallery_config.nonce
			},
			cache: false,
			success: function(response) {
				jQuery.magnificPopup.close();
				jQuery("#um-photo-" + id).slideUp().remove();
				um_gallery_images = response;
			}
		});
	}

	/**
	 * Delete Album
	 *
	 * @param  {int} id
	 * @return {void}
	 */
	app._um_gallery_album_delete = function( id ){
		jQuery.ajax({
			type: 'post',
			url: um_gallery_config.ajax_url,
			data: {
				'action': 'um_gallery_delete_album',
				'id': id,
				'security': um_gallery_config.nonce
			},
			cache: false,
			success: function(response) {
				jQuery("#um-album-" + id).slideUp().remove();
				if ( ! jQuery( "div[id^='um-album-']" ).length ) {
					jQuery( '.um-gallery-none' ).show();
				}
			}
		});
	}

	/**
	 * Load info for the curent media
	 *
	 * @param  {int} id
	 * @return {void}
	 */
	app._um_load_info = function( id ){
		jQuery.ajax({
			type: 'get',
			url: um_gallery_config.ajax_url,
			data: {
				'action': 'um_photo_info',
				'id': id,
				'security': um_gallery_config.nonce
			},
			cache: false,
			success: function(response) {
				jQuery('#um-user-gallery-title').text(response.title);
				jQuery('#um-user-gallery-description').text(response.caption);
			}
		});
	}

	/**
	 * Load Image
	 *
	 * @param  {int} id
	 * @return {void}
	 */
	app._um_load_image = function( id ){
		if ( ! id || id === 'undefined' ) {
			return false;
		}

		app.current_photo_id = id;
		var caption     = 'caption' in um_gallery_images[id] ? um_gallery_images[id].caption : '';
		//caption.replace("\n", "<br />");
		var description = 'description' in um_gallery_images[id] ? um_gallery_images[id].description : '';
		var user_id     = um_gallery_images[id].hasOwnProperty('user_id') ? um_gallery_images[id].user_id : '';
		var category    = um_gallery_images[id].hasOwnProperty('category') && um_gallery_images[id].category.length ? um_gallery_images[id].category[0] : [];
		var category_id = um_gallery_images[id].hasOwnProperty('category_ids') && um_gallery_images[id].category_ids.length ? um_gallery_images[id].category_ids[0] : 0;
		var tags        = um_gallery_images[id].hasOwnProperty('tags') && um_gallery_images[id].tags.length ? um_gallery_images[id].tags : [];
		var media_frame = '';


		// Get the HTML tmpl.
		var source   = document.getElementById("um_gallery_media").innerHTML;
		
		var type 	= um_gallery_images[id].type;
		var image 	= jQuery( '#um-gallery-item-' + id ).attr('data-source-url');
		if ( 'youtube' == type || 'vimeo' == type || 'hudl' === type  ) {
			var vid = app.um_gallery_get_video_type( image );
			if ( 'youtube' == type ) {
				video_id = vid.id;
				media_frame = '<iframe class="mfp-iframe" width="100%" src="//www.youtube.com/embed/' + video_id + '" frameborder="0" allowfullscreen></iframe>';
			} else if( 'vimeo' == type ) {
				video_id = vid.id;
				media_frame = '<iframe src="//player.vimeo.com/video/' + video_id + '" width="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
			} else if( 'hudl' === type ) {
				video_id = vid.id;
				media_frame = '<iframe src="//www.hudl.com/embed/video/' + video_id + '" width="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
			}
		}

		var data = {
			'media_id': id,
			'caption': caption,
			'description': description,
			'type': type,
			'link': um_gallery_users[user_id].link,
			'avatar': um_gallery_users[user_id].avatar,
			'avatar_name': um_gallery_users[user_id].name,
			'user_id': user_id,
			'media_frame': media_frame,
			'image': image,
			'category': category,
			'tags': tags,
			'is_owner': um_gallery_images[id].current_user == user_id ? true : false
		};

		var template = Handlebars.compile(source);

		html    = template( data );
		jQuery.magnificPopup.open({
			items: {
				src: html
			},
			type: 'inline',
			closeMarkup: '<a title="%title%" class="mfp-close">&#215;</a>',
			'mainClass': 'um-gallery-modal-wrapper',
			callbacks: {
				open: function() {
				},
				close: function() {
				  jQuery('body').removeClass('gallery-open');
				}
			  }
		}, 0);

		$("#um_gallery_tag_list").tagit({
			fieldName: "tax_input[um_gallery_tag][]",
			autocomplete: {
				delay: 0,
				minLength: 2,
				source: um_gallery_config.ajaxurl + "?action=um_gallery_suggest_tabs",
			}
		});
		if ( category_id ) {
			jQuery('#um-gallery-cat-picker').val( category_id );
		}

		//jQuery( '.mfp-content' ).html( html );
		jQuery('#aqm_comment_id').val(id);
		if( um_gallery_config.enable_comments ) {
			app.fetchComments( id );
		}
	}

	/**
	 * Fetch Comments based on media
	 *
	 * @param  {int} id
	 * @return {mixed}
	 */
	app.fetchComments = function( id ) {
		$('#um-gallery-comments').comments({
			enableReplying: true,
			currentUserId:  ( um_gallery_config.user ? um_gallery_config.user.id : false ),
			//canComment: ( um_gallery_config.user ? true : false ),
			readOnly: ( um_gallery_config.user ? false : true ),
			roundProfilePictures: true,
			enableDeletingCommentWithReplies: true,
			enableNavigation: false,
			enableUpvoting: false,
			profilePictureURL: ( um_gallery_config.user && um_gallery_config.user.avatar ? um_gallery_config.user.avatar : '' ),

			 // Strings to be formatted (for example localization)
			textareaPlaceholderText: um_gallery_config.comments.textareaPlaceholderText,
			newestText: um_gallery_config.comments.newestText,
			oldestText: um_gallery_config.comments.oldestText,
			popularText: um_gallery_config.comments.popularText,
			attachmentsText: um_gallery_config.comments.attachmentsText,
			sendText: um_gallery_config.comments.sendText,
			replyText: um_gallery_config.comments.replyText,
			editText: um_gallery_config.comments.editText,
			editedText: um_gallery_config.comments.editedText,
			youText: um_gallery_config.comments.youText,
			saveText: um_gallery_config.comments.saveText,
			deleteText: um_gallery_config.comments.deleteText,
			viewAllRepliesText: um_gallery_config.comments.viewAllRepliesText,
			hideRepliesText: um_gallery_config.comments.hideRepliesText,
			noCommentsText: um_gallery_config.comments.noCommentsText,
			noAttachmentsText: um_gallery_config.comments.noAttachmentsText,
			attachmentDropText: um_gallery_config.comments.attachmentDropText,
			textFormatter: function(text) {return text},
			// Get Comments,
			getComments: function(success, error) {
				$.ajax({
					type: 'get',
					url: um_gallery_config.ajax_url,
					data: {
						action: 'um_gallery_get_comments',
						id: app.current_photo_id
					},
					success: function(commentsArray) {
						success(commentsArray)
					},
					error: error
				});
			},
			postComment: function(commentJSON, success, error) {
				commentJSON.action = "um_gallery_post_comment";
				commentJSON.photo_id = app.current_photo_id;
				$.ajax({
					type: 'post',
					url: um_gallery_config.ajax_url,
					data: commentJSON,
					success: function(comment) {
						commentJSON.id = comment.id;
						success(commentJSON)
					},
					error: error
				});
			},
			putComment: function(commentJSON, success, error) {
				commentJSON.action = "um_gallery_post_comment";
				commentJSON.photo_id = app.current_photo_id;
				$.ajax({
					type: 'post',
					url: um_gallery_config.ajax_url,
					data: commentJSON,
					success: function(comment) {
						success(commentJSON)
					},
					error: error
				});
			},
			deleteComment: function(commentJSON, success, error) {
				commentJSON.action = "um_gallery_delete_comment";
				$.ajax({
					type: 'post',
					url: um_gallery_config.ajax_url,
					data: commentJSON,
					success: success,
					error: error
				});
			}
		});
	}
	/**
	 * Open a media item based on ID
	 *
	 * @param  {[type]} id [description]
	 * @return {[type]}    [description]
	 */
	app._um_gallery_open_photo = function( id ){
		var image = jQuery('#um-gallery-item-' + id).attr('href');
		var source   = document.getElementById("um_gallery_media").innerHTML;
		var template = Handlebars.compile(source);
		html    = template();
		jQuery.magnificPopup.open({
			items: {
				src: '<div id="um-gallery-modal" class="um-gallery-popup" data-id="' + id + '">Loading icon</div>'
			},
			type: 'inline',
			closeMarkup: '<a title="%title%" class="mfp-close">&#215;</a>',
			'mainClass': 'um-gallery-modal-wrapper',
			callbacks: {
				open: function() {
					
					jQuery('.um-user-gallery-image-wrap').css('background-image',  'url(' + image + ')');
					//_um_load_info( id );
					app._um_load_image( id );

					jQuery('body').addClass('gallery-open');
				},
				close: function() {
				  jQuery('body').removeClass('gallery-open');
				}
			  }
		}, 0);
	}

	app.toggleFullScreen = function( e ) {
		e.preventDefault();
		jQuery( 'body' ).toggleClass( 'gallery-full-screen' );
	}
	/**
	 * Change tab in modal
	 *
	 * @param  {string} tab
	 * @return {void}
	 */
	app.um_gallery_change_tab = function( tab ) {
		if ( '' == tab ) {
			tab = 'photo';
		}
		jQuery( '.um-gallery-form-tabs > div' ).hide();
		jQuery( '#um-gallery-form-tab-' + tab ).show();

	}

	app.get_video_thumbnail = function( video_url ) {
		var video_type = app.um_gallery_get_video_type( video_url );

		if( ! video_type.type ) {
			return;
		}

		var thumbnail 	= '';
		var video_id 	= '';
		var content 	= '';
		var video_holder = jQuery( '.um-gallery-pro-video-list' );
		if( 'youtube' == video_type.type ){

			video_id = video_type.id;
			window.UM_Gallery_Pro.thumbnail = '//i.ytimg.com/vi/'+ video_id +'/0.jpg';
		}

		if( 'vimeo' == video_type.type ) {

			$.ajax({
				type:'GET',
				url: '//vimeo.com/api/v2/video/' + video_type.id + '.json',
				jsonp: 'callback',
				dataType: 'jsonp',
				success: function(data){
					window.UM_Gallery_Pro.thumbnail = data[0].thumbnail_large;
				}
			});
		}

		if ( 'hudl' == video_type.type ) {
			$.ajax({
				type:'GET',
				url: um_gallery_config.ajax_url,
				data: {
					action: 'um_gallery_fetch_remote_thumbnail',
					videoType: video_type.type,
					videoUrl: video_url,
				},
				success: function(data){
					window.UM_Gallery_Pro.thumbnail = data.thumbnail;
				}
			});
		}
		return window.UM_Gallery_Pro.thumbnail;
	}

	/**
	 * Get video data based on URL
	 *
	 * @param  {string} url
	 * @return {array}
	 */
	app.um_gallery_get_video_type = function( url ) {
		if ( '' == url ) {
			return;
		}
		// - Supported YouTube URL formats:
		//   - http://www.youtube.com/watch?v=My2FRPA3Gf8
		//   - http://youtu.be/My2FRPA3Gf8
		//   - https://youtube.googleapis.com/v/My2FRPA3Gf8
		// - Supported Vimeo URL formats:
		//   - http://vimeo.com/25451551
		//   - http://player.vimeo.com/video/25451551
		// - Also supports relative URLs:
		//   - //player.vimeo.com/video/25451551

		url.match(/(http:\/\/|https:\/\/|)(player.|www.)?(hudl\.com|vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/);
		var type = null;
		var id = RegExp.$6;
		if (RegExp.$3.indexOf('youtu') > -1) {
			type = 'youtube';
		} else if (RegExp.$3.indexOf('vimeo') > -1) {
			type = 'vimeo';
		} else if ( RegExp.$3.indexOf('hudl') > -1 ) {
			type = 'hudl';
			url.match(/(http:\/\/|https:\/\/|)(player.|www.)?(hudl\.com)\/(video\/)?(.*)(\&\S+)?/);
			id   = RegExp.$5;
		}
		return {
			type: type,
			id: id
		};
		return false;
	}

	$( app.init );

})( window, jQuery, window.UM_Gallery_Pro );




jQuery(window).resize(function($) {
	var winsize = jQuery(window).width();
	var modal_size = winsize - (winsize * 0.15);
	modal_size = Math.round(modal_size);
	//jQuery('#um-gallery-modal').animate({'max-width': modal_size + 'px'}, 'slow');
});

Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
    switch (operator) {
        case '==':
            return (v1 == v2) ? options.fn(this) : options.inverse(this);
        case '===':
            return (v1 === v2) ? options.fn(this) : options.inverse(this);
        case '!=':
            return (v1 != v2) ? options.fn(this) : options.inverse(this);
        case '!==':
            return (v1 !== v2) ? options.fn(this) : options.inverse(this);
        case '<':
            return (v1 < v2) ? options.fn(this) : options.inverse(this);
        case '<=':
            return (v1 <= v2) ? options.fn(this) : options.inverse(this);
        case '>':
            return (v1 > v2) ? options.fn(this) : options.inverse(this);
        case '>=':
            return (v1 >= v2) ? options.fn(this) : options.inverse(this);
        case '&&':
            return (v1 && v2) ? options.fn(this) : options.inverse(this);
        case '||':
            return (v1 || v2) ? options.fn(this) : options.inverse(this);
        default:
            return options.inverse(this);
    }
});

Handlebars.registerHelper('list', function(context, options) {
  var ret = "<ul>";

  for(var i=0, j=context.length; i<j; i++) {
    ret = ret + "<li>" + options.fn(context[i]) + "</li>";
  }

  return ret + "</ul>";
});
