<?php
global $wpdb;
$charset_collate = ! empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET $wpdb->charset" : '';
$table_prefix = $wpdb->prefix;

$message        = '';
$gallery_fields = array();
$gallery_table  = $wpdb->get_row( "SELECT id, album_id, user_id, file_name, caption, description, type, status, upload_date FROM {$wpdb->prefix}um_gallery LIMIT 1" );
$bad_database   = $wpdb->last_error;

if ( ! $bad_database ) {
	$result = $wpdb->query( "SHOW TABLES LIKE `" . $wpdb->prefix . "um_gallery_meta`" );
	if ( ! $result ) {
		$bad_database = true;
	}
}

if ( ! $bad_database ) {
	$result = $wpdb->query( "SHOW TABLES LIKE `" . $wpdb->prefix . "um_gallery_favorites`" );
	if ( ! $result ) {
		$bad_database = true;
	}
}

$stats          = $wpdb->get_results( "SELECT id, album_id, user_id FROM {$wpdb->prefix}um_gallery" );
$stats_photos   = count( $stats );
$stats_albums   = wp_list_pluck( $stats, 'album_id' );
$stats_albums   = array_unique( $stats_albums );
$stats_users    = wp_list_pluck( $stats, 'user_id' );
$stats_users    = array_unique( $stats_users );


if ( isset( $_GET['um_gallery'] ) && wp_verify_nonce( $_GET['um_gallery'], 'um_gallery_delete_db' ) ) {

}

// Fix Database with missing columns
if ( isset( $_GET['um_gallery'] ) && wp_verify_nonce( $_GET['um_gallery'], 'um_gallery_db_fix' ) ) {
	//check version and make edits to database
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	$database_scheme = array(
		'id'            => '`id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY',
		'album_id'      => '`album_id` bigint(20) NOT NULL',
		'user_id'       => '`user_id` bigint(20) NOT NULL',
		'file_name'     => '`file_name` varchar(255) NOT NULL',
		'caption'       => '`caption` text NOT NULL',
		'description'   => '`description` text NOT NULL',
		'type'          => '`type` varchar(100) NOT NULL',
		'status'        => '`status` tinyint(2) NOT NULL',
		'upload_date'   => '`upload_date` DATETIME NULL DEFAULT NULL'
	);
	$fixed   = 0;
	foreach( $database_scheme as $field => $string ) {
		$result = $wpdb->query( "SHOW COLUMNS FROM `" . $wpdb->prefix . "um_gallery` LIKE '{$field}'" );
		// if the column doesn't exists then let's add it
		if ( ! $result ) {
			$wpdb->query( 'ALTER TABLE `' . $wpdb->prefix . 'um_gallery` ADD ' . $string . ' NOT NULL AFTER `id`' );
			
			$fixed++;
		}
	}

	$result = $wpdb->query( "SHOW TABLES LIKE `" . $wpdb->prefix . "um_gallery_meta`" );
	// if the table doesn't exists then let's add it.
	if ( ! $result ) {
		$sql4 = "CREATE TABLE IF NOT EXISTS {$table_prefix}um_gallery_meta (
				`id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`object_id` bigint(20) NOT NULL,
				`meta_key` varchar(255) NOT NULL,
				`meta_object` varchar(255) NOT NULL,
				`meta_value` text NOT NULL
			) {$charset_collate};";

		dbDelta( $sql4 );
		$fixed++;
	}

	$result = $wpdb->query( "SHOW TABLES LIKE `" . $wpdb->prefix . "um_gallery_favorites`" );
	// if the table doesn't exists then let's add it.
	if ( ! $result ) {
		$sql5 = "CREATE TABLE IF NOT EXISTS {$table_prefix}um_gallery_favorites (
				`id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`photo_id` bigint(20) NOT NULL,
				`user_id` bigint(20) NOT NULL,
				`favorited_date` DATETIME NULL DEFAULT NULL
			) {$charset_collate};";

		dbDelta( $sql5 );
		$fixed++;
	}

	if ( ! empty( $fixed ) ) {
		$bad_database = false;
		//$message = sprintf( _x( '%d columns fixed', 'Fixed columns', 'um-gallery-pro' ), (int) $fixed );
		$message = esc_html__( 'Fixes have been applied', 'um-gallery-pro' );
	}
}
?>
<?php if ( $message ) { ?>
	<div class="notice notice-success is-dismissible">
		<p><?php echo $message; ?></p>
	</div>
<?php } ?>
<div class="um-gallery--tools-wrapper">
	<div class="um-gallery--stats-wrapper">
		<h3><?php _e('Overview', 'um-gallery-pro' ); ?></h3>
		<div class="um-gallery--stats-col-1"><label><?php _e( 'UM Gallery Pro Version:', 'um-gallery-pro' ); ?></label><?php echo UM_GALLERY_PRO_VERSION; ?></div>
		<div class="um-gallery--stats-col-1"><label><?php _e( 'Albums:', 'um-gallery-pro' ); ?></label><?php echo (int) count( $stats_albums ); ?></div>
		<div class="um-gallery--stats-col-1"><label><?php _e( 'Photos:', 'um-gallery-pro' ); ?></label><?php echo (int) $stats_photos; ?></div>
		<div class="um-gallery--stats-col-1"><label><?php _e( 'Users:', 'um-gallery-pro' ); ?></label><?php echo (int) count( $stats_users ); ?></div>
		<div class="um-gallery--stats-col-1"><label><?php _e( 'Database Ok?:', 'um-gallery-pro' ); ?></label><?php echo ( $bad_database ?  __( 'No ( Click Database Repair )', 'um-gallery-pro' ) : __( 'Yes', 'um-gallery-pro' ) ); ?></div>
	</div>
	<table class="form-table">
		<tr valign="top">
			<th scope="row" valign="top">
				<?php _e( 'Database', 'um-gallery-pro' ); ?>
			</th>
			<td>
				<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=um_gallery_pro_settings&tab=advanced' ), 'um_gallery_db_fix', 'um_gallery' ) ); ?>" class="button button-primary"><?php _e( 'Database Repair', 'um-gallery-pro' ); ?></a>
			</td>
		</tr>
		<!--
		<tr valign="top">
			<th scope="row" valign="top">
				<?php _e( 'Delete Data', 'um-gallery-pro' ); ?>
			</th>
			<td>
				<p class="description"><?php _e( 'Option to delete all albums and images', 'um-gallery-pro' ); ?></p>
			</td>
		</tr>
		-->
	</table>
</div>
