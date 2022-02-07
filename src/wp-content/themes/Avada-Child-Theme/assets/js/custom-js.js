

function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function cycleImages(){
	var $active = jQuery('#cycler .active');
	var $next = ($active.next().length > 0) ? $active.next('.holdslide') : jQuery('#cycler .holdslide:first');
	$next.css('z-index',2); //move the next image up the pile
	//$active.fadeOut(0,function(){ //fade out the top image
		$active.css('z-index',1).show().removeClass('active'); //reset the z-index and unhide the image
		$next.css('z-index',3).addClass('active'); //make the next image the top one
	//});

}

jQuery('#cycler').width(jQuery(window).width());
if (jQuery(window).width() > 640) {
	x = 2.7;
}
else {
	x = 1.42;
}
var newheight =jQuery(window).width()/x;
jQuery('#cycler').height(newheight);
jQuery('#cycler').css({top: 0, left: -20});






////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
jQuery(document).ready(function ($) {




if (typeof payment_email !== 'undefined') {
    	var email_field = $(".um-register .um-form-field[id^=user_email-]");
    	setTimeout(function(){
    		email_field.val(atob(payment_email)).css('pointer-events','none');
    	},
    		1000);
}

if ( $( ".um-register .um-field-user_email input" ).length ) {
	// setTimeout(function(){
	// 	$( ".um-register .um-field-user_email input" ).attr('placeholder','(Use the same email address as per payment page)');
	// },
 //    		1000);
}

$(".reviews-row .fusion-builder-row").hover(
	function(e){
		$(this).find('span.bold-on-hover').addClass('be-bold');
	},
	// over
	function(e){
		$(this).find('span.bold-on-hover').removeClass('be-bold');
	}
	// out
);

if($('.page-id-14805').length){
	$('.page-id-14805').show();
}


//ux payment
$('.um-form').on('click' , '#card_payment_button , #call_paypal_btn' , function(){
	var first_name_details = $('.um-form #firstName_details').val();
	var last_name_details = $('.um-form #lastName_details').val();
	var email_details = $('.um-form #email_details').val();
	localStorage.setItem("first_name_details", first_name_details);
	localStorage.setItem("last_name_details", last_name_details);
	localStorage.setItem("email_details", email_details);
});

$('#upload-form').on('click' , '#new-entry-process-submit' , function(){
	var user_name_details 	= $('#upload-form #name').val();
	var instagram_details 	= $('#upload-form #instagram').val();
	var email_details 		= $('#upload-form #email').val();
	var website_details 	= $('#upload-form #portfolio').val();
	localStorage.setItem("user_name_details", user_name_details);
	localStorage.setItem("instagram_details", instagram_details);
	localStorage.setItem("email_details", email_details);
	localStorage.setItem("website_details", website_details);
});

if ( $( "#upload-form" ).length ) {
	$('#upload-form #email').val(localStorage.getItem("email_details"));
}

if ( $( ".um-register.um-8383" ).length ) {
	$('.um-register.um-8383 #user_login-8383').val(localStorage.getItem("user_name_details"));
	$('.um-register.um-8383 #first_name-8383').val(localStorage.getItem("first_name_details"));
	$('.um-register.um-8383 #last_name-8383').val(localStorage.getItem("last_name_details"));
	$('.um-register.um-8383 #user_email-8383').val(localStorage.getItem("email_details"));
	$('.um-register.um-8383 #social_instagram-8383').val(localStorage.getItem("instagram_details"));
	$('.um-register.um-8383 #social_website-8383').val(localStorage.getItem("website_details"));
}

//toggle filter tags section
$(document).on('click' , '.collection-tags-toggle' , function (){
	$('.collection-filters-tags').slideToggle();
	$('.collection-tags-toggle i').toggleClass('fa-caret-down  fa-caret-up');
});

/* UM Login form - Check "Remember me"  */
$(".um-login .um-field-checkbox").addClass('active');
$(".um-login .um-field-checkbox input[type='checkbox']").prop( "checked", true );
$(".um-login .um-field-checkbox .um-field-checkbox-state i").attr("class","um-icon-android-checkbox-outline");







//close red edit banner on click , ajax in functions
$(document).on('click' , '.close-logged-in-banner' , function (){
	console.log('123');
	$('body .looged-in-banner').hide();
	$("body").removeClass('incomplete-profile');
	$.ajax({
		url: my_ajax_object.ajaxurl,
		type: 'post',
		data:{ action: 'hide_edit_banner'},
		success: function(result){
		}
	});
});







//filter tags collection ajax in functions
//$(document).on('click' , '.collection-filter-label' , function (){
//	$('#collection_search').val('')
//	$('.collection-search-container .clear-search').remove();
//	if($(this).hasClass('collection-filter-all')){
//		clearSearch();
//		return;
//	}
//	var tagSlug = $(this).attr('for');
//	$('.ns-collection div .col-sm-4').each(function(el){
//		var tags = $(this).attr('data-terms');
//		if( tags && tags.indexOf(tagSlug) !== -1){
//			$(this).show();
//		}else{
//			$(this).hide();
//		}
//		$('.butrow').hide();
//
//	});
//});








//autocomplete collection search
//if($("#data-search").length){
//	var availableNames 	= $('#data-search').attr('data-search');
//	availableNames 		= $.parseJSON(availableNames);
//	availableNames.sort(function(a, b) {
//		return a.localeCompare(b) || a.length - b.length  ;
//	});
//	$( "#collection_search" ).autocomplete({
//		source: function(request, response) {
//			var results = $.ui.autocomplete.filter(availableNames, request.term);
//			response(results.slice(0, 5));
//		},
//		minLength: 2,
//		appendTo: ".collection-search-container",
//
//	});
//}







////clear search collection
//$('.collection-search-container').on('click' , '.clear-search' , function (){
//	clearSearch();
//});
//
//
//
//
//
//
////search collection ajax in functions
//$('body').on('click' , '.ui-menu-item-wrapper' , function (e){
//	var stringSearch = $(this).text();
//	$('.collection-filters-tags input').prop('checked', false);
//	$('.collection-search-container .clear-search').remove();
//	$('.ns-collection div .col-sm-4').each(function(el){
//		var search = $(this).attr('data-search');
//		if( search && search.indexOf(stringSearch) != -1){
//			$(this).show();
//		}else{
//			$(this).hide();
//		}
//		$('.butrow').hide();
//
//	});
//	$('.collection-search-container').append('<span class="fa fa-times-thin clear-search"></span>');
//
//});
//
//
//
//
//
//
////clear search
//function clearSearch(){
//	var limit = parseInt($('#loadmore').attr('data-limit'));
//	$('.ns-collection .col-user').hide();
//	$('.collection-filters-tags input').prop('checked', false);
//	$('.collection-search-container .clear-search').remove();
//	$('#collection_search').val('')
//	$('#loadmore').attr( 'data-page' , '2' );
//	$('.ns-collection .col-user:lt(' + limit + ')').show();
//	if($('.ns-collection .col-user').length > limit){
//		$('.butrow').show();
//	}
//}


//search collection ajax in functions
$('body').on('click' , '.ui-menu-item-wrapper' , function (e){
	var stringSearch = $(this).text();
	var users = JSON.parse($('.ns-collection').attr('data-users'));
	$.ajax({
		url	: my_ajax_object.ajaxurl,
		type: 'GET',
		data:{
			action: 'swpsh_collection_filter',
			//page: page,
			users: users,
			limit: 21,
			stringSearch:stringSearch,
		},
		beforeSend: function (){
			$('.filter-loading').css('visibility', 'visible');
		},
		success: function(result){
			//$('#loadmore').attr('data-page' , page );
			$("#collection-container .fusion-column-wrapper .fusion-text").empty().append(result);
			$(".collection-search-container").append('<span class="fa fa-times-thin clear-search"></span>');
			collection_count_users_column();
			//if(page * limit > allCount){
			//	$('#loadmore').hide();
			//}
			//$('#collection-container .fusion-column-wrapper .ns-collection .row:first-child').append(result);
		},
		complete:function(){
			$('.filter-loading').css('visibility', 'hidden');
			$('.filter-all').show();
		}
	});

});






if($("#data-search").length){
	//var availableNames 	= $('#data-search').attr('data-search');
	//availableNames 		= $.parseJSON(availableNames);
	//availableNames.sort(function(a, b) {
	//	return a.localeCompare(b) || a.length - b.length  ;
	//});
	$( "#collection_search" ).autocomplete({
		source: function(request, response){
			var stringSearch = $("#collection_search").val();
			var users = JSON.parse($('.ns-collection').attr('data-users'));
			//if (!force && stringSearch.length < 3) return; //wasn't enter, not > 2 char
			$.ajax({
				url	: my_ajax_object.ajaxurl,
				type: 'GET',
				dataType: "json",
				data:{
					action: 'wpsh_collection_autocomplete',
					stringSearch : stringSearch,
					users : users
				},
				beforeSend: function (){
					$('.filter-loading').css('visibility', 'visible');
				},
				success: function(result){
					response($.map( result, function(item) {
						return {
							label: item.display_name,
							value: item.display_name,
						}
					}));
					console.log('success2');
				},
				complete:function(){
					$('.filter-loading').css('visibility', 'hidden');
					$('.filter-all').show();
					console.log('complete2');
				}
			});
		},
		minLength: 2,
		appendTo: ".collection-search-container",
		delay: 10

	});
}



//actual collection search function triggered above
//function searchCollection(stringSearch) {
//	//var stringSearch = stringSearch;
//	var stringSearch = $("#collection_search").val();
//	//if (!force && stringSearch.length < 3) return; //wasn't enter, not > 2 char
//	$.ajax({
//		url	: my_ajax_object.ajaxurl,
//		type: 'GET',
//		dataType: "json",
//		data:{
//			action: 'wpsh_collection_autocomplete',
//			stringSearch : stringSearch
//		},
//		beforeSend: function (){
//			//$('.filter-loading').css('visibility', 'visible');
//		},
//		success: function(result){
//			response($.map( data, function(item) {
//					return {
//						label: item.label,
//						value: item.label,
//					}
//			}));
//		},
//		complete:function(){
//			$('.filter-loading').css('visibility', 'hidden');
//			$('.filter-all').show();
//		}
//	});
//}


function back_img_samesize(){
	var back_img = jQuery(".back-image").closest(".fusion-builder-row").find("img:visible").first();
	if(back_img.length){
		console.log(back_img);
		var back_img_height = back_img.height();
		var back_img_width = back_img.width();

		jQuery(".back-image, .front-text.winner ").css({
			"width" : back_img_width+"px",
			"height" : back_img_height+"px",
		})
	}
}

back_img_samesize();

setInterval('cycleImages()', 3000);

$('#cycler').width($(window).width());

$(window).resize(function() {
	$('#cycler').width($(window).width());
	if (jQuery(window).width() > 640) {
		x = 2.7;
	}
	else {
		x = 1.42;
	}
	var newheight =$(window).width()/x;
	$('#cycler').height(newheight);

	back_img_samesize();
});








//load more instant users on collection page
//$('body').on('click', '#loadmore', function () {
//
//	var limit = parseInt($(this).attr('data-limit'));
//	var page  = parseInt($(this).attr('data-page'));
//	var users = $('.ns-collection .row div.col-sm-4').length;
//	var items = page * limit;
//
//	//hide load more button if there are no more users to show
//	if(items >= users){
//		$('.butrow').hide();
//	}
//
//	$('.ns-collection .row div.col-sm-4:lt('+items+') ').show();
//	$('#loadmore').attr( 'data-page' , page + 1 );
//
//});

$('body').on('click', '#loadmore', function () {
	var page = parseInt($(this).attr('data-page')) + 1;
	var users = JSON.parse($(this).attr('data-users'));
	var allCount =  parseInt($(this).attr('data-count'));
	var limit = parseInt($(this).attr('data-limit'));
	$.ajax({
		url	: my_ajax_object.ajaxurl,
		type: 'GET',
		data:{
			action: 'swph_load_more',
			page: page,
			users: users,
			limit: limit,
		},
		beforeSend: function (){
			$('.filter-loading-loadmore').css('visibility', 'visible');
		},
		success: function(result){
			$('#loadmore').attr('data-page' , page );
			$("#collection-container .fusion-column-wrapper .ns-collection .row:first-child .col-user:last").after(result);
			collection_count_users_column();
			if(page * limit > allCount){
				$('#loadmore').hide();
				$(".collection-progress").css('margin-bottom','40px');
			}
			//$('#collection-container .fusion-column-wrapper .ns-collection .row:first-child').append(result);
		},
		complete:function(){
			$('.filter-loading-loadmore').css('visibility', 'hidden');
		}
	});

});







$('body').on('click', '.collection-filter-label', function () {
	//var page = parseInt($('#loadmore').attr('data-page')) + 1;
	var users = JSON.parse($('.ns-collection').attr('data-users'));
	//var allCount =  parseInt($('#loadmore').attr('data-count'));
	var limit = parseInt($('#loadmore').attr('data-limit'));
	var tagslug = $(this).parent().find('input').val();
	$.ajax({
		url	: my_ajax_object.ajaxurl,
		type: 'GET',
		data:{
			action: 'swpsh_collection_filter',
			//page: page,
			users: users,
			limit: 21,
			tagSlug:tagslug,
		},
		beforeSend: function (){
			$('.filter-loading').css('visibility', 'visible');
		},
		success: function(result){
			//$('#loadmore').attr('data-page' , page );
			$("#collection-container .fusion-column-wrapper .fusion-text").empty().append(result);
			collection_count_users_column();
			//if(page * limit > allCount){
			//	$('#loadmore').hide();
			//}
			//$('#collection-container .fusion-column-wrapper .ns-collection .row:first-child').append(result);
		},
		complete:function(){
			$('.filter-loading').css('visibility', 'hidden');
			$('.filter-all').show();
		}
	});

});




	$('body').on('click', '.clear-search', function () {
		//var page = parseInt($('#loadmore').attr('data-page')) + 1;
		var users = JSON.parse($('.ns-collection').attr('data-users'));
		//var allCount =  parseInt($('#loadmore').attr('data-count'));
		var limit = parseInt($('#loadmore').attr('data-limit'));
		$.ajax({
			url	: my_ajax_object.ajaxurl,
			type: 'GET',
			data:{
				action: 'swpsh_collection_filter',
				users: users,
				limit: 21,
			},
			beforeSend: function (){
				$('.filter-loading').css('visibility', 'visible');
			},
			success: function(result){
				//$('#loadmore').attr('data-page' , page );
				$("#collection-container .fusion-column-wrapper .fusion-text").empty().append(result);
				collection_count_users_column();
				//if(page * limit > allCount){
				//	$('#loadmore').hide();
				//}
				//$('#collection-container .fusion-column-wrapper .ns-collection .row:first-child').append(result);
			},
			complete:function(){
				$('.filter-loading').css('visibility', 'hidden');
				$('.filter-all').show();
			}
		});

	});


	function collection_count_users_column(){
		var col_user = $('.col-user').length;
		var total_user = $('#data_user_count').val();
		$('.ns-collection .loaded-profiles').html(col_user);
		$('.ns-collection .total-profiles').html(total_user);
		var percent_users = col_user*100/total_user;
		$('.collection-progressbar').attr('aria-valuenow',percent_users).css('width',percent_users+'%');
	}

	if($('.ns-collection.um-form').length){
		collection_count_users_column();
	}







$( "[data-is-sortable]" ).sortable({
	update: function( event, ui ) {
		var items = $.map($(this).find('.ui-sortable-handle'), function(el) {
			var _el = $(el);
			var sortData = JSON.parse(_el.attr('data-ns-sort'));
			var menu_order = _el.index();

			sortData.menu_order = menu_order;

			_el.attr('data-ns-sort', JSON.stringify(sortData));

            return sortData;
        });

        console.log('123');
        console.log(items);

		jQuery.ajax({
			type: 'post',
			url: my_ajax_object.ajaxurl,
			data:{
				action: 'ns_order_user_collection_images',
				items: items,
				security: um_gallery_config.nonce
			},
			beforeSend: function(){
				$('.sortable-loading').fadeIn();
				$('.sortable-loading .bubblingG').css({
					position : 'fixed',
					top: '50%',
					marginTop: -25,
					left: '50%',
					marginLeft: -40,
				});
			},
			success: function(res){
				console.log(res);
			},
			complete: function(){
				$('.sortable-loading').hide();
			}
		});
	},
	helper: 'clone',
});
$( "[data-is-sortable]" ).disableSelection();

$('[data-is-sortable]').on('update', function () {
	var items = $.map($(document).find('.ui-sortable-handle'), function(el) {
		var _el = $(el);
		var sortData = JSON.parse(_el.attr('data-ns-sort'));
		var menu_order = _el.index();

		sortData.menu_order = menu_order;

		_el.attr('data-ns-sort', JSON.stringify(sortData));

		return sortData;
	});


	jQuery.ajax({
		type: 'post',
		url: my_ajax_object.ajaxurl,
		data:{
			action: 'ns_order_user_collection_images',
			items: items,
			security: um_gallery_config.nonce
		},

		success: function(res){
			// console.log(res);
		},
		complete: function(){

		}
	});
});


$('body').on('click', '#um-gallery-save', function () {
	var album_name = $("#album_name").val();
	var album_description = $("#album_description").val();
	$(".um-gallery-album-head .um-gallery-album-title").html(album_name);

	if( $(".um-gallery-album-head .um-gallery-album-description").length ){
		$(".um-gallery-album-head .um-gallery-album-description").html(nl2br(album_description));
	}
	else{
		$(".um-gallery-album-head .um-gallery-album-title").after('<div class="um-gallery-album-head um-gallery-album-description">'+nl2br(album_description)+'</div>');
	}


});

$('.malinky-ajax-pagination-loading').html('<div class="fusion-loading-container fusion-clearfix"><div class="fusion-loading-spinner"><div class="fusion-spinner-1"></div><div class="fusion-spinner-2"></div><div class="fusion-spinner-3"></div></div><div class="fusion-loading-msg"><em>Loading the next set of posts...</em></div></div>');


/* Profile - trigger click on photo automatically to change */
$('.um-profile:not(.um-viewing) .um-profile-photo-img').on('click', function(){
	$( ".um-profile-photo .um-dropdown .um-manual-trigger" ).trigger( "click" );
});

/* Same for Cover photo */
$('.um-cover .um-cover-overlay-s ins, .profile-header-cover').on('click', function(){
// $('body').on('click', '.um-trigger-menu-on-click', function () {
	$( ".um-cover .um-manual-trigger" ).trigger( "click" );
});


/**
 * save artist "about section"
 */
	function ns_save_about_section (elem, processing) {
	    var description = elem.val(),
			user_id = elem.data('user-id'),
			show_res = elem.parent().find('[data-ns-res]');

		if(is_saving_about){
			return;
		}
		is_saving_about = true;

		jQuery.ajax({
			type: 'post',
			url: my_ajax_object.ajaxurl,
			data:{
				action: 'ns_update_about_you',
				description: description,
				user_id: user_id,
				security: um_gallery_config.nonce
			},
			beforeSend: function(){
				show_res.show().text('Saving...');
			},
			success: function(res){
				show_res.text(res);

				setTimeout(function(){
					show_res.fadeOut();
				}, 1500);
			},
			complete: function(){
				is_saving_about = false;
				var redirect_after_save = $(".save-changes-photographer").attr("data-href");
				window.location.href = redirect_after_save;
			}
		});
	}

	// var typingTimer;
	// var doneTypingInterval = 5000;
	var is_saving_about = false;

	//on keyup, start the countdown
	// $('#um-meta-bio').keyup(function(){
	// 	var _this = $(this);

	//     clearTimeout(typingTimer);
	//     var callback = function (){
	//     	ns_save_about_section(_this, is_saving_about);
	//     };

	//     if ($('#um-meta-bio').val()) {
	//         typingTimer = setTimeout(callback, doneTypingInterval);
	//     }
	// });
	//on blur
	// $('#um-meta-bio').on('blur', function(){
	// 	ns_save_about_section($(this), is_saving_about);
	// });
	$(".save-changes-photographer").on('click', function(){
		ns_save_about_section($("#um-meta-bio"), is_saving_about);
	});


/**
 * init custom iLightBox
 */
 	var lightbox_skin = $("#photographer-album").attr("data-lightbox");
	var c_iLightBox = jQuery('[data-ns-rel]').iLightBox({
		path: 'horizontal',
		skin: lightbox_skin,
		controls: {
			arrows: true
		}
	});

	//add action to refresh iLightBox when a new photo is uploaded or deleted
	PubSub.subscribe( 'refresh_iLightBox', function(msg, data){
		c_iLightBox.refresh();
		// console.log(data);
	});


/**
 * delete big file from upload modal
 */
	$(document).on('click', '.dz-preview', function(){
		var _this = $(this);

		if(_this.hasClass('dz-error')){
			var parent = _this.parent();

			if(parent.find('.dz-preview').length  == 1){
				_this.parent().removeClass('dz-started');
			}
			_this.remove();
		}
	});


/**
 * edit caption
 */
	var is_saving_caption = false;
	$(document).on('click', '.ns_um-gallery-caption-edit', function(e){
		e.preventDefault();

		var _this = $(this),
			id = _this.attr('data-id'),
			parent = _this.parent(),
			edit_wrapper = parent.find('.ns-edit-wrapper'),
			caption = edit_wrapper.find('.ns-caption').val();


		if(_this.hasClass('ns-open')){

			if(is_saving_caption){
				return;
			}

			is_saving_caption = true;

			var default_caption = um_gallery_images[id].caption;

			jQuery.ajax({
				type: 'post',
				url: my_ajax_object.ajaxurl,
				data: {
					'action': 'um_gallery_photo_update',
					'id': id,
					'album_id': um_gallery_config.album_id,
					'caption' : caption,
					'default_caption' : default_caption,
					'description' : '',
					'security': um_gallery_config.nonce
				},
				cache: false,
				beforeSend: function() {
					_this.find('span').text('Saving...');
					_this.removeClass('ns-open');
					parent.css({'bottom': 10});
					edit_wrapper.hide();
				},
				success: function(response) {

					_this.find('span').text('Edit Caption');
					_this.closest('.um-gallery-inner').find('[data-caption]').attr('data-caption', caption);
				},
				complete: function(){
					is_saving_caption = false;
				}
			});

		}else{

			if(is_saving_caption){
				return;
			}

			_this.find('span').text('Save Caption');
			_this.addClass('ns-open');
			parent.css({'bottom': -10});
			edit_wrapper.fadeIn();
		}

	});

	/* Update profile social links */
	var is_saving_social = false;
	$("#um_account_social_submit").on('click',function(){
		$(".social_error_msg").remove();
		$(this).text('Updating...');
		var user_id = $(this).attr("data-userid");
		var ns_social_nonce = $("#ns_update_social_nonce_field").val();
		console.log(ns_social_nonce);
		if(is_saving_social){
			return;
		}

		is_saving_social = true;

		jQuery.ajax({
				type: 'post',
				url: my_ajax_object.ajaxurl,
				dataType : "json",
				data: {
					'action': 'ns_update_user_social_links',
					'user_id': user_id,
					'social_facebook_username': $("#social_facebook_username").val(),
					'social_instagram_username': jQuery("#social_instagram_username").val(),
					'social_website_custom': jQuery("#social_website_custom").val(),
					'_ns_update_social_security': ns_social_nonce
				},
				cache: false,
				success: function(response) {
					if(response.website_error !=''){
						$(".um-field-social_website").append('<div class="um-field-error social_error_msg"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>'+response.website_error+'</div>');
					}

					if(response.facebook_error !=''){
						$(".um-field-social_facebook").append('<div class="um-field-error social_error_msg"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>'+response.facebook_error+'</div>');
					}

					if(response.instagram_error !=''){
						$(".um-field-social_instagram").append('<div class="um-field-error social_error_msg"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>'+response.instagram_error+'</div>');
					}

				},
				complete: function(){
					is_saving_social = false;
					$("#um_account_social_submit").text("Update Account");
				}
			});


	});



	/* Create new saving for general tab */
		/* Update profile social links */
	var is_saving_general = false;
	$("#um_account_general_submit").on('click',function(){
		console.log('1');
		$(".general_error_msg").remove();
		$(".um-general-success").remove();

		$(this).text('Updating...');
		var user_id = $(this).attr("data-userid");
		var current_email = $(this).attr("data-current-email");
		var ns_general_nonce = $("#ns_update_general_nonce_field").val();
		console.log(ns_general_nonce);
		if(is_saving_general){
			console.log('is saving');
			return;
		}

		console.log('execute ajax request');

		is_saving_general = true;

		jQuery.ajax({
				type: 'post',
				url: my_ajax_object.ajaxurl,
				dataType : "json",
				data: {
					'action': 'ns_update_user_general_info',
					'user_id': user_id,
					'current_email' : current_email,
					'first_name': $("#general_firstname").val(),
					'last_name': $("#general_lastname").val(),
					'email_address': $("#general_email").val(),
					'_ns_update_general_security': ns_general_nonce
				},
				cache: false,
				success: function(response) {

					console.log(response);
					if(response.firstname_error !=''){
						$(".um-field-general_firstname").append('<div class="um-field-error general_error_msg"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>'+response.firstname_error+'</div>');
					}

					if(response.lastname_error !=''){
						$(".um-field-general_lastname").append('<div class="um-field-error general_error_msg"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>'+response.lastname_error+'</div>');
					}

					if(response.email_error !=''){
						$(".um-field-general_email").append('<div class="um-field-error general_error_msg"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>'+response.email_error+'</div>');
					}


					if(!(response.error)){
						console.log('ok');
						jQuery(".um-account-tab-general-info").prepend('<p class="um-notice success um-general-success"><i class="um-icon-ios-close-empty" onclick="jQuery(this).parent().fadeOut();"></i>Your account was updated successfully.</p>');

						$("#um_account_general_submit").attr('data-current-email',$("#general_email").val());


					}
					else{
						console.log('not ok');
					}

					// if(response.facebook_error !=''){
					// 	$(".um-field-social_facebook").append('<div class="um-field-error social_error_msg"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>'+response.facebook_error+'</div>');
					// }

					// if(response.instagram_error !=''){
					// 	$(".um-field-social_instagram").append('<div class="um-field-error social_error_msg"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>'+response.instagram_error+'</div>');
					// }

				},
				complete: function(response){
					console.log(response)
					is_saving_general = false;
					$("#um_account_general_submit").text("Update Account");
				}
			});


	});






	/* Fix Profile Accordeon "Unable to preventDefault inside passive event listener due to target being treated as passive."
	on mobile devices */

	$(".um-account-main .um-account-nav.uimob500-show").on("click",function(){
		// return false;
	});

	$(".front-text.winner").on("click",function(){
		var winner_url = $(this).find('h4 a').attr("href");
		window.location.replace(winner_url);
		console.log(winner_url);
	});

	$(".sendmail-feedback").on('click',function(){
		jQuery.ajax({
					type: 'post',
					url: my_ajax_object.ajaxurl,
					data: {
						'action'    : 'send_pop_email',
						'url_user'  : $('.url_user').val(),
						'user_name' : $('.user_name').val(),
						'user_id'   : $('.user_id').val(),
						'user_email': $('.user_email').val(),
					},
					cache: false,
					beforeSend: function() {
						//_this.find('span').text('Saving...');
					},
					success: function(response) {
						console.log(response);
						if (response=='success') {
							$(".initial_text_feed").text('Thank you.');
							$('.txtfeedrqs').text('Thanks for submitting your Profile for feedback. Your request has been safely received, and we will return our critique to you by email. This may take some time, but please rest assured we have not forgotten.');
							$('.request-feedback').closest('.fusion-button-wrapper').hide();
							$('.sendmail-feedback').hide();
						}
						else {
							$(".initial_text_feed").text('An error occured: your message was not sent.');
							$('.txtfeedrqs').text('An error occured: your message was not sent.').css('margin-top','30px');
						}
						//_this.find('span').text('Edit Caption');
						//_this.closest('.um-gallery-inner').find('[data-caption]').attr('data-caption', caption);
					},
					complete: function(){
						//is_saving_caption = false;
					}
				});
		});

	$(".sendmail-feature").on('click',function(){
		jQuery.ajax({
					type: 'post',
					url: my_ajax_object.ajaxurl,
					data: {
						'action'    : 'send_feature_pop_email',
						'url_user'  : $('.url_user').val(),
						'user_name' : $('.user_name').val(),
						'user_id'   : $('.user_id').val(),
						'user_email': $('.user_email').val(),
					},
					cache: false,
					beforeSend: function() {
						//_this.find('span').text('Saving...');
					},
					success: function(response) {
						console.log(response);
						if (response='success') {
							$(".initial_text_feat").text('Thank you.');
							$('.txtfeatrqs').text('Thank you very much for submitting your Life Framer Profile. We enjoy reviewing every Profile, and while we can\'t include every single one in The Collection it is still a portfolio for you to use, and we encourage you to share the link on your social media and with your friends and contacts. If your Profile is selected for The Collection, we will of course let you know.');
							$('.request-feature').closest('.fusion-button-wrapper').hide();
							$('.sendmail-feature').hide();
						}
						else {
							$(".initial_text_feat").text('Your message was not sent.');
						}
						//_this.find('span').text('Edit Caption');
						//_this.closest('.um-gallery-inner').find('[data-caption]').attr('data-caption', caption);
					},
					complete: function(){
						//is_saving_caption = false;
					}
				});
		});

	$(".sendmail-series").on('click',function(){
		jQuery.ajax({
					type: 'post',
					url: my_ajax_object.ajaxurl,
					data: {
						'action'    : 'send_series_pop_email',
						'url_user'  : $('.url_user').val(),
						'user_name' : $('.user_name').val(),
						'user_id'   : $('.user_id').val(),
						'user_email': $('.user_email').val(),
					},
					cache: false,
					beforeSend: function() {
						//_this.find('span').text('Saving...');
					},
					success: function(response) {
						console.log(response);
						if (response='success') {
							$(".initial_text_series").text('Thank you.');
							$('.txtseriesrqs').text('Thank you very much for your submission to the Series Award. Your submission found us well... The winner will be selected by our guest judge shortly after the end of the award and you can expect and announcement before mid-december 2020. We will contact the grand winner and honorary mentions shortly before the announcement. Thank you and good luck!');
							$('.request-series').closest('.fusion-button-wrapper').hide();
							$('.sendmail-series').hide();
						}
						else {
							$(".initial_text_series").text('Your message was not sent.');
						}
						//_this.find('span').text('Edit Caption');
						//_this.closest('.um-gallery-inner').find('[data-caption]').attr('data-caption', caption);
					},
					complete: function(){
						//is_saving_caption = false;
					}
				});
		});

	$(".get_included_mail").on('click',function(){
	jQuery.ajax({
				type: 'post',
				url: my_ajax_object.ajaxurl,
				data: {
					'action'    : 'send_pop_email_get_included',
					'url_user'  : $('.url_user').val(),
					'user_name' : $('.user_name').val(),
					'user_id'   : $('.user_id').val(),
					'user_email': $('.user_email').val(),
				},
				cache: false,
				beforeSend: function() {
					//_this.find('span').text('Saving...');
				},
				success: function(response) {
					console.log(response);
					if (response='success') {
						$(".text_getincluded").text('Thank you.');
					}
					else {
						$(".text_getincluded").text('Your message was not sent.');
					}
					//_this.find('span').text('Edit Caption');
					//_this.closest('.um-gallery-inner').find('[data-caption]').attr('data-caption', caption);
				},
				complete: function(){
					//is_saving_caption = false;
				}
			});
	});

	$('body').on('click' , '.past-editions-holder .mejs-overlay-button' , function (e){
		$(".past-editions-holder .mejs-container .mejs-controls").css('opacity','1');
		console.log('11');
	})

	if($('#lf-float-btn').length){
		if(!$('body').hasClass('logged-in')){
			$('#lf-float-btn').css('display','block');
			$('body').addClass('has-float-button');
		}
	}

	/* Remove showing title of the social images  */
	$(".fusion-social-network-icon").removeAttr('title');


	/* UM Gallery Pro LF custom delete photo - remove the window confirmation */
	jQuery(document).on("click",".ns_um-gallery-delete-item", function(event){
		event.preventDefault();
		var id = jQuery(this).data("id");
		var obj = jQuery(this);
			jQuery.ajax({
			  method: "POST",
			  url: um_gallery_config.ajax_url,
			  data: { action: "sp_gallery_um_delete", id: id, 'album_id': um_gallery_config.album_id},
			  success: function(result){
					obj.closest('.um-gallery-item').slideUp().remove();
			  }
			})
	});



	function preventFormSubmittionBeforeShowingLoader(e) {
		e.preventDefault();
		//no longer needed
		jQuery('.um-register form').off('submit', preventFormSubmittionBeforeShowingLoader);
		jQuery('.register-loader').show();

		var form = this;
		//delay to show the message and animation
		setTimeout(function() {
			jQuery(form).submit();
		}, 500);

		return false;
	}
	jQuery('.um-register form').on('submit', preventFormSubmittionBeforeShowingLoader);

	jQuery(window).on('resize', function() {
		jQuery('#mobile-menu-modal').css('margin-top', jQuery('header.fusion-header-wrapper').height());
	}).trigger('resize');

});



window.UM_Gallery_Pro = {};
( function( window, $, app ) {

	//window.UM_Gallery_Pro._um_gallery_album_save - accessing property

	jQuery(document).on("click", "#um-gallery-save", function(event){
		event.preventDefault();
		var id = $(this).data('id');
		var type = $(this).data('type');

		var btn = $(this);
		if( type === 'album' ){
			btn.text( 'Saving...' );
		}


	});


	// jQuery([document.documentElement, document.body]).animate({
    //     scrollTop: jQuery("#pastuploads").offset().top-200
	// }, 1000);
	var url_string =window.location.href;
	var url = new URL(url_string);
	if( url.searchParams.get("um_action")=='edit' && url.searchParams.get("change")=='profile_img' ){

		if(url_string.indexOf("#um-profilephoto") !== -1){
			jQuery([document.documentElement, document.body]).animate({
				scrollTop: jQuery("#um-profilephoto").offset().top-150
			}, 1000);

			setTimeout(function(){
				jQuery( ".um-profile-photo-img" ).trigger( "click" );
			}, 1000);

		}

	}



})( window, jQuery, window.UM_Gallery_Pro );

jQuery(document).ready(function() {
	jQuery('header li.menu-item-has-children>a').each(function() {
		jQuery(this).attr('href', 'javascript:void(0);').css('cursor', 'default');
	});

	// lf_event_expanded
	jQuery('[data-lf-event-expanded-id]').parents('.fusion-fullwidth').hide();
	jQuery('[data-lf-event-expander-id]').on('click', function() {
		var id = jQuery(this).data('lf-event-expander-id');
		var expandedPair = jQuery("[data-lf-event-expanded-id='" + id + "']");
		window.dummy = this;
		if (!expandedPair.length || jQuery(this).parents('.lf-event-expander-expanded').length) {
			return;
		}
		jQuery(this).parents('.lf-event-expander').addClass('lf-event-expander-expanded');

		expandedPair.parents('.fusion-fullwidth').show();
	});
	jQuery('[data-lf-event-expanded-id]').on('click', function() {
		var id = jQuery(this).data('lf-event-expanded-id');
		var pair = jQuery("[data-lf-event-expander-id='" + id + "']")
		pair.parents('.lf-event-expander').removeClass('lf-event-expander-expanded');
		jQuery(this).parents('.fusion-fullwidth').hide();
	});

	jQuery('#profile-iframe').load(um_var.um_user_profile_url +' #wrapper', function() {
		setTimeout(function(){
			jQuery(window).trigger('resize');
    	}, 1000);
	  });

	jQuery('.read-more-expander').on('click', function(e) {
		e.preventDefault();

		jQuery(this).hide().closest('.fusion-text').find('.read-more-content').show();
	});

});


