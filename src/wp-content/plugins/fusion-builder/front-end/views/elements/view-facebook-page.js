/* global fusionAllElements, FusionApp */
var FusionPageBuilder = FusionPageBuilder || {};

( function() {

	jQuery( document ).ready( function() {

		// Alert Element View.
		FusionPageBuilder.fusion_facebook_page = FusionPageBuilder.ElementView.extend( {

			/**
			 * Modify template attributes.
			 *
			 * @since 2.0
			 * @param {Object} atts - The attributes object.
			 * @return {Object}
			 */
			filterTemplateAtts: function( atts ) {
				var attributes = {};


				// Create attribute objects
				attributes.atts   = this.buildAttr( atts.values );

				// Any extras that need passed on.
				attributes.cid    = this.model.get( 'cid' );
				attributes.values = atts.values;
				attributes.styles  = this.buildStyles( atts );


				return attributes;
			},

			/**
			 * Builds attributes.
			 *
			 * @since 2.0
			 * @param {Object} values - The values object.
			 * @return {Object}
			 */
			buildAttr: function( values ) {
				var attr = {};

				if ( '' !== values.id ) {
					attr.id = values.id;
				}

				attr[ 'class' ] = 'fusion-facebook-page fb-page fusion-facebook-page-' + this.model.get( 'cid' ) + ' ' + values[ 'class' ];

				attr  = _.fusionVisibilityAtts( values.hide_on_mobile, attr );
				if ( '' !== values.href ) {
					attr[ 'data-href' ] = values.href;
				}
				if ( '' !== values.tabs ) {
					attr[ 'data-tabs' ] = values.tabs;
				}

				if ( '' !== values.width ) {
					attr[ 'data-width' ] = values.width;
				}

				if ( '' !== values.height ) {
					attr[ 'data-height' ] = values.height;
				}

				if ( 'small' === values.header ) {
					attr[ 'data-small_header' ] = 'true';
				}

				if ( 'hide' === values.cover ) {
					attr[ 'data-hide_cover' ] = 'true';
				}

				if ( 'hide' === values.cta ) {
					attr[ 'data-hide_cta' ] = 'true';
				}

				if ( 'on' === values.lazy ) {
					attr[ 'data-lazy' ] = 'true';
				}

				if ( 'hide' === values.facepile ) {
					attr[ 'data-show_facepile' ] = 'false';
				}

				//Animation
				attr = _.fusionAnimations( values, attr );

				return attr;
			},

			/**
			 * Builds margin styles.
			 *
			 * @since 3.5
			 * @param {Object} atts - The atts object.
			 * @return {string}
			 */
			buildMarginStyles: function( atts ) {
				var extras = jQuery.extend( true, {}, fusionAllElements.fusion_imageframe.extras ),
					elementSelector = '.fusion-facebook-page-' + this.model.get( 'cid' ),
					responsiveStyles = '';

				_.each( [ 'large', 'medium', 'small' ], function( size ) {
					var marginStyles = '',
						marginKey;

					_.each( [ 'top', 'right', 'bottom', 'left' ], function( direction ) {

						// Margin.
						marginKey = 'margin_' + direction + ( 'large' === size ? '' : '_' + size );
						if ( '' !== atts.values[ marginKey ] ) {
							marginStyles += 'margin-' + direction + ' : ' + _.fusionGetValueWithUnit( atts.values[ marginKey ] ) + ';';
						}

					} );

					if ( '' === marginStyles ) {
						return;
					}

					// Wrap CSS selectors
					if ( '' !== marginStyles ) {
						marginStyles = elementSelector + ' {' + marginStyles + '}';
					}

					// Large styles, no wrapping needed.
					if ( 'large' === size ) {
						responsiveStyles += marginStyles;
					} else {
						// Medium and Small size screen styles.
						responsiveStyles += '@media only screen and (max-width:' + extras[ 'visibility_' + size ] + 'px) {' + marginStyles + '}';
					}
				} );


				return responsiveStyles;
			},

			/**
			 * Builds styles.
			 *
			 * @since 3.5
			 * @param {Object} atts - The atts object
			 * @return {string}
			 */
			buildStyles: function( atts ) {
					var selectors;
					var values = atts.values;
					var style;
					this.dynamic_css = {};
					this.baseSelector = '.fusion-facebook-page-' + this.model.get( 'cid' );

					selectors = [ this.baseSelector ];

					if ( '' !==  values.alignment ) {
						this.addCssProperty( selectors, 'display',  'flex' );
						this.addCssProperty( selectors, 'justify-content',  values.alignment );
					}

					style = this.parseCSS();
					style += this.buildMarginStyles( atts );

					return style ? '<style>' + style + '</style>' : '';
			},

			/**
			 * Triggers a refresh.
			 *
			 * @since 2.0.0
			 * @return void
			 */
			refreshJs: function() {
				if ( 'undefined' !== typeof FusionApp.previewWindow.FB ) {
					FusionApp.previewWindow.FB.XFBML.parse();
				}
			},

			onInit: function() {
				this._refreshJs();
			},
			onRender: function() {
				this._refreshJs();
			},
			afterPatch: function() {
				this._refreshJs();
			}
		} );
	} );
}( jQuery ) );
