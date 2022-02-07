/* global FusionEvents */
var FusionPageBuilder = FusionPageBuilder || {};
FusionPageBuilder.options = FusionPageBuilder.options || {};

FusionPageBuilder.options.fusionImageFocusPoint = {
	optionFocusImage: function( el ) {
		var points = el.find( '.fusion-image-focus-point' );
		var model = this.model;

		points.each( function() {
			var point 	= jQuery( this ).find( '.point' );
			var field 	= jQuery( this ).find( 'input.fusion-builder-focus-point-field' );
			var preview = jQuery( this ).find( '.preview' );
			var previewImg = preview.find( '.image' );
			var placeHolder = jQuery( this ).find( '.placeholder' );
			var paramName	= previewImg.data( 'image' );
			var image 	= el.find( `[data-option-id="${paramName}"]` ).find( '.fusion-builder-upload-preview img' );
			var imageValue = model.attributes.params[ paramName ];

			if ( imageValue ) {
				placeHolder.hide();
				preview.show();
				previewImg.append( image.clone() );
			} else {
				preview.hide();
				placeHolder.show();
			}
			FusionEvents.on( 'awb-image-upload-url-' + paramName, function( url ) {
				if ( url ) {
					image 	= '<img src="' + url + '" alt="">';
					previewImg.find( 'img' ).remove();
					previewImg.append( image );
					preview.show();
					placeHolder.hide();
				} else {
					previewImg.find( 'img' ).remove();
					preview.hide();
					placeHolder.show();
				}
			} );

			point.draggable( {
				containment: 'parent',
				scroll: false,
				drag: function () {
					var top = parseInt( 100 * parseFloat( jQuery( this ).css( 'top' ) ) / parseFloat( jQuery( this ).parent().css( 'height' ) ) );
					var left = parseInt( 100 * parseFloat( jQuery( this ).css( 'left' ) ) / parseFloat( jQuery( this ).parent().css( 'width' ) ) );
					field.val( `${left}% ${top}%` ).trigger( 'change' );

				},
				stop: function () {
					var top = parseInt( 100 * parseFloat( jQuery( this ).css( 'top' ) ) / parseFloat( jQuery( this ).parent().css( 'height' ) ) );
					var left = parseInt( 100 * parseFloat( jQuery( this ).css( 'left' ) ) / parseFloat( jQuery( this ).parent().css( 'width' ) ) );
					field.val( `${left}% ${top}%` ).trigger( 'change' );
				}
			} );

			const $defaultReset = point.closest( '.fusion-builder-option' ).find( '.fusion-builder-default-reset' );

			// Default reset icon, set value to empty.
			$defaultReset.on( 'click', function( event ) {
				var dataDefault,
					top = '50%',
					left = '50%';

				event.preventDefault();
				dataDefault = jQuery( this ).find( '.fusion-range-default' ).attr( 'data-default' ) || '';

				if ( dataDefault && 'string' === typeof dataDefault ) {
					top = dataDefault.split( ' ' )[ 1 ];
					left = dataDefault.split( ' ' )[ 0 ];
				}
				point.css( {
					top,
					left
				} );
				field.val( dataDefault ).trigger( 'change' );

			} );

		} );


	}
};
