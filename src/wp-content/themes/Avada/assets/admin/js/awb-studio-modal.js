/* global Fuse */
window.awbStudioModal = {
	data: {},
	$option: false,
	timeouts: [],
	context: {
		type: 'fusion_template',
		tag: 'all'
	},

	/**
	 * Run actions on load.
	 *
	 * @since 7.5
	 *
	 * @return {void}
	 */
	init: function( $el, data ) {
		this.data = data;
		this.$el  = $el;

		return this;
	},

	/**
	 * Modal events.
	 *
	 * @since 7.5
	 * @return {void}
	 */
	initModalEvents: function() {
		var self = this;

		this.$el.find( '.fusion-studio-preview-back, .post-modal-bg' ).on( 'click', function() {
			self.closeModal();
		} );

		jQuery( 'body' ).on( 'keydown', function( event ) {
			if ( ( 27 === event.keyCode || '27' === event.keyCode ) && jQuery( 'body' ).hasClass( 'fusion-studio-preview-active' ) ) {
				self.closeModal( self.$el );
			}
			return true;
		} );
	},

	/**
	 * Closes Modal.
	 *
	 * @since 7.5
	 * @return {void}
	 */
	closeModal: function() {
		this.$el.find( '.awb-studio-modal' ).css( 'visibility', 'hidden' );
		this.$el.find( '.awb-studio-modal' ).css( 'opacity',  '0' );
		jQuery( 'body' ).removeClass( 'fusion-studio-preview-active' );

		jQuery( '#search-input' ).off();
	},

	/**
	 * Opens Modal.
	 *
	 * @since 7.5
	 * @return {void}
	 */
	openModal: function( elementType, $option ) {
		this.context = {
			type: elementType,
			tag: 'all'
		};
		this.$option = $option;
		this.tagsUpdate();
		this.previewsUpdate();

		jQuery( 'body' ).addClass( 'fusion-studio-preview-active' );
		this.$el.find( '.awb-studio-modal' ).attr( 'data-context', 'selection' ).css( 'visibility', 'visible' ).animate( { opacity: 1 }, 250 );

		// Modal close.
		this.initModalEvents();
		this.initIframeListener();
		this.initSearch();
	},

	/**
	 * Init Search panel.
	 *
	 * @since 7.5
	 * @return {void}
	 */
	initSearch: function() {
		var self = this,
			previewEl = jQuery( '.previews' ),
			options,
			fuse,
			result,
			value;

		jQuery( '#search-input' ).on( 'change paste keyup search', _.debounce( function() {
			var thisEl = jQuery( this ),
				data,
				hasValue = false;

			// Hide all.
			jQuery( 'article', previewEl ).css( { display: 'none' } ).addClass( 'hidden' );

			if ( thisEl.val() ) {
				value = thisEl.val().toLowerCase();

				options = {
					threshold: 0.2,
					location: 0,
					distance: 100,
					maxPatternLength: 32,
					minMatchCharLength: 3,
					keys: [ 'post_title' ]
				};

				data = _.map( self.data[ self.context.type ], function( post ) {
					return post;
				} );

				fuse = new Fuse( data, options );
				result = fuse.search( value );
				hasValue = true;
			} else {
				result = self.data[ self.context.type ];
			}

			// Show items.
			_.each( result, function( post ) {
				// Post is not within active tag then no need to show it.
				if ( ! hasValue && 'all' !== self.context.tag && -1 === jQuery.inArray( self.context.tag, post.tags ) ) {
					return;
				}
				previewEl.find( 'article[data-id="' + post.ID + '"]' ).css( { display: 'inline-block' } ).removeClass( 'hidden' );
			} );
		}, 100 ) );
	},

	/**
	 * Iframe events.
	 *
	 * @since 7.5
	 * @return {void}
	 */
	initIframeListener: function() {
		this.$el.find( '.awb-studio-preview-frame' ).on( 'load', function() {
			jQuery( '#fusion-loader' ).hide();
		} );
	},

	/**
	 * Update the tag list for the current context.
	 *
	 * @since 3.1
	 * @return {void}
	 */
	tagsUpdate: function() {
		var $nav = this.$el.find( '#filter-bar nav' );

		// Clear out old tags.
		$nav.empty();

		// Add all link with new count.
		$nav.prepend( '<a href="#" class="active" data-tag="all">All Categories <span>' + Object.keys( this.data[ this.context.type ] ).length + '</span></a>' );

		// Each tag of type add in.
		jQuery.each( this.data.studio_tags[ this.context.type ], function( index, tag ) {
			$nav.append( '<a href="#" data-tag="' + tag.slug + '">' + tag.name + '<span>' + tag.count + '</span></a>' );
		} );

		// Add click listener.
		this.initTagFilter();
	},

	/**
	 * Click listener for tag links.
	 *
	 * @since 3.1
	 * @return {void}
	 */
	initTagFilter: function() {
		var self = this;

		this.$el.find( '#filter-bar nav a' ).on( 'click', function( event ) {
			event.preventDefault();

			if ( self.context.tag === jQuery( this ).data( 'tag' ) ) {
				return;
			}

			// Active styling.
			jQuery( '#filter-bar nav .active' ).removeClass( 'active' );
			jQuery( this ).addClass( 'active' );

			// Update context tag.
			self.context.tag = jQuery( this ).data( 'tag' );

			// Update preview for new tag.
			self.previewsUpdate();
		} );
	},

	/**
	 * Display studio content directly.
	 *
	 * @since 7.5
	 * @return {void}
	 */
	renderContents: function( elementType, tag, container ) {
		var self     = this,
			counter  = 1,
			postType = 'fusion_tb_section';

		this.context = {
			type: elementType,
			tag: tag
		};

		// Clear all timeouts to prevent animations still running.
		jQuery.each( this.timeouts, function( index, value ) {
			clearTimeout( value );
		} );

		// Hide all.
		jQuery( '.awb-wizard-content .awb-db-preview-grid .awb-db-preview' ).css( { display: 'none' } ).addClass( 'hidden' );

		// Post type for rest endpoint.
		if ( 'elements' === self.context.type || 'sections' === self.context.type || 'columns' === self.context.type || 'post_cards' === self.context.type ) {
			postType = 'fusion_element';
		} else if ( 'fusion_template' === self.context.type ) {
			postType = 'fusion_template';
		} else if ( 'icons' === self.context.type ) {
			postType = 'fusion_icons';
		}
		console.log( self.context.tag, self.context.type, self.data );
		// Get data of posts we need.
		jQuery.each( self.data[ self.context.type ], function( key, post ) {

			// Post is not within active tag then no need to show it.
			if ( 'all' !== self.context.tag && -1 === jQuery.inArray( self.context.tag, post.tags ) ) {
				return;
			}

			// We already have preview loaded, show it.  TODO, avoid searching DOM.
			if ( jQuery( '.awb-wizard-content .awb-db-preview[data-id="' + post.ID + '"]' ).length ) {
				jQuery( '.awb-wizard-content .awb-db-preview[data-id="' + post.ID + '"]' ).css( { display: 'inline-block' } );
			} else {

				// We need to create markup for preview.
				jQuery( '.awb-wizard-content .awb-db-preview-grid' ).append( '<article class="awb-db-preview hidden" data-type="' + postType + '" data-id="' + post.ID + '"><div class="preview lazy-load"><img src="data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20width%3D%27' + post.thumbnail.width + '%27%20height%3D%27' + post.thumbnail.height + '%27%20viewBox%3D%270%200%20' + post.thumbnail.width + '%20' + post.thumbnail.height + '%27%3E%3Crect%20width%3D%27' + post.thumbnail.width + '>%27%20height%3D%273' + post.thumbnail.height + '%27%20fill-opacity%3D%220%22%2F%3E%3C%2Fsvg%3E" alt="" width="' + post.thumbnail.width + '" height="' + post.thumbnail.height + '" data-src="' + post.thumbnail.url + '" data-alt="' + post.post_title + '"/></div><div class="bar"><input type="checkbox" class="awb-page-template" name="awb-page-template-' + post.post_title + '" value="" /><span class="fusion_module_title">' + post.post_title + '</span><span class="awb-studio-actions"><a href="#" data-url="' + post.url + '" class="awb-preview"><i class="fusiona-search"></i></a></span></div></article>' );
			}

			self.timeouts.push(
				setTimeout( function() {
					jQuery( '.awb-wizard-content .awb-db-preview[data-id="' + post.ID + '"]' ).removeClass( 'hidden' );
				}, counter * 50 )
			);
			counter++;
		} );

		// Reinit click listeners.
		this.initPreviewListener();

		// Lazy load of any new images.
		this.initLazyLoad( container );
	},

	/**
	 * Update the preview area.
	 *
	 * @since 3.1
	 * @return {void}
	 */
	previewsUpdate: function() {
		var self     = this,
			counter  = 1,
			postType = 'fusion_tb_section';

		// Clear all timeouts to prevent animations still running.
		jQuery.each( this.timeouts, function( index, value ) {
			clearTimeout( value );
		} );

		// Hide all.
		jQuery( '.previews article' ).css( { display: 'none' } ).addClass( 'hidden' );

		// Post type for rest endpoint.
		if ( 'elements' === self.context.type || 'sections' === self.context.type || 'columns' === self.context.type || 'post_cards' === self.context.type ) {
			postType = 'fusion_element';
		} else if ( 'fusion_template' === self.context.type ) {
			postType = 'fusion_template';
		} else if ( 'icons' === self.context.type ) {
			postType = 'fusion_icons';
		}

		// Get data of posts we need.
		jQuery.each( self.data[ self.context.type ], function( key, post ) {

			// Post is not within active tag then no need to show it.
			if ( 'all' !== self.context.tag && -1 === jQuery.inArray( self.context.tag, post.tags ) ) {
				return;
			}

			// We already have preview loaded, show it.  TODO, avoid searching DOM.
			if ( jQuery( 'article[data-id="' + post.ID + '"]' ).length ) {
				jQuery( 'article[data-id="' + post.ID + '"]' ).css( { display: 'inline-block' } );
			} else {

				// We need to create markup for preview.
				jQuery( '.previews' ).append( '<article class="hidden" data-type="' + postType + '" data-id="' + post.ID + '"><div class="preview lazy-load"><img src="data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20width%3D%27' + post.thumbnail.width + '%27%20height%3D%27' + post.thumbnail.height + '%27%20viewBox%3D%270%200%20' + post.thumbnail.width + '%20' + post.thumbnail.height + '%27%3E%3Crect%20width%3D%27' + post.thumbnail.width + '>%27%20height%3D%273' + post.thumbnail.height + '%27%20fill-opacity%3D%220%22%2F%3E%3C%2Fsvg%3E" alt="" width="' + post.thumbnail.width + '" height="' + post.thumbnail.height + '" data-src="' + post.thumbnail.url + '" data-alt="' + post.post_title + '"/></div><div class="bar"><span class="fusion_module_title">' + post.post_title + '</span><span class="awb-studio-actions"><a href="#" data-url="' + post.url + '" class="awb-preview"><i class="fusiona-search"></i></a><a href="#" data-id="' + post.ID + '" class="awb-save"><i class="fusiona-drive"></i></a></span></div></article>' );
			}

			self.timeouts.push(
				setTimeout( function() {
					jQuery( 'article[data-id="' + post.ID + '"]' ).removeClass( 'hidden' );
				}, counter * 50 )
			);
			counter++;
		} );

		// Reinit click listeners.
		this.initPreviewListener();

		// Lazy load of any new images.
		this.initLazyLoad();
	},

	/**
	 * Lazy load images.
	 *
	 * @since 3.1
	 * @return {void}
	 */
	initLazyLoad: function( container ) {
		var imageObserver,
			container  = container || '#main-content', // eslint-disable-line no-redeclare
			$container = this.$el.find( container ),
			demoImages = $container.find( '.lazy-load' ),
			options    = {
				root: $container[ 0 ],
				rootMargin: '0px',
				threshold: 0
			};

		// TODO, make this more efficient when re-init.
		if ( 'IntersectionObserver' in window ) {
			imageObserver = new IntersectionObserver( function( entries ) {
				jQuery.each( entries, function( key, entry ) {
					var demo  = jQuery( entry.target ),
						image = demo.find( 'img' );

					if ( entry.isIntersecting && 'string' === typeof image.data( 'src' ) && '' !== image.data( 'src' ) ) {
						image.attr( 'src', image.data( 'src' ) );

						// Wait 500ms as estimation for the image to load.
						setTimeout( function() {
							demo.removeClass( 'lazy-load' ).addClass( 'lazy-loaded' );
							image.attr( 'alt', image.data( 'alt' ) );
						}, 500 );
						imageObserver.unobserve( entry.target );
					}
				} );
			}, options );

			demoImages.each( function() {
				imageObserver.observe( this );
			} );
		}
	},

	/**
	 * Click listener for opening previews.
	 *
	 * @since 3.1
	 * @return {void}
	 */
	initPreviewListener: function() {
		var self = this;

		// Remove any existing.
		self.$el.find( '.awb-preview, .awb-save' ).off( 'click' );

		// Studio content import.
		self.$el.find( '.awb-save' ).on( 'click', function( event ) {
			var $button  = jQuery( this ),
				$article = $button.closest( 'article' ),
				dataID   = $article.data( 'id' );

			event.preventDefault();

			self.$option.find( 'input[name="' + self.context.type + '"]' ).val( dataID );
			self.$option.find( '.preview' ).html( '<strong>' + $article.find( '.fusion_module_title' ).html() + '</strong>' + $article.find( '.preview' ).html() );

			self.closeModal( self.$el );
		} );

		// Add for each.
		self.$el.find( '.awb-preview' ).on( 'click', function( event ) {

			self.$el.find( '.awb-studio-modal' ).attr( 'data-context', 'preview' );

			event.preventDefault();

			jQuery( '#fusion-loader' ).show();
			self.loadIframePreview( jQuery( this ).attr( 'data-url' ) );
		} );
	},

	loadIframePreview: function( url ) {
		this.$el.find( '.post-preview iframe' ).attr( 'src', url );
	}

};
