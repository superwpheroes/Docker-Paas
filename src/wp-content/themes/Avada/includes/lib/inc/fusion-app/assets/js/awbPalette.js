var awbPalette = awbPalette || {};

/**
 * Initialize the awbPalette object. This object is meant to be as a singleton.
 * In data property holds all the global colors.
 *
 * Usage:
 * 1. Use getColorObject to retrieve an object, and don't forget to verify if the returned value is not null. Ex:
 * color = awbPalette.getColorObject( slug );
 * if ( ! color ) {
 *  color = awbPalette.getDefaultColorObject();
 * }
 *
 * 2. Use functions like addOrUpdateColor(), removeColor() to add/remove global colors.
 *
 * 3. Listen for any changes if a global color changes, via 'awbPalette' event.
 */
( function( $, undef ) {
    awbPalette.data = awbPalette.data || {};
    awbPalette.LiveEditorCSSVars = [];

    // Make wrapper of jQuery Color to parse global color.
    if ( 'function' === typeof jQuery.Color ) {
        jQuery.AWB_Color = function( color ) {
            return jQuery.Color( awbPalette.getRealColor( color ) );
        };
    }

    /**
     * Gets the entire color object. Makes sure that the color object returned
     * have all the properties set.
     *
     * @since 3.6
     * @param {string|null} colorSlug
     */
    awbPalette.getColorObject = function( colorSlug ) {
        var color;

        if ( awbPalette.data[ colorSlug ] ) {
            color = Object.assign( {}, awbPalette.data[ colorSlug ] );
            if ( undefined !== color.color && undefined !== color.label ) {
                return color;
            }
        }

        return null;
    };

    /**
     * Gets a default color object, used to replace data the if the color slug
     * needed does not exist.
     *
     * @since 3.6
     */
    awbPalette.getDefaultColorObject = function() {
        return {
            label: awbPalette.unknownColor || 'Unknown Color',
            color: '#ffffff',
        };
    };

    /**
     * Add or update a color to the global palette. The color data passes is
     * merged with the previous one, if it exists.
     *
     * @since 3.6
     * @param {string} colorSlug Color slug to be replaced or added.
     * @param {Object} colorData This object will be merged with the previous one.
     */
    awbPalette.addOrUpdateColor = function( colorSlug, colorData ) {
        var oldObject;

        awbPalette.data[ colorSlug ] = awbPalette.data[ colorSlug ] || {};
        oldObject = Object.assign( {}, awbPalette.data[ colorSlug ] );

        awbPalette.data[ colorSlug ] = Object.assign( {}, awbPalette.data[ colorSlug ], colorData );
        jQuery( document ).trigger( 'awbPalette', { slug: colorSlug, oldObject: oldObject, context: 'addOrUpdateColor' } );
    };

    /**
     * Remove a color from the global palette object.
     *
     * @since 3.6
     * @param {string} colorSlug
     */
    awbPalette.removeColor = function( colorSlug ) {
        var clonedOldObject;

        awbPalette.data[ colorSlug ] = awbPalette.data[ colorSlug ] || {};
        clonedOldObject = Object.assign( {}, awbPalette.data[ colorSlug ] );

        delete awbPalette.data[ colorSlug ];
        jQuery( document ).trigger( 'awbPalette', { slug: colorSlug, oldObject: clonedOldObject, context: 'removeColor' } );
    };

    awbPalette.getColorSlugFromCssVar = function( colorVar ) {
        var isHsla = /^\s*hsla\s*\(/i.test( colorVar ),
            matches;

		if ( isHsla ) {
            matches = colorVar.match( /var\s*\(\s*--awb-\w+-h\W.*var\s*\(\s*--awb-(\w+)-s\W/ );
			if ( matches[1] ) {
				return matches[1];
			} else {
				return false;
			}
		} else if ( /var\s*\(\s*--awb-(\w+)/.test( colorVar ) ) {
            matches = colorVar.match( /var\s*\(\s*--awb-(\w+)/ );
			if ( matches[1] ) {
				return matches[1];
			} else {
				return false;
			}
		}

        return false;
    };

    awbPalette.getRealColor = function( colorVar ) {
        var globalColorSlug  = awbPalette.getColorSlugFromCssVar( colorVar ),
            liveEditorIframe = document.getElementById( 'fb-preview' ),
            styleObject      = false;

        if ( liveEditorIframe && liveEditorIframe.contentWindow && liveEditorIframe.contentWindow.document ) {
            styleObject = liveEditorIframe.contentWindow;
        }

        if ( ! styleObject ) {
            return colorVar;
        }

        if ( globalColorSlug ) {
            if ( styleObject.document.getElementById( 'awb-hidden-el-color' ) ) {
                var el = styleObject.document.getElementById( 'awb-hidden-el-color' );
            } else {
                var el = styleObject.document.createElement( 'span' );
                el.setAttribute( 'id', 'awb-hidden-el-color' );
                el.style.display = 'none';
                styleObject.document.body.appendChild( el );
            }
            el.style.color = colorVar;
            return styleObject.window.getComputedStyle( el ).getPropertyValue( 'color' );
        }

        return colorVar;
    };
}( jQuery ) );


// Initialize awbPalette global events.
( function( $, undef ) {
    var LiveEditorCSSVars = [];
    var AdminCSSVars = [];

    // When a global palette color changes, also change the live editor global CSS vars.
    jQuery( function() {
        jQuery( document ).on( 'awbPalette', updateLiveEditorVars );
        jQuery( document ).on( 'awbPalette', updateAdminVars );
    } );

    /**
     * Update the live editor body style, with the CSS variables from the global palette.
     *
     * @since 3.6
     */
    function _updateLiveEditorVars() {
        var styleObject = getLiveEditorDocumentStyle();

        if ( ! styleObject ) {
            return;
        }

        removeAllCSSVars( styleObject, LiveEditorCSSVars );
        addAllCSSVars( styleObject, LiveEditorCSSVars );
    };
    var updateLiveEditorVars = _.debounce( _updateLiveEditorVars, 200 );

    /**
     * Update the admin document style, with the CSS variables from the global palette.
     *
     * @since 3.6
     */
    function _updateAdminVars() {
        removeAllCSSVars( document.documentElement.style, AdminCSSVars );
        addAllCSSVars( document.documentElement.style, AdminCSSVars );
    };
    var updateAdminVars = _.debounce( _updateAdminVars, 200 );

    /**
     * Remove all the CSS variables from the live editor body style, that comes from global palette.
     *
     * @since 3.6
     */
    function removeAllCSSVars( styleObject, cssVarsCache ) {
        var needToOverwriteDeletedColor;

        cssVarsCache.forEach( function( cssVar ) {
            styleObject.removeProperty( cssVar.varName );

            // Overwrite with default color(white) if a global color was removed.
            needToOverwriteDeletedColor = ( awbPalette.getColorObject( cssVar.slug ) ? false : true );
            if ( needToOverwriteDeletedColor ) {
                styleObject.setProperty( cssVar.varName, awbPalette.getDefaultColorObject().color );
            }
        } );

        cssVarsCache = [];
    };

    /**
     * Add all the CSS variables that comes from global palette to the live editor body style.
     *
     * @since 3.6
     */
    function addAllCSSVars( styleObject, cssVarsCache ) {
        var colorSlug,
            colorValue,
            hsla;

        for ( colorSlug in awbPalette.data ) {
            colorValue = awbPalette.data[ colorSlug ].color;

            hsla = jQuery.Color( colorValue ).hsla();

            addDocumentCSSVar( colorSlug, '--awb-' + colorSlug, colorValue );

            addDocumentCSSVar( colorSlug, '--awb-' + colorSlug + '-h', hsla[ 0 ] );
            addDocumentCSSVar( colorSlug, '--awb-' + colorSlug + '-s', ( hsla[ 1 ] * 100 ) + '%' );
            addDocumentCSSVar( colorSlug, '--awb-' + colorSlug + '-l', ( hsla[ 2 ] * 100 ) + '%' );
            addDocumentCSSVar( colorSlug, '--awb-' + colorSlug + '-a', ( hsla[ 3 ] * 100 ) + '%' );
        }

        function addDocumentCSSVar( slug, varName, varValue ) {
            styleObject.setProperty( varName, varValue );
            cssVarsCache.push( { varName: varName, slug: slug } );
        }
    };

    function getLiveEditorDocumentStyle() {
        var liveEditorIframe = document.getElementById( 'fb-preview' );
        if ( liveEditorIframe && liveEditorIframe.contentWindow && liveEditorIframe.contentWindow.document ) {
            return liveEditorIframe.contentWindow.document.documentElement.style;
        }

        return null;
    };

}( jQuery ) );
