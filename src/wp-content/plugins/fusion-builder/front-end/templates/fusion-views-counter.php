<?php
/**
 * Underscore.js template
 *
 * @package fusion-builder
 * @since 3.5
 */

?>
<script type="text/html" id="tmpl-fusion_views_counter-shortcode">
	<div {{{ _.fusionGetAttributes( wrapperAttributes ) }}}>
		{{{ styleTag }}}
		<div {{{ _.fusionGetAttributes( contentAttributes ) }}}>{{{ FusionPageBuilderApp.renderContent( mainContent, cid, false ) }}}</div>
	</div>
</script>
