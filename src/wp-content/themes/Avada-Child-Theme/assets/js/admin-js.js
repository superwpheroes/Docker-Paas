jQuery(function($) {
	
  var is_processing = is_processing_split = false;
  var success_response = success_response_split = false;
  
  /* Delete image */
  $(".delete-img-sub").on("click",function(){

  	if(is_processing) return;
	is_processing = true;
	  
  	$(".images-submitted").css({
  		'opacity':'0.7',
  		'pointer-events' : 'none'
  	});

  	var image_deleted = $(this);
  	var entry_id = $(this).attr('data-entry-id');
  	var photo_id = $(this).attr('data-photo-id');
  	var user_id = $(this).attr('data-user-id');

  	var normal = parseInt($('.left_to_submit').html());
  	var extra = parseInt($('#extra_entry_imgs').val());
  	var new_normal = parseInt(normal);
  	var new_extra = parseInt(extra);
  	console.log('new normal '+new_normal);
  	console.log('new extra '+new_extra);

  	jQuery.ajax({
			url : ajaxurl,
			type : 'POST',
			data : {
				action   : 'delete_entry_image',
				entry_id : entry_id,
				photo_id : photo_id,
				user_id  : user_id,
				security : $("#delete_img_sub-ajax-nonce").val()

			},
			success: function(response,status,xhr){
		        success_response = true;
		        console.log(response);
		        response = JSON.parse(response);
		        console.log(response);
		        if(response.error == ''){
		        	image_deleted.parent('.submittion-image-holder').parent('.submittion-image').remove();
			        if(response.extra =='1'){
			        	console.log('extra');
			        	new_extra = parseInt(extra) + parseInt(1);
			        	$("#extra_entry_imgs").val(new_extra);
			        }
			        if(response.normal =='1'){
			        	console.log('normal');
			            new_normal = parseInt(normal) + parseInt(1);
			        	$('.left_to_submit').html(new_normal);
			        }
			        var new_total = parseInt(new_extra)+parseInt(new_normal);
			        console.log(new_extra);
			        console.log(new_normal);
			        console.log(new_total);
			        $('.total_entry_imgs').html(new_total);

		        }
		        else{
		        	console.log('eeerrr');
		        }



		        is_processing = false;
		    },
		    error: function(xhr, status, error){
		        alert("Error occured !!" + xhr.status);
		        is_processing = false;
		        $(".images-submitted").css({
			  		'opacity':'1',
			  		'pointer-events' : 'auto'
			  	});
		    },
		    complete: function(){
		        if(!success_response){
		             alert('Error occured !');
		        }
		        $(".images-submitted").css({
			  		'opacity':'1',
			  		'pointer-events' : 'auto'
			  	});
		        is_processing = false;
		    },
	});

  });


  /* Change image theme name */
  
  $(".change-img_name").on("click",function(){
	  var change_img_btn = $(this);

	  /* Hide Change Theme btn */
	  change_img_btn.hide();

	  /* Make image name editable */
	  change_img_btn.parent().find('.entry-img_name').removeAttr('disabled');

	  /* Display Save Theme btn */
	  change_img_btn.parent().find('.save-img_name').css('display','block');

  });

var is_save_processing = false;
var success_save = false;

$(".save-img_name").on("click",function(){
	if(is_save_processing) return;
	is_save_processing = true;
	
	
	var save_img_btn = $(this);

	var new_img_name = save_img_btn.parent().find('.entry-img_name').val();
	var photo_id = save_img_btn.attr('data-photo-id');
	
	console.log(new_img_name);
	save_img_btn.parent('.image-info').css({
		'opacity':'0.7',
		'pointer-events':'none',
	});

	jQuery.ajax({
		url : ajaxurl,
		type : 'POST',
		data : {
			action   : 'lf_change_img_name',
			new_img_name : new_img_name,
			photo_id : photo_id,
			security : $("#delete_img_sub-ajax-nonce").val()

		},
		success: function(response,status,xhr){
			success_response = true;
			console.log(response);
			response = JSON.parse(response);

			if(response.error == ''){
				save_img_btn.parent().find('.entry-img_name').val(response.output);
			}
			else{
				save_img_btn.parent('image-info').append('<p class="error">'+response.error+'</p>');
			}





			is_save_processing = false;
		},
		error: function(xhr, status, error){
			// alert("Error occured !!" + xhr.status);
			is_save_processing = false;
			// $(".images-submitted").css({
			// 	  'opacity':'1',
			// 	  'pointer-events' : 'auto'
			//   });
		},
		complete: function(){
			// if(!success_response){
			// 	 alert('Error occured !');
			// }
			// $(".images-submitted").css({
			// 	  'opacity':'1',
			// 	  'pointer-events' : 'auto'
			//   });
			is_save_processing = false;
			save_img_btn.hide();
			save_img_btn.parent().find('.change-img_name').show();
			save_img_btn.parent('.image-info').css({
				'opacity':'1',
				'pointer-events':'auto',
			});
			save_img_btn.parent().find('.entry-img_name').attr('disabled','disabled');
		},
	});


});

	$(".split-entries-btn").on("click",function(){
		if(is_processing_split) return;
	  	is_processing_split = true;
	  	$(".split-entries-result").html('');

		var theme_name = $("#theme_name").val();
		var theme_start_date = $("#theme_start").val();
		var theme_end_date = $("#theme_end").val();

		  	jQuery.ajax({
			url : ajaxurl,
			type : 'POST',
			data : {
				action   : 'split_theme_entries',
				theme_name : theme_name,
				theme_start_date : theme_start_date,
				theme_end_date : theme_end_date

			},
			success: function(response,status,xhr){
		        success_response_split = true;
		        console.log(response);
		        $(".split-entries-result").html(response);
		        is_processing_split = false;
		    },
		    error: function(xhr, status, error){
		        alert("Error occured !!" + xhr.status);

		        is_processing_split = false;

		    },
		    complete: function(){
		        if(!success_response_split){
		             alert('Error occured !');
		        }
		        $(".images-submitted").css({
			  		'opacity':'1',
			  		'pointer-events' : 'auto'
			  	});
		        is_processing_split = false;
		    },
		});		
	});



	

	// edit-split-entries-btn 

	$("#theme_name, #theme_start, #theme_end").on('input',function(){
		$(".split-entries-result").html('');
	})







});
