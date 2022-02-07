
jQuery(document).ready(function ($) {

	var max_uploaded_width = 2500;
	var max_uploaded_height = 1667;
	var max_uploaded_quality = 0.9;


	var already_uploaded, all_files;
	var already_uploaded_per_theme = [0,0,0];
	

	function reCalculate(role){
		console.log ('max_files = '+max_files);
		// console.log ('already_uploaded_per_theme = '+already_uploaded_per_theme);
		/* for the ENTRANT role the limitation is global (per all themes in total) */
		if(role=='um_entrant'){
			already_uploaded = 0;

			for (var i = 0; i < Dropzones.length; i++) {
				already_uploaded = already_uploaded + Dropzones[i].getAcceptedFiles().length;
				console.log(i+ 'Already uploaded '+ already_uploaded);
			}
	
			for (var i = 0; i < Dropzones.length; i++) {
				Dropzones[i].options.maxFiles = max_files - already_uploaded + Dropzones[i].getAcceptedFiles().length;
				var test = max_files - already_uploaded + Dropzones[i].getAcceptedFiles().length ;
				console.log(i+'maxFiles = '+test);
				// Dropzones[i].options.maxFiles = 10;
			}
		}else{
			/* for the MEMBER role the limitation is per theme (10 maximum) */	
			for (var i = 0; i < Dropzones.length; i++) {
				Dropzones[i].options.maxFiles = max_files[i];
				
			}
		}

		
	}

	function get_all_files(){
		all_files = 0;
		for (var i = 0; i < Dropzones.length; i++) {
			all_files = all_files + Dropzones[i].getAcceptedFiles().length;
		}
		return all_files;
	}

	var permalink = window.location.href;
	if(permalink.indexOf("#pastuploads") !== -1){
		// console.log('goto');
		jQuery([document.documentElement, document.body]).animate({
			scrollTop: jQuery("#pastuploads").offset().top-200
		}, 1000);
	}

	var all_images=0,dz_nb=0;

	var Dropzones = [];
	var imgs = {};  // new array
	var server_response = {};

	var all_success = 0;

	var total_images = [];

	var date_time = $(".upload-form").attr("date-time");
	var user_login = $("#user_login").val();
	var user_fname_lname = $("#user_fname_lname").val();
	var user_email = $("#user_email").val();
	var user_role = $("#user_role").val();
	var user_id = $("#user_id").val();

	if(user_role=='um_entrant'){
		var max_files = $("#img_to_submit").val();
	}
	else{
		var max_files = [];
	}

	$(".lf-img-submitted-dismiss").on('click', function(){
		$(this).closest('.lf-img-submitted').remove();
	});


/* ENTRANT DZ */
	$( ".lf-upload-form" ).each(function( index ) {
		max_files[index] =  $(this).attr("data-max_per_theme");
		var theme_id = $(this).attr("data-box");
		var entry_item = $(this).attr("data-entry_item");
		var current_theme = $(this).attr("data-current_theme");
		var current_entry_theme = $(this).attr("data-current_entry_theme");

		var currentForm = this;

		total_images[current_theme]=0;


		// console.log(theme_id);
		var x = new Dropzone("#"+$(currentForm).attr("id"), {

				url: "/?submit_upload=true&theme_id="+theme_id+
				"&date_time="+date_time+
				'&user_login='+user_login+
				'&user_fname_lname='+user_fname_lname+
				'&user_email='+user_email+
				'&user_role='+user_role+
				'&user_id='+user_id+
				'&current_theme='+current_theme+
				'&current_entry_theme='+current_entry_theme+
				'&submission_type='+$(".dz-submit-images").data('submission_type')+
				'&index='+(index+1)+
				'&entry_item='+entry_item,
				addRemoveLinks: true,
				paramName: "img-submit",
				maxFilesize : 8,
				resizeWidth : max_uploaded_width,
				resizeHeight : max_uploaded_height,
				resizeQuality : max_uploaded_quality,
				acceptedFiles : "image/jpeg",
				thumbnailWidth : 110,
				thumbnailHeight : 110,
				thumbnailMethod: 'contain',
				uploadMultiple: false,
				parallelUploads:1,
				autoProcessQueue: false,
				timeout:30000000,
				headers: {
					"Cache-Control": "",
					"X-Requested-With": ""
				  },
				dictFileTooBig: "File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB.",
				method: 'POST',

				init: function() {


					var submitButton = document.querySelector(".dz-submit-images");
					var dz_error_mark = document.querySelector(".dz-error-mark");
					var dz = this;

					submitButton.addEventListener("click", function() {
						var entry_desc = '';

						$('textarea[name^=submit-images-description]').map(function(idx, elem) {
							if($(elem).val()!=''){
								entry_desc += $(elem).data('entry_desc')+": ";
								entry_desc += $(elem).val().replace(/(?:\r\n|\r|\n)/g, '<br>');
								entry_desc += "<br>";
						}}).get();
						console.log(entry_desc);
						// return false;

						dz.options.url = "/?submit_upload=true&theme_id="+theme_id+
										"&date_time="+date_time+
										'&user_login='+user_login+
										'&user_fname_lname='+user_fname_lname+
										'&user_email='+user_email+
										'&user_role='+user_role+
										'&user_id='+user_id+
										'&current_theme='+current_theme+
										'&current_entry_theme='+current_entry_theme+
										'&entry_item='+entry_item+
										'&submission_type='+$(".dz-submit-images").data('submission_type')+
										'&index='+(index+1)+
										'&all_images='+get_all_files();
										
						dz.options.params = {'entry_desc': entry_desc},										

						$('.extra-images-error').remove();

						var too_many_img = $('.dz-error-message span').filter(function() {
						    return $(this).text() == 'You can not upload any more files.';
						}).length;

						console.log('Too many images = ' + too_many_img);

						var too_big_img = $('.dz-error-message span').filter(function() {
						    return $(this).text().indexOf('File is too big') !== -1;
						}).length;
						console.log('Too big images = ' + too_big_img);


						/* All rejected files */
						var rejected_files = $('.dz-error-mark').filter(function() {
						    return $(this).css('opacity') == '1';
						}).length;
						console.log('rejected files = '+ rejected_files);



						if(rejected_files>0){
							if(too_many_img>0){
								$('.dz-submit-images').after('<p class="extra-images-error">You are trying to submit too many images. Please remove the extra images.');
							}
							if(too_big_img>0){
								$('.dz-submit-images').after('<p class="extra-images-error">You are trying to upload one or more images that are above the 8MB limit. Please remove them to upload your submission.')

							}

							// return false;
						}
						else{

					      	dz.processQueue(); // Tell Dropzone to process all queued files.
							console.log(Dropzones.length);
							dz_nb = dz_nb +1;
							all_images = get_all_files();
							if((dz_nb==Dropzones.length) && (all_images)){

								$(".dz-submit-images").find('span.submit').css('display','none');
								$(".dz-submit-images").find('i.fa-spinner').css('display','inline-block');
								$('.submit-images').after('<p class="do-not-close-window">Please do not close the window while your images are being submitted!</p>');
								$(".submit-images").css({
									'pointer-events':'none',
									'opacity':'0.7',
								});
							}
						}
				    });



					this.on("success", function(file, serverFileName) {

						dz.processQueue();

						server_response = $.parseJSON(serverFileName);

						console.log(server_response);

						var success_uploaded = server_response.success_uploaded;
						console.log('All success= '+ success_uploaded);
						console.log('All Images= '+all_images);

						if(success_uploaded == all_images){
							/* All images were uploaded => redirect user */
							var permalink = window.location.href;
							if(permalink.indexOf("?success=true#pastuploads") !== -1){
								permalink = permalink.split('#')[0];
								location.reload();
							}
							else{
								window.location.href = (permalink.split('?')[0]+'?success=true#pastuploads');
							}
						}
						

					});

					this.on("addedfile", function(file, serverFileName) {
						reCalculate(user_role);
						var uuid = file.upload.uuid;
			            imgs[uuid]=serverFileName;
					});

					this.on("removedfile", function(file, serverFileName) {
					  reCalculate(user_role);
					  // console.log(file);
					  var uuid = file.upload.uuid;
					  delete imgs[uuid];
					});

					this.on("complete", function (file) {
				      if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
				        // console.log('all files were uploaded');
				      }
					});


					this.on("thumbnail", function (file, dataUrl) {
					  var scaledImage =  loadImage(
						file,
						function (img) {
							//   $('body .dz-image img ').last().html(img);
							var get_thumb = $('body .dz-image img[src="'+dataUrl+'"]');
							if(get_thumb.length){
								get_thumb.replaceWith(img);
							}
							else{
								var get_thumb_by_name = $('body .dz-image img[alt="'+file.name+'"]');
								get_thumb_by_name.replaceWith(img);
							}
							// document.body.appendChild(img)
						},
						{
							maxWidth: 110,
							maxHeight: 110,
							canvas: true,
							orientation: true /* !!! Super important - this option honours the exif data orientation!!! */
						 } // Options
					  );

					});

					/* prevend adding the same image to a series */
					this.on("addedfile", function(file) {

						if (this.files.length) {
							/* Prevent adding the same file again in a dialog */
							var _i, _len;
							for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) // -1 to exclude current file
							{

								if(this.files[_i].name === file.name && this.files[_i].size === file.size )
								{
									this.removeFile(file);
								}
							}
							/* Prevent adding not permitted files in a dialog */
							var file_type = this.files[this.files.length-1].type;
							file_type = file_type.indexOf('image/jpeg');
							if(file_type === -1){
								this.removeFile(file);
							}
						}
					});
				}
		});

		jQuery(currentForm).sortable({
			items:'.dz-preview',
			cursor: 'move',
			opacity: 0.5,
			containment: jQuery(currentForm),
			distance: 20,
			tolerance: 'pointer',
			stop: function () {
				window.currentDZ = x;
				var queue = x.getAcceptedFiles();
				var newQueue = [];
				$('.dz-preview .dz-filename [data-dz-name]', currentForm).each(function (count, el) {
					var name = el.innerHTML;

					newQueue.push(queue.find(function(item) {
						return name == item.name;
					}));
				});
				x.files = newQueue;
			}
		});


		Dropzones.push(x);

	});


});



