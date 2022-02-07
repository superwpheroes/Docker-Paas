<style type="text/css">
.cmb2-options-page.wrap.um_gallery_pro_settings > h2 {
	margin-bottom: 0px;
}
</style>
<?php
$this->gallery_admin_head();
if ( 'license' == $this->active_tab ) {
	echo '<form method="post" action="options.php">';
	$this->license_fields();
	submit_button( __( 'Update License', 'um-gallery-pro' ), 'primary', 'submit', true );
	echo '</form>';
} elseif ( 'addons' == $this->active_tab ) {
	$this->addons_tab();
} elseif ( 'advanced' == $this->active_tab ) {
	$this->tools_tab();
} elseif ( 'labels' == $this->active_tab ) {
	cmb2_metabox_form( $this->metabox_id . '-labels', $this->key );
} elseif ( 'layout' == $this->active_tab ) {
	?>
	<div class="wp-clearfix">
		<ul class="subsubsub">
			<li><a class="<?php echo ( 'profile' == $this->active_section ? 'current' : '' ); ?>" href="<?php echo admin_url( 'admin.php?page=' . $this->setting_key . '&tab=' . $this->active_tab . '&section=profile' ); ?>"><?php echo __( 'Profile', 'um-gallery-pro' ); ?></a> | </li>
			<li><a class="<?php echo ( 'main_tab' == $this->active_section ? 'current' : '' ); ?>" href="<?php echo admin_url( 'admin.php?page=' . $this->setting_key . '&tab=' . $this->active_tab . '&section=main_tab' ); ?>"><?php echo __( 'Main Tab', 'um-gallery-pro' ); ?></a> | </li>
		</ul>
	</div>
	<?php
	if ( 'profile' == $this->active_section ) {
		cmb2_metabox_form( $this->metabox_id . '-layout', $this->key );
	} else {
		cmb2_metabox_form( $this->metabox_id . '-layout-main', $this->key );
	}
} else {
	cmb2_metabox_form( $this->metabox_id, $this->key );
}
?>
</div>
